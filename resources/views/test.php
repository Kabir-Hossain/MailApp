<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if(isset($_POST['recipient'])){

    if (isset($_FILES['pdf-file'])){
        // Get file details
        $fileTmpPath = $_FILES['pdf-file']['tmp_name'];
        $fileName = $_FILES['pdf-file']['name'];
        $fileType = $_FILES['pdf-file']['type'];

        // Read the file content as a string
        $fileContent = file_get_contents($fileTmpPath);
    }

//    print_r($_POST);
//    exit;
    $recipient = $_POST['recipient'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $cc = !empty($_POST['cc']) ? explode(',', $_POST['cc']) : null;
    $bcc = !empty($_POST['bcc']) ? explode(',', $_POST['bcc']) : null;


//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'hamko.tesst@gmail.com';                     //SMTP username
    $mail->Password   = '';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // ENCRYPTION_SMTPS - Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('hamko.tesst@gmail.com', 'Hamko Corporation Ltd');
    $mail->addAddress($recipient, 'Kabir');     //Add a recipient

    // $mail->addAddress('ellen@example.com');               //Name is optional
    // $mail->addReplyTo('info@example.com', 'Information');

    
    // Add CC emails if provided
    if (!empty($cc)) {
        foreach ($cc as $ccEmail) {
            $mail->addCC(trim($ccEmail)); // trim to remove extra spaces
        }
    }

    // Add BCC emails if provided
    if (!empty($bcc)) {
        foreach ($bcc as $bccEmail) {
            $mail->addBCC(trim($bccEmail)); // trim to remove extra spaces
        }
    }

    //Attachments
    $mail->addStringAttachment($fileContent, $fileName, 'base64', $fileType);
    // $mail->addAttachment('pdf/pdfReport.pdf');         //Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $subject; //'Mail From HAMKO ICT Accounting Application';
    $mail->Body    = $$message;
    // "
    //                     Dear Recipient's,

    //                     I hope this message finds you well.

    //                     Attached, please find the document you requested. If you have any questions or need further assistance, please don't hesitate to reach out.

    //                     Warm regards,
    //                     [Your Name]
    //                     [Your Position, if applicable]
    //                     [Your Company, if applicable]
    //                     [Your Contact Information, if applicable]";

    // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

}
?>