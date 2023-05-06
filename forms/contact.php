<?php

$receiving_email_address = 'mwitacyrus566@gmail.com';

if (file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php')) {
    include($php_email_form);
} else {
    die('Unable to load the "PHP Email Form" Library!');
}

$contact = new PHP_Email_Form;
$contact->ajax = true;

$contact->to = $receiving_email_address;
$contact->from_name = $_POST['name'];
$contact->from_email = $_POST['email'];
$contact->subject = $_POST['subject'];

$contact->add_message($_POST['name'], 'From');
$contact->add_message($_POST['email'], 'Email');
$contact->add_message($_POST['message'], 'Message', 10);

// Set headers
$headers = "From: {$contact->from_name} <{$contact->from_email}>\r\n";
$headers .= "Reply-To: {$contact->from_email}\r\n";
$headers .= "Content-type: text/html\r\n";

// Send the email using the PHP mail() function
if (mail($contact->to, $contact->subject, $contact->message, $headers)) {
    echo 'success';
} else {
    echo 'failed';
}

?>
