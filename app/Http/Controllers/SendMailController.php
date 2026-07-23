<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\EmailTemplate;
use App\Models\Setting;
use App\Services\GmailSmtpService;
use App\Services\StockService;
use Illuminate\Http\Request;

class SendMailController extends Controller
{
    protected StockService $stockService;
    protected GmailSmtpService $smtpService;

    public function __construct(StockService $stockService, GmailSmtpService $smtpService)
    {
        $this->stockService = $stockService;
        $this->smtpService = $smtpService;
    }

    public function index(Request $request)
    {
        $customers = Customer::where('status', 'active')->orderBy('name')->get();
        $stockList = $this->stockService->getStockList();
        $defaultBrokerageEmail = Setting::get('brokerage_email', 'orders@capitalbrokerage.com');
        $templates = EmailTemplate::latest()->get();
        
        $selectedCustomerId = $request->query('customer_id');

        return view('send-mail.index', compact('customers', 'stockList', 'defaultBrokerageEmail', 'selectedCustomerId', 'templates'));
    }

    public function getStockDetails(Request $request)
    {
        $symbol = $request->input('symbol', 'RELIANCE');
        $details = $this->stockService->getStockDetails($symbol);
        return response()->json([
            'success' => true,
            'data' => $details,
        ]);
    }

