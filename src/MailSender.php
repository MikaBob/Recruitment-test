<?php

namespace Blexr;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

/**
 * From https://github.com/PHPMailer/PHPMailer
 */
class MailSender {

    private $mailer;
    private $error;

    public function __construct() {
        $this->mailer = new PHPMailer(true);

        //Server settings
        $this->mailer->isSMTP(); // Send using SMTP
        $this->mailer->Host = $_ENV['SMTP_HOST']; // Set the SMTP server to send through
        $this->mailer->SMTPAuth = true; // Enable SMTP authentication
        $this->mailer->Username = $_ENV['SMTP_USERNAME'];
        $this->mailer->Password = $_ENV['SMTP_PASSWORD'];
        $this->mailer->Port = $_ENV['SMTP_PORT']; // TCP port to connect to
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable implicit TLS encryption
    }

    public function sendMail($to, $subject, $content): bool {
        try {
            //Recipients
            $this->mailer->setFrom($_ENV['SMTP_EMAIL'], 'Admin');
            $this->mailer->addAddress($to);

            //Attachments
            $this->mailer->isHTML(true); // Set email format to HTML
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $content;

            $this->mailer->send();
        } catch (PHPMailerException $e) {
            $this->error = $this->mailer->ErrorInfo;
            return false;
        }

        return true;
    }

    function getError() {
        return $this->error;
    }

}
