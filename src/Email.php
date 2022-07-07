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

class Email extends Appinit {

  private static $conf;

  private static function index(){
    self::$conf = parse_ini_file(__DIR__ . '/../../env.ini', true);
  }



  # ----------------------------------
  // Send The Email
  # ----------------------------------
  public static function send($info){

    self::index();

    // Email Parameters
    extract($info);

    // Sender Email Address
    $mailFrom = (!isset($from) || $from == "")
    ? self::$conf['sMTP']['MAIL_FROM_ADDRESS']
    : $from;

    // Set Up Mail Body
    $mailBody = self::setEmailTemplate($template, $data);

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
        $mail->Port       = self::$conf['sMTP']['SMTO_PORT'];        // Port
        $mail->SMTPSecure = self::$conf['sMTP']['SECURE'];           // Secure Connection
      }

      # Recipients
      $mail->setFrom($mailFrom, self::$conf['sMTP']['MAIL_FROM_NAME']);
      $mail->addAddress($to); // Add a recipient
      $mail->addReplyTo($mailFrom, self::$conf['sMTP']['MAIL_FROM_NAME']);

      # BCC
      if(isset($bcc) && !empty($bcc)){
        foreach ($bcc as $k => $v) {
          $mail->addBcc($v, $k);
        }
      }

      # CC
      if(isset($cc) && !empty($bcc)){
        foreach ($cc as $k => $v) {
          $mail->addCC($v, $k);
        }
      }

      # Content
      $mail->isHTML(true);
      $mail->CharSet = "UTF-8";
      $mail->WordWrap = 50;
      $mail->Subject = $subject;
      $mail->Body = $mailBody;
      $mail->AltBody = strip_tags($alt);

      # Embeded Images
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
      }
      else{
        return false;
      }
    } catch (Exception $e) {
      if(DEBUG){
        echo "Mailer Error: {$mail->ErrorInfo}";
      }
      return false;
    }
  }



  # ----------------------------------
  // Set Up HTML Email Template
  # ----------------------------------
  private static function setEmailTemplate($tmp, $data){
    $placeHolders = []; // Placeholders in Template
    $htmlMSG = file_get_contents(__DIR__ .'/../app/email_template/'.$tmp);

    // Set up Template with actual data
    preg_match_all("~\{\{\s*(.*?)\s*\}\}~", $htmlMSG, $placeHolders);
    foreach ($placeHolders[1] as $val) {
      if(array_key_exists($val, $data)){
        $htmlMSG = str_replace("{{ $val }}", $data[$val], $htmlMSG);
      }
      else{
        if(!DEBUG){
          $htmlMSG = str_replace("{{ $val }}", "", $htmlMSG);
        }
      }
    }
    return $htmlMSG;
  }

}