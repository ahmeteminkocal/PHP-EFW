<?php


namespace efwEngine\app;
use efwEngine\config;
use PHPMailer\PHPMailer\PHPMailer;
class mail
{
        private static $sender;
        public static array $receivers;
        public static function sendMail($topic, $content){

            foreach (self::$receivers as $receiver){
                self::mailEngine($receiver, $topic, $content);
            }

        }
        private static function mailEngine($receiver, $topic, $content){
            $mail = new PHPMailer();
            try {
                //Server settings
                $mail->SMTPDebug = config::getMailDebug();                                       // Enable verbose debug output
                $mail->isSMTP();                                            // Set mailer to use SMTP
                $mail->Host       = config::getMailServer();  // Specify main and backup SMTP servers
                $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                $mail->Username   = config::getMailUser();                     // SMTP username
                $mail->Password   = '';                               // SMTP password
                $mail->SMTPSecure = config::getMailType();                                  // Enable TLS encryption, `ssl` also accepted
                $mail->Port       = config::getMailPort();                                    // TCP port to connect to
                $mail->CharSet = 'UTF-8';
                //Recipients
                $mail->setFrom(config::getMailSender(), config::getMailName());
                $mail->addAddress($receiver, '');     // Add a recipient // Name is optional
                $mail->addReplyTo(config::getMailReply(), config::getMailReplyName());
                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = $topic;
                $mail->Body    = $content;
                $mail->AltBody = 'Eğer hatalı gözüküyorsa lütfen farklı bir tarayıcı ile maili görüntüleyin.';
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );

                $mail->send();
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }

}