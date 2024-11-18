<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailController extends Controller
{
    public function sendEmail(Request $request)
    {
        if(isset($_POST['recipient']) && (isset($_FILES['pdf-file']))){
        
            $validatedData = $request->validate([
                'recipient' => 'required|email',
                'subject' => 'required|string|max:255',
                'message' => 'nullable|string',
                'cc' => 'nullable|string',
                'bcc' => 'nullable|string',
                'pdf-file' => 'nullable|file|mimes:pdf|max:5120',
            ]);

            try {
                // Initialize PHPMailer
                $mail = new PHPMailer(true);

                // $mail->SMTPDebug = 0;
                $mail->isSMTP();
                $mail->Host = env('MAIL_HOST');
                $mail->SMTPAuth = true;
                $mail->Username = env('MAIL_USERNAME');
                $mail->Password = env('MAIL_PASSWORD');
                $mail->SMTPSecure = env('MAIL_ENCRYPTION');
                $mail->Port = env('MAIL_PORT');
        
                $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                $mail->addAddress($request->recipient);

                // Server settings
                // $mail->isSMTP();
                // $mail->Host = 'smtp.gmail.com';
                // $mail->SMTPAuth = true;
                // $mail->Username = 'hamko.tesst@gmail.com';
                // $mail->Password = 'dahz cdud zpap pgwp';
                // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                // $mail->Port = 465;

                // // Set sender and recipient
                // $mail->setFrom('hamko.tesst@gmail.com', 'Hamko Corporation Ltd');
                // $mail->addAddress($validatedData['recipient'], 'Recipient Name');

                // Add CC recipients
                if (!empty($validatedData['cc'])) {
                    $ccEmails = explode(',', $validatedData['cc']);
                    foreach ($ccEmails as $ccEmail) {
                        $mail->addCC(trim($ccEmail));
                    }
                }

                // Add BCC recipients
                if (!empty($validatedData['bcc'])) {
                    $bccEmails = explode(',', $validatedData['bcc']);
                    foreach ($bccEmails as $bccEmail) {
                        $mail->addBCC(trim($bccEmail));
                    }
                }

                // Add attachment if file is uploaded
                if ($request->hasFile('pdf-file')) {
                    $file = $request->file('pdf-file');
                    $mail->addStringAttachment(
                        file_get_contents($file->getRealPath()),
                        $file->getClientOriginalName(),
                        'base64',
                        $file->getMimeType()
                    );
                }

                // Email content
                $mail->isHTML(true);
                $mail->Subject = $validatedData['subject'];
                $mail->Body = $validatedData['message'];

                // Send the email
                $mail->send();

                return response()->json(['message' => 'Email Sent Successfully!.'], 200);
            } catch (Exception $e) {
                return response()->json(['error' => "Mailer Error: {$mail->ErrorInfo}"], 500);
            }
        }
    }
}
