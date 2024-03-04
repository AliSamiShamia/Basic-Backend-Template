<?php


namespace App\Helper;


use App\Models\UserToken;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\ApiException;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class _EmailHelper
{
    public function __construct()
    {
    }

    public function generateToken($user, $expired_at)
    {
        $check = UserToken::query()->where([
            'user_id' => $user->id,
        ])->where('expired_at', '>', Carbon::now())->first();
        if ($check) {
            return $check->token;
        }
        $userToken = UserToken::query()->create([
            'token' => Str::slug(Hash::make($user->email)),
            'user_id' => $user->id,
            'expired_at' => $expired_at
        ]);
        if ($userToken) {
            return $userToken->token;
        }
        return "";
    }

    public static function getUserPerToken($token)
    {
        $check = UserToken::query()->where([
            'token' => $token,
        ])->where('expired_at', '>', Carbon::now())->first();
        if ($check) {
            return $check->User;
        }
        return null;
    }

    public static function generateTransactionToken($user): string
    {
        $token = Str::uuid()->toString(); // Generates a UUID (Universally Unique Identifier) as a string
        $userToken = UserToken::query()->create([
            'token' => $token,
            'user_id' => $user->id,
            'expired_at' => Carbon::now()->addMonth()
        ]);
        if ($userToken) {
            return $token;
        }
        return "";
    }

    public static function send($email, $data, $view, $subject, $attachment = null)
    {
        try {
            $mail = new PHPMailer(true);
            // SMTP configurations
            $mail->isSMTP();
            $mail->Host = getenv('xxxxx');
            $mail->SMTPAuth = true;
            $mail->Username = getenv('xxxxx');
            $mail->Password = getenv('xxxxx');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = getenv('_PORT');
            $mail->setFrom(getenv('xxxxx'), 'xxxxx');
            $mail->Sender = getenv('xxxxx');
            $mail->ContentType = "text/html;charset=UTF-8\r\n";
            $mail->Priority = 3;
            $mail->addCustomHeader("MIME-Version: 1.0\r\n");
            $mail->addCustomHeader("X-Mailer: PHP'" . phpversion() . "'\r\n");
            $mail->addAddress($email);
            $mail->Subject = 'xxxxx - ' . $subject;

            $mail->isHTML();
            if ($attachment) {
                $mail->addAttachment($attachment);
            }
            // Email body content
            $mail->Body = view($view, $data)->render();
            // Send email
            if ($mail->send()) {
                return true;
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
        return false;
    }

    public static function sendToMail($email, $data, $view, $subject, $attachment = [])
    {
        $sendSmtpEmail = new SendSmtpEmail([
            'subject' => $subject,
            'sender' => ['name' => 'xxxxx', 'email' => 'xxxxx@gmail.com'],
            'replyTo' => ['name' => 'xxxxx', 'email' => 'xxxxx@gmail.com'],
            'to' => [['email' => $email]],
            'htmlContent' => view($view, $data)->render(),
            'params' => $data,
            'attachment' => $attachment
        ]);
        try {
            $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', getenv('_BREVO_API_KEY'));
            $api_instance = new TransactionalEmailsApi(
                new Client(),
                $config
            );
            $api_instance->sendTransacEmail($sendSmtpEmail);
        } catch (ApiException $e) {
            Log::error('Exception when calling TransactionalEmailsApi->sendTransacEmail:' . $e->getMessage());
            return false;
        }
        return true;
    }

    public static function sendOTPByEmail($email, $otp, $view, $subject): bool
    {
        return self::send($email, [
            'otp' => $otp
        ], $view, $subject);
    }

    public static function sendEmail($user, $data, $view, $subject, $attachment = null): bool
    {
        return self::send($user->email, $data, $view, $subject, $attachment);
    }

    public static function sendEmailToSupport($email, $data, $view, $subject): bool
    {
        try {
            $mail = new PHPMailer();
            // SMTP configurations
            $mail->isSMTP();
            $mail->Host = getenv('xxxxx');
            $mail->SMTPAuth = true;
            $mail->Username = getenv('xxxxx');
            $mail->Password = getenv('xxxxx');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = getenv('_PORT');
            $mail->setFrom($email, $data['name']);
            $mail->Sender = getenv('_EMAIL_USER_NAME');
            $mail->ContentType = "text/html;charset=UTF-8\r\n";
            $mail->Priority = 3;
            $mail->addCustomHeader("MIME-Version: 1.0\r\n");
            $mail->addCustomHeader("X-Mailer: PHP'" . phpversion() . "'\r\n");
            $mail->addAddress(getenv('xxxxx'), 'xxxxx');
            $mail->Subject = 'Request Inquiry - ' . $subject;

            $mail->isHTML();
            // Email body content
            $mail->Body = view($view, $data)->render();
            // Send email
            if ($mail->send()) {
                return true;
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
        return false;
    }

}
