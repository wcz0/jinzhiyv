<?php

declare(strict_types=1);

namespace App\Utils;

use PHPMailer\PHPMailer\PHPMailer;

class Mail
{
    protected $mail;

    public function __construct()
    {
        $config = config('mail');
        $this->mail = new PHPMailer();
        $this->mail->isSMTP();
        $this->mail->Host       = $config['host'];
        $this->mail->SMTPAuth   = $config['auth'];
        $this->mail->Username   = $config['username'];
        $this->mail->Password   = $config['password'];
        $this->mail->SMTPSecure = $config['secure'];
        $this->mail->Port       = $config['port'];
        $this->mail->setFrom($config['username'], $config['from']);
        $this->mail->isHTML(true);
        $this->mail->CharSet = 'UTF-8';
    }


    public function send($email, $title, $content)
    {
        $this->mail->Subject = $title;
        $this->mail->Body    = $content;
        $this->mail->addAddress($email);
        return $this->mail->send();
    }
}
