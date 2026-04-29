<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail($to, $subject, $body)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        
        $mail->Host       = env('MAIL_HOST');
        $mail->Port       = env('MAIL_PORT');
        $mail->Username   = env('MAIL_USER');
        $mail->Password   = env('MAIL_PASS');
        $mail->SMTPSecure = env('MAIL_ENCRYPTION');

        $mail->setFrom(env('MAIL_FROM'), env('MAIL_FROM_NAME'));
        $mail->SMTPAuth   = true;
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}
