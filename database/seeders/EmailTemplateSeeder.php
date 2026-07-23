<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        EmailTemplate::updateOrCreate(
            ['name' => 'Standard NSE Trade Instruction'],
            [
                'subject' => 'Trade Instruction - {TRADE_TYPE} {STOCK_NAME}',
                'is_default' => true,
                'body_html' => '<p>Dear Brokerage Team,</p>
<p>Please execute the following trade instruction on my behalf.</p>
<table style="width: 100%; border-collapse: collapse; margin: 14px 0; background: #f6f5f4; border-radius: 8px; border: 1px solid rgba(0,0,0,0.1); overflow: hidden;">
    <tr style="border-bottom: 1px solid rgba(0,0,0,0.08);"><td style="padding: 8px 12px; font-weight: bold;">Trade Action:</td><td style="padding: 8px 12px; font-weight: bold; color: #0075de;">{TRADE_TYPE}</td></tr>
    <tr style="border-bottom: 1px solid rgba(0,0,0,0.08);"><td style="padding: 8px 12px; font-weight: bold;">Stock Name:</td><td style="padding: 8px 12px; font-weight: bold;">{STOCK_NAME}</td></tr>
    <tr style="border-bottom: 1px solid rgba(0,0,0,0.08);"><td style="padding: 8px 12px; font-weight: bold;">Entry Range:</td><td style="padding: 8px 12px; font-family: monospace;">₹{ENTRY_RANGE}</td></tr>
    <tr style="border-bottom: 1px solid rgba(0,0,0,0.08);"><td style="padding: 8px 12px; font-weight: bold;">Stop Loss:</td><td style="padding: 8px 12px; color: #f64932; font-weight: bold; font-family: monospace;">₹{STOP_LOSS}</td></tr>
    <tr style="border-bottom: 1px solid rgba(0,0,0,0.08);"><td style="padding: 8px 12px; font-weight: bold;">Target 1:</td><td style="padding: 8px 12px; font-family: monospace;">₹{TARGET_1}</td></tr>
    <tr style="border-bottom: 1px solid rgba(0,0,0,0.08);"><td style="padding: 8px 12px; font-weight: bold;">Target 2:</td><td style="padding: 8px 12px; font-family: monospace;">₹{TARGET_2}</td></tr>
    <tr style="border-bottom: 1px solid rgba(0,0,0,0.08);"><td style="padding: 8px 12px; font-weight: bold;">Target 3:</td><td style="padding: 8px 12px; font-family: monospace;">₹{TARGET_3}</td></tr>
    <tr style="border-bottom: 1px solid rgba(0,0,0,0.08);"><td style="padding: 8px 12px; font-weight: bold;">Profit Booking:</td><td style="padding: 8px 12px;">{PROFIT_BOOKING}</td></tr>
    <tr><td style="padding: 8px 12px; font-weight: bold;">Holding Period:</td><td style="padding: 8px 12px;">{HOLDING_PERIOD}</td></tr>
</table>
<p style="margin-top: 14px; color: #615d59;">This email serves as official authorization to execute the trade instruction in my trading account.</p>
<p>Regards,<br><strong>{CLIENT_NAME}</strong><br><span style="color: #0075de; font-family: monospace;">{CLIENT_GMAIL}</span></p>'
            ]
        );

        EmailTemplate::updateOrCreate(
            ['name' => 'Minimal Quick Stock Order'],
            [
                'subject' => 'URGENT: {TRADE_TYPE} Order for {STOCK_NAME}',
                'is_default' => false,
                'body_html' => '<p>Hi Trading Desk,</p>
<p>Kindly place a <strong>{TRADE_TYPE}</strong> order for <strong>{STOCK_NAME}</strong> at entry price <strong>₹{ENTRY_RANGE}</strong>.</p>
<p>Stop Loss: <span style="color: red; font-weight: bold;">₹{STOP_LOSS}</span> | Target: <span style="color: green; font-weight: bold;">₹{TARGET_1}</span></p>
<p>Thanks,<br><strong>{CLIENT_NAME}</strong></p>'
            ]
        );
    }
}
