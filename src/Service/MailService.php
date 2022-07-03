<?php

namespace App\Service;

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class MailService
{
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }
    
    public function getHostSmtp(): string
    {
        return $this->parameterBag->get('HOSTSMTP');
    }
    public function getCompteSmtp(): string
    {
        return $this->parameterBag->get('COMPTESMTP');
    }
    public function getPasswordSmtp(): string
    {
        return $this->parameterBag->get('PASSWORDSMTP');
    }

    
    public function sendEmailWithPhpMailer($to, $sujet, $body)
    {

        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = $this->getHostSmtp();                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = $this->getCompteSmtp();                     //SMTP username
            $mail->Password   = $this->getPasswordSmtp();                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom($this->getCompteSmtp(), 'Refaites vos jeux');
            // $mail->addAddress('joe@example.net', 'Joe User');     //Add a recipient
            $mail->addAddress($to);               //Name is optional
            $mail->addReplyTo($this->getCompteSmtp(), 'Information');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');

            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $sujet;
            $mail->Body    = $body;
            // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}