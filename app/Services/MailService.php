<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    protected $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        $this->mail->isSMTP();
        $this->mail->Host       = $_ENV['MAIL_HOST'] ?? '127.0.0.1';
        $this->mail->Port       = $_ENV['MAIL_PORT'] ?? 1025;
        $this->mail->SMTPAuth   = false;
        $this->mail->setFrom('system@mjd-core.local', 'MJD_CORE_ENGINE');
    }

    public function send($to, $subject, $body)
    {
        try {
            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;

            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Mail Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }
}