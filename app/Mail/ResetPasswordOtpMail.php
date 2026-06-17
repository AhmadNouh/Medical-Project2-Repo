<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordOtpMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * Get the message envelope.
     */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'إعادة تعيين كلمة المرور - شركة المستلزمات الطبية',
    //     );
    // }

    // /**
    //  * Get the message content definition.
    //  */
    // public function content(): Content
    // {
    //     return new Content(
    //         text: "كود التحقق الخاص بك لإعادة تعيين كلمة المرور هو: " . $this->code
    //     );
    // }

    public function build()
    {
        $htmlContent = "
        <div dir='rtl' style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; background-color: #ffffff;'>
            <div style='text-align: center; padding-bottom: 20px; border-bottom: 2px solid #3b82f6;'>
                <h2 style='color: #1e3a8a; margin: 0;'>نظام الدعم الطبي</h2>
            </div>
            <div style='padding: 30px 10px; text-align: center;'>
                <p style='font-size: 16px; color: #334155;'>لقد تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بك. يرجى استخدام رمز التحقق (OTP) التالي لإتمام العملية:</p>
                <div style='margin: 30px auto; padding: 15px; background-color: #f1f5f9; border-radius: 6px; display: inline-block; letter-spacing: 4px;'>
                    <span style='font-size: 32px; font-weight: bold; color: #2563eb;'>{$this->code}</span>
                </div>
                <p style='font-size: 14px; color: #64748b;'>هذا الرمز صالح لمدة 15 دقيقة فقط لحماية أمان حسابك.</p>
            </div>
            <div style='text-align: center; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8;'>
                <p>إذا لم تطلب إعادة تعيين كلمة المرور، يمكنك تجاهل هذا الإيميل بأمان.</p>
                <p>© " . date('Y') . " جميع الحقوق محفوظة للشركة الطبية.</p>
            </div>
        </div>
        ";

        return $this->subject('قفل الأمان: رمز التحقق لإعادة تعيين كلمة المرور')
                    ->html($htmlContent);
     }
    
    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
