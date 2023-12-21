<?php
/**
 * Ozz micro framework
 * Author: Shakir
 * Contact: shakeerwahid@gmail.com
 */

namespace Ozz\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Ozz\Core\Err;

class Mail extends AppInit {

  private static $conf;

  private static function index(){
    self::$conf = env();
  }

  /**
   * Send The Email
   */
  public static function send($info){
    self::index();

    // Email Parameters
    extract($info);

    // Sender Email Address
    $mailFrom = (!isset($from) || $from == "")
    ? self::$conf['sMTP']['MAIL_FROM_ADDRESS']
    : $from;

    // Sender Name
    $mailFromName = (!isset($from_name) || $from_name == "")
    ? self::$conf['sMTP']['MAIL_FROM_NAME']
    : $from_name;

    // Set Up Mail Body
    $mailBody = self::setEmailTemplate($template, $data);

    if(DEBUG && DEBUG_EMAIL){
      echo $mailBody;
    }

    $mail = new PHPMailer(true);
    try {

      # Configure Email Server settings
      if(DEBUG){
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                       // Enable verbose debug output
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;          // Enable TLS encryption;
      }

      if(self::$conf['sMTP']['IS_SMTP'] == 1){
        $mail->isSMTP();                                             // Send using SMTP
        $mail->SMTPAuth   = true;                                    // Enable SMTP authentication
        $mail->Host       = self::$conf['sMTP']['SMTP_HOST'];        // Set the SMTP server to send through
        $mail->Username   = self::$conf['sMTP']['SMTP_USERNAME'];    // SMTP username
        $mail->Password   = self::$conf['sMTP']['SMTP_PASSWORD'];    // SMTP password
        $mail->Port       = self::$conf['sMTP']['SMTP_PORT'];        // Port
        $mail->SMTPSecure = self::$conf['sMTP']['SECURE'];           // Secure Connection
      } else {
        $mail->isMail();
      }

      # Mail from
      $mail->setFrom($mailFrom, $mailFromName);

      # Recipients
      if(is_string($to)){
        $mail->addAddress($to);
      } elseif(is_array($to)) {
        foreach ($to as $email => $name_or_mail) {
          if(is_string($email)){
            $mail->addAddress($email, $name_or_mail);
          } else {
            $mail->addAddress($name_or_mail);
          }
        }
      }

      # Set ReplyTo
      if(isset($reply_to)){
        if(is_array($reply_to)){
          foreach ($reply_to as $name => $email) {
            $mail->addReplyTo($email, $name);
          }
        } else {
          $mail->addReplyTo($reply_to, $mailFromName);
        }
      } else {
        $mail->addReplyTo($mailFrom, $mailFromName);
      }
      

      # BCC
      if(isset($bcc) && !empty($bcc)){
        foreach ($bcc as $k => $v) {
          $mail->addBcc($v, $k);
        }
      }

      # CC
      if(isset($cc) && !empty($cc)){
        foreach ($cc as $k => $v) {
          $mail->addCC($v, $k);
        }
      }

      # Content
      $mail->isHTML(true);
      $mail->CharSet = CONFIG['EMAIL_CHARSET'];
      $mail->WordWrap = isset($word_wrap) ? $word_wrap : 50;
      $mail->Subject = isset($subject) ? $subject : 'New message from '.APP_NAME;
      $mail->Body = $mailBody;
      $mail->AltBody = strip_tags($alt);

      # Embedded Images
      if(isset($img) && !empty($img)){
        foreach ($img as $key => $value) {
          $mail->AddEmbeddedImage($value, $key, $value);
        }
      }

      # Attach Files
      if(isset($files) && !empty($files)){
        foreach ($files as $key => $value) {
          $mail->addAttachment($value);
        }
      }

      # Finally Send the Mail
      if($mail->send()){
        return true;
      } else {
        return false;
      }
    } catch (Exception $e) {
      if(DEBUG && DEBUG_EMAIL){
        echo "Mailer Error: {$mail->ErrorInfo}";
      }
    }

    if(DEBUG && DEBUG_EMAIL){
      dump($mail);
      exit;
    }

    return false;
  }

  /**
   * Set Up HTML Email Template
   */
  private static function setEmailTemplate($tmp, $data){
    $placeHolders = []; // Placeholders in Template
    $tmp = (strpos($tmp, '.') !== false) ? $tmp : $tmp.'.phtml';
    if(file_exists(APP_DIR .'mail/'.$tmp)){
      $htmlMSG = file_get_contents(APP_DIR .'mail/'.$tmp);
    } else {
      return DEBUG
        ? Err::custom([
          'msg' => "Email template [ app/mail/$tmp ] not found",
          'info' => 'Please check your email template name or create one if it is not exist.',
          'note' => "You can create an email template by running this command <br> [ php ozz c:email-temp template_name ]",
        ])
        : false;
    }

    // Set up Template with actual data
    preg_match_all("~\{\{\s*(.*?)\s*\}\}~", $htmlMSG, $placeHolders);
    foreach ($placeHolders[1] as $val) {
      if(array_key_exists($val, $data)){
        $htmlMSG = str_replace("{{ $val }}", $data[$val], $htmlMSG);
      } else {
        if(!DEBUG){
          $htmlMSG = str_replace("{{ $val }}", "", $htmlMSG);
        }
      }
    }
    return $htmlMSG;
  }

}