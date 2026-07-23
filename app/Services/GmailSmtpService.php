<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\EmailLog;
use Exception;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class GmailSmtpService
{
    /**
     * Test SMTP connection credentials for a given customer.
     */
    public function testConnection(Customer $customer): array
    {
        try {
            $transport = new EsmtpTransport('smtp.gmail.com', 587, false);
            $transport->setUsername($customer->gmail);
            $transport->setPassword($customer->app_password);

            $transport->start();
            $transport->stop();

            return [
                'success' => true,
                'message' => 'Gmail SMTP connection successful! Credentials verified.',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'SMTP Authentication/Connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send email using selected customer's Gmail account via SMTP.
     */
    public function sendStockEmail(Customer $customer, array $mailData): array
    {
        try {
            $transport = new EsmtpTransport('smtp.gmail.com', 587, false);
            $transport->setUsername($customer->gmail);
            $transport->setPassword($customer->app_password);

            $mailer = new Mailer($transport);

            $email = (new Email())
                ->from(new Address($customer->gmail, $customer->name))
                ->to($mailData['recipient_email'])
                ->subject($mailData['subject'])
                ->text($mailData['body_text'])
                ->html($mailData['body_html']);

            $mailer->send($email);

            // Log successful email dispatch
            EmailLog::create([
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'gmail_used' => $customer->gmail,
                'recipient_email' => $mailData['recipient_email'],
                'stock_name' => $mailData['stock_name'],
                'trade_type' => $mailData['trade_type'] ?? 'BUY',
                'subject' => $mailData['subject'],
                'body' => $mailData['body_html'],
                'status' => 'success',
                'smtp_error' => null,
                'sent_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => "Email sent successfully from {$customer->gmail} to {$mailData['recipient_email']}.",
            ];
        } catch (Exception $e) {
            $errorMsg = $e->getMessage();

            // Log failed email attempt
            EmailLog::create([
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'gmail_used' => $customer->gmail,
                'recipient_email' => $mailData['recipient_email'],
                'stock_name' => $mailData['stock_name'],
                'trade_type' => $mailData['trade_type'] ?? 'BUY',
                'subject' => $mailData['subject'],
                'body' => $mailData['body_html'] ?? '',
                'status' => 'failed',
                'smtp_error' => $errorMsg,
                'sent_at' => now(),
            ]);

            return [
                'success' => false,
                'message' => "SMTP Error: " . $errorMsg,
            ];
        }
    }
}
