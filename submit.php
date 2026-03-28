<?php
//email setup
use PHPMailer\PHPMailer\PHPMailer;
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stk_orders";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Initialize response
$success = false;
$message = "";

// Connection check
if ($conn->connect_error) {
    $message = "Pri pripojení k databáze nastala chyba: " . $conn->connect_error;
} else {
    // Get POST data
    $meno = $_POST['meno'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefon = $_POST['telefon'] ?? '';
    $datum = $_POST['datum'] ?? '';
    $cas = $_POST['cas'] ?? '';
    $technicka = $_POST['technicka'] ?? '';
    $emisna = $_POST['emisna'] ?? '';
    $originalita = $_POST['originalita'] ?? '';
    $spz = $_POST['spz'] ?? '';
    $palivo = $_POST['palivo'] ?? '';
    $typ_vozidla = $_POST['typ_vozidla'] ?? '';
    $poznamka = $_POST['poznamka'] ?? '';
    $gdpr_consent = isset($_POST['gdpr_consent']) ? 1 : 0;

    // Basic validation
    if (empty($meno) || empty($email) || empty($telefon) || !$gdpr_consent) {
        $message = "Chýbajú povinné údaje alebo súhlas s GDPR.";
    } else {
        // Check if time is already taken
        $stmt_check = $conn->prepare("SELECT COUNT(*) as count FROM orders WHERE datum = ? AND cas = ?");
        $stmt_check->bind_param("ss", $datum, $cas);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $row_check = $result_check->fetch_assoc();
        $stmt_check->close();

        if ($row_check['count'] > 0) {
            $message = "Tento čas je už obsadený. Vyberte iný.";
        } else {
            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO orders (meno, email, telefon, datum, cas, technicka, emisna, originalita, spz, palivo, typ_vozidla, poznamka, gdpr_consent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssssssi", $meno, $email, $telefon, $datum, $cas, $technicka, $emisna, $originalita, $spz, $palivo, $typ_vozidla, $poznamka, $gdpr_consent);

            if ($stmt->execute()) {
                $success = true;
                $message = "Objednávka bola úspešne odoslaná.";

/*
$mail = new PHPMailer(true);

// SMTP config
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'webovestrankybj@gmail.com';
$mail->Password = 'your_app_password'; // NOT your normal password
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

// Email content
$mail->setFrom('webovestrankybj@gmail.com', 'Test');
$mail->addAddress('patriksidorbj@gmail.com');

$mail->Subject = 'Test from localhost';
$mail->Body = 'It works!';

$mail->send();

echo "Email sent!";
*/

                } else {
                $message = "Chyba pri odosielaní objednávky: " . $stmt->error;
            }

            $stmt->close();
        }
    }

    $conn->close();
}

// Output user-friendly result page
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stav objednávky</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #F2F2F2;
            color: #4A4A4A;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }

        .card {
            background: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
            text-align: center;
        }

        .card h1 {
            margin-top: 0;
            color: #1E4E8C;
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 20px;
            border-radius: 10px;
            margin: 20px auto;
            font-weight: bold;
            font-size: 1.1rem;
            max-width: 600px;
        }

        .status.success {
            background: rgba(72, 187, 120, 0.15);
            color: #2F7A4E;
            border: 1px solid rgba(72, 187, 120, 0.4);
        }

        .status.error {
            background: rgba(223, 66, 66, 0.15);
            color: #9B2A2A;
            border: 1px solid rgba(223, 66, 66, 0.4);
        }

        .submit-btn {
            margin-top: 16px;
            padding: 14px 20px;
            background: #1E4E8C;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: 0.2s;
        }

        .submit-btn:hover {
            background: #163a66;
        }

        @media (max-width: 700px) {
            .container {
                margin: 20px auto;
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h1>Stav objednávky</h1>

        <div class="status <?php echo $success ? 'success' : 'error'; ?>">
            <?php echo $success ? '✅' : '❌'; ?>
            <span><?php echo htmlspecialchars($message, ENT_QUOTES); ?></span>
        </div>

        <a class="submit-btn" href="objednavka.html">Späť na stránku objednávania</a>
    </div>
</div>

</body>
</html>