    public function send(Request $request)
    {
        // COMPULSORY FIELDS: customer_id, recipient_email, stock_name, entry_range
        // ALL OTHER FIELDS ARE OPTIONAL!
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'recipient_email' => 'required|email',
            'stock_name' => 'required|string',
            'entry_range' => 'required|string',
            
            'trade_type' => 'nullable|string|in:BUY,SELL',
            'stop_loss' => 'nullable|string',
            'target_1' => 'nullable|string',
            'target_2' => 'nullable|string',
            'target_3' => 'nullable|string',
            'profit_booking' => 'nullable|string',
            'holding_period' => 'nullable|string',
            'notes' => 'nullable|string',
            'custom_body_html' => 'nullable|string',
            'custom_subject' => 'nullable|string',
        ]);

        $customer = Customer::findOrFail($request->customer_id);

        if ($customer->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Selected customer Gmail account is currently inactive.',
            ], 422);
        }

        $stockName = strtoupper(trim($request->stock_name));
        $tradeType = strtoupper($request->trade_type ?: 'BUY');
        $entryRange = $request->entry_range;
        $stopLoss = $request->stop_loss ?: 'N/A';
        $t1 = $request->target_1 ?: 'N/A';
        $t2 = $request->target_2 ?: 'N/A';
        $t3 = $request->target_3 ?: 'N/A';
        $profitBooking = $request->profit_booking ?: 'N/A';
        $holdingPeriod = $request->holding_period ?: 'N/A';
        $notes = $request->notes ?: '';

        $subject = $request->custom_subject ?: "Trade Instruction - {$tradeType} {$stockName}";
        $subject = str_replace(
            ['{STOCK_NAME}', '{TRADE_TYPE}', '{ENTRY_RANGE}', '{STOP_LOSS}', '{TARGET_1}', '{TARGET_2}', '{TARGET_3}', '{PROFIT_BOOKING}', '{HOLDING_PERIOD}', '{CLIENT_NAME}', '{CLIENT_GMAIL}', '{NOTES}'],
            [$stockName, $tradeType, $entryRange, $stopLoss, $t1, $t2, $t3, $profitBooking, $holdingPeriod, $customer->name, $customer->gmail, $notes],
            $subject
        );

        // Check if user submitted custom HTML from WYSIWYG editor
        if ($request->filled('custom_body_html')) {
            $bodyHtml = $request->custom_body_html;

            // Replace template placeholders if present
            $bodyHtml = str_replace(
                ['{STOCK_NAME}', '{TRADE_TYPE}', '{ENTRY_RANGE}', '{STOP_LOSS}', '{TARGET_1}', '{TARGET_2}', '{TARGET_3}', '{PROFIT_BOOKING}', '{HOLDING_PERIOD}', '{CLIENT_NAME}', '{CLIENT_GMAIL}', '{NOTES}'],
                [$stockName, $tradeType, $entryRange, $stopLoss, $t1, $t2, $t3, $profitBooking, $holdingPeriod, $customer->name, $customer->gmail, $notes],
                $bodyHtml
            );

            $bodyText = strip_tags($bodyHtml);
        } else {
            // Default HTML Body
            $bodyText = "Dear Brokerage Team,\n\n"
                . "Please execute the following trade on my behalf.\n\n"
                . "Trade Type: {$tradeType}\n"
                . "Stock Name: {$stockName}\n"
                . "Entry Range: ₹{$entryRange}\n"
                . "Stop Loss: ₹{$stopLoss}\n"
                . "Target 1: ₹{$t1}\n"
                . "Target 2: ₹{$t2}\n"
                . "Target 3: ₹{$t3}\n"
                . "Profit Booking: {$profitBooking}\n"
                . "Holding Period: {$holdingPeriod}\n\n"
                . "Notes: {$notes}\n\n"
                . "Regards,\n{$customer->name}\n{$customer->gmail}";

            $bodyHtml = '
            <!DOCTYPE html>
            <html>
            <head><meta charset="utf-8"></head>
            <body style="font-family: Arial, sans-serif; background-color: #f4f6f9; color: #1e293b; padding: 20px;">
              <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px;">
                <h2 style="color: #0075de; margin-top: 0;">Trade Execution Instruction</h2>
                <p>Dear Brokerage Team,</p>
                <p>Please execute the following trade instruction on my behalf.</p>
                <table style="width: 100%; border-collapse: collapse; margin: 16px 0; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0;">
                  <tr style="border-bottom: 1px solid #e2e8f0;"><td style="padding: 10px; font-weight: bold;">Trade Action:</td><td style="padding: 10px; font-weight: bold; color: ' . ($tradeType === 'BUY' ? '#0075de' : '#ef4444') . ';">' . $tradeType . '</td></tr>
                  <tr style="border-bottom: 1px solid #e2e8f0;"><td style="padding: 10px; font-weight: bold;">Stock Name:</td><td style="padding: 10px; font-weight: bold;">' . htmlspecialchars($stockName) . '</td></tr>
                  <tr style="border-bottom: 1px solid #e2e8f0;"><td style="padding: 10px; font-weight: bold;">Entry Range:</td><td style="padding: 10px;">₹' . htmlspecialchars($entryRange) . '</td></tr>
                  <tr style="border-bottom: 1px solid #e2e8f0;"><td style="padding: 10px; font-weight: bold;">Stop Loss:</td><td style="padding: 10px; color: #ef4444;">₹' . htmlspecialchars($stopLoss) . '</td></tr>
                  <tr style="border-bottom: 1px solid #e2e8f0;"><td style="padding: 10px; font-weight: bold;">Target 1:</td><td style="padding: 10px;">₹' . htmlspecialchars($t1) . '</td></tr>
                  <tr><td style="padding: 10px; font-weight: bold;">Holding Period:</td><td style="padding: 10px;">' . htmlspecialchars($holdingPeriod) . '</td></tr>
                </table>
                ' . ($notes ? '<div style="background: #eff6ff; border-left: 4px solid #0075de; padding: 12px; border-radius: 4px;">' . nl2br(htmlspecialchars($notes)) . '</div>' : '') . '
                <p style="margin-top: 20px; font-size: 13px; color: #64748b;">This email serves as my authorization to execute the trade instruction in my trading account.</p>
                <p>Regards,<br><strong>' . htmlspecialchars($customer->name) . '</strong><br><span style="color: #0075de;">' . htmlspecialchars($customer->gmail) . '</span></p>
              </div>
            </body>
            </html>';
        }

        $mailData = [
            'recipient_email' => $request->recipient_email,
            'stock_name' => $stockName,
            'trade_type' => $tradeType,
            'subject' => $subject,
            'body_text' => $bodyText,
            'body_html' => $bodyHtml,
        ];

        $res = $this->smtpService->sendStockEmail($customer, $mailData);

        return response()->json($res);
    }
}
