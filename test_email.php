<?php
// Test email sending
$to = 'test@example.com'; // Replace with your test email
$subject = 'Test Email';
$message = 'This is a test email from PHP.';
$headers = 'From: yourgmail@gmail.com' . "\r\n" .
           'Reply-To: yourgmail@gmail.com' . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

if (mail($to, $subject, $message, $headers)) {
    echo 'Email sent successfully!';
} else {
    echo 'Email sending failed.';
}
?>