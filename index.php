<?php
    session_start();
    $recipient="tuttas@mmbbs.de";
    $upload_folder="/var/tmp/";
    $SMTPHost="smtp.gmail.com";
    $SMTPUser="tuttas68@gmail.com";
    $STMPPassword=file_get_contents("kennwort.txt");

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'phpmailer/Exception.php';
    require 'phpmailer/PHPMailer.php';
    require 'phpmailer/SMTP.php';

    if ($_POST["id"]) {
        if (isset($_SESSION[$_POST["id"]])) {
            if (!$_POST["name"] || !$_POST["klasse"] || !$_POST["email"] || !$_POST["von"]) {
                echo file_get_contents("fehler.html");
            }
            else {
                $answer= file_get_contents("answer.html");
                $msg="Ihre Krankmeldung wurde gesendet an $recipient. Eine Kopie der Krankmeldung wurde geschickt an ".$_POST["email"];                
                if ($_POST["betrieb"]) {
                    $msg=$msg." und an ".$_POST["betrieb"];
                }                
                $answer=str_replace("<!--msg-->",$msg,$answer);
                echo $answer;
                $betreff = "Krankmeldung von ".$_POST["name"]." (Klasse: ".$_POST["klasse"].") vom ".$_POST["von"];
                if ($_POST["bis"]) {
                    $betreff=$betreff." bis ".$_POST["bis"];
                }
                $text="Sehr geehrte Damen und Herren,\r\n";
                $text = $text."hiermit melde ich (".$_POST["name"]." aus der Klasse ".$_POST["klasse"].") mich krank ";
                if ($_POST["bis"]) {
                    $text=$text." vom ".$_POST["von"]." bis ".$_POST["bis"].".";
                }
                else {
                    $text=$text."für den ".$_POST["von"].".";
                }             
                if ($_POST["comment"]) {
                    $text=$text."\r\n\r\n";
                    $text=$text.$_POST["comment"];
                    $text=$text."\r\n";
                }
                $text=$text."\r\n\r\nMit freundlichen Grüßen\r\n".$_POST["name"];


                $mail = new PHPMailer();
                $mail->IsSMTP();
                $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
                $mail->SMTPAuth = true; // authentication enabled
                $mail->Mailer = "smtp";
                $mail->Host = $SMTPHost;
                $mail->Port = 465;
                // SMTP username (e.g xyz@gmail.com)
                $mail->Username = $SMTPUser;

                // SMTP password
                $mail->Password = $STMPPassword;

                // Enable encryption, 'tls' also accepted
                $mail->SMTPSecure = 'ssl';

                // Sender Email address
                $mail->AddReplyTo($_POST["email"], $_POST["name"]);
                $mail->SetFrom($_POST["email"], $_POST["name"]);
                $mail->CharSet = 'UTF-8';
                // Sender name
                $mail->FromName = $_POST["name"];

                // Receiver Email address
                $mail->addAddress($recipient,"INFO");

                $mail->Subject=$betreff;
                $mail->Body     =  $text;


                // Attaching files in the mail
                if ($_FILES["anlage"]['tmp_name']) {
                    $uploadfile = $upload_folder . basename($_FILES['anlage']['name']);
                    #echo $uploadfile;
                    if (move_uploaded_file($_FILES['anlage']['tmp_name'], $uploadfile)) {
                        #echo "Datei ist valide und wurde erfolgreich hochgeladen.\n";
                        $mail->addAttachment($uploadfile);
                    }
                    else {
                        echo "Move Failed";
                    }
                }

                $mail->WordWrap = 50;

                $mail->AddCC($_POST["email"],$_POST["name"]);
                if ($_POST["betrieb"]) {
                    $mail->AddCC($_POST["betrieb"],$_POST["betrieb"]);                    
                }
                // Sending message and checking status
                if (!$mail->send()) {
                    echo "Sorry!!! Message was not sent. Mailer error: " . $mail->ErrorInfo;
                    exit;
                } else {
                }
                if ($uploadfile) {
                    unlink($uploadfile);
                }
            }
        }
        else {
            echo "Not Allowed";
        }
    }    
    else {
        $welcome = file_get_contents("index.html");
        $id=md5(uniqid(rand(), true));
        $tag='<input type="hidden" name="id" value="'.$id.'">';
        $welcome=str_replace("<!--key-->",$tag,$welcome);
        echo $welcome;    
        $_SESSION[$id]=$id;
    }
?>