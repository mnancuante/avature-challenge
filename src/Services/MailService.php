<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService {
    private PHPMailer $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);

        try {

            $this->mailer->isSMTP();
            $this->mailer->Host       = 'sandbox.smtp.mailtrap.io';
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = '3c299f2de1f9b4';
            $this->mailer->Password   = '171e3a92d90a00'; 
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port       = 2525;
            $this->mailer->setFrom('remitente@ejemplo.com', 'Nombre Remitente');
        } catch (Exception $e) {
            error_log("Error al configurar PHPMailer: " . $e->getMessage());
        }
    }

    public function sendMail(string $to, string $subject, string $body): bool {
        try {
            $this->mailer->addAddress($to);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;
            $this->mailer->AltBody = strip_tags($body);

            $this->mailer->send();

            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            return true;
        } catch (Exception $e) {
            error_log("Error al enviar el correo: " . $this->mailer->ErrorInfo);
            return false;
        }
    }
}