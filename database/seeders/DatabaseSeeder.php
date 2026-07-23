<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\EmailLog;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Settings
        Setting::set('brokerage_email', 'orders@capitalbrokerage.com');
        Setting::set('company_name', 'Capital Vantage Brokerage');

        // Seed Sample Customers
        $c1 = Customer::create([
            'name' => 'Rajesh Sharma',
            'gmail' => 'rajesh.sharma.trade@gmail.com',
            'app_password' => 'abcd efgh ijkl mnop',
            'mobile' => '+91 98765 43210',
            'broker_code' => 'BRK-8821',
            'status' => 'active',
        ]);

        $c2 = Customer::create([
            'name' => 'Priya Patel',
            'gmail' => 'priya.patel.invest@gmail.com',
            'app_password' => '1234 5678 9012 3456',
            'mobile' => '+91 98123 45678',
            'broker_code' => 'BRK-4092',
            'status' => 'active',
        ]);

        $c3 = Customer::create([
            'name' => 'Amitabh Varma',
            'gmail' => 'amitabh.varma.wealth@gmail.com',
            'app_password' => 'xyzq wety uiop asdf',
            'mobile' => '+91 97654 32109',
            'broker_code' => 'BRK-1055',
            'status' => 'active',
        ]);

        $c4 = Customer::create([
            'name' => 'Vikram Sengupta',
            'gmail' => 'vikram.sengupta@gmail.com',
            'app_password' => 'pass word app key1',
            'mobile' => '+91 99887 76655',
            'broker_code' => 'BRK-7723',
            'status' => 'inactive',
        ]);

        // Seed Sample Email Logs
        EmailLog::create([
            'customer_id' => $c1->id,
            'customer_name' => $c1->name,
            'gmail_used' => $c1->gmail,
            'recipient_email' => 'orders@capitalbrokerage.com',
            'stock_name' => 'RELIANCE',
            'trade_type' => 'BUY',
            'subject' => 'Trade Instruction - BUY RELIANCE',
            'body' => 'Dear Brokerage Team, Please execute the trade for RELIANCE.',
            'status' => 'success',
            'smtp_error' => null,
            'sent_at' => now()->subHours(2),
        ]);

        EmailLog::create([
            'customer_id' => $c2->id,
            'customer_name' => $c2->name,
            'gmail_used' => $c2->gmail,
            'recipient_email' => 'orders@capitalbrokerage.com',
            'stock_name' => 'TATAMOTORS',
            'trade_type' => 'BUY',
            'subject' => 'Trade Instruction - BUY TATAMOTORS',
            'body' => 'Dear Brokerage Team, Please execute the trade for TATAMOTORS.',
            'status' => 'success',
            'smtp_error' => null,
            'sent_at' => now()->subHours(4),
        ]);

        EmailLog::create([
            'customer_id' => $c3->id,
            'customer_name' => $c3->name,
            'gmail_used' => $c3->gmail,
            'recipient_email' => 'orders@capitalbrokerage.com',
            'stock_name' => 'HDFCBANK',
            'trade_type' => 'BUY',
            'subject' => 'Trade Instruction - BUY HDFCBANK',
            'body' => 'Dear Brokerage Team, Please execute the trade for HDFCBANK.',
            'status' => 'failed',
            'smtp_error' => '535-5.7.8 Username and Password not accepted. Learn more at https://support.google.com/mail/answer/71262',
            'sent_at' => now()->subDays(1),
        ]);
    }
}
