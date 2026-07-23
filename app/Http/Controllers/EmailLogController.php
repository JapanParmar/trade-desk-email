<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\EmailLog;
use App\Services\GmailSmtpService;
use Illuminate\Http\Request;

class EmailLogController extends Controller
{
    protected GmailSmtpService $smtpService;

    public function __construct(GmailSmtpService $smtpService)
    {
        $this->smtpService = $smtpService;
    }

    public function index(Request $request)
    {
        $query = EmailLog::with('customer');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('gmail_used', 'like', "%{$search}%")
                  ->orWhere('recipient_email', 'like', "%{$search}%")
                  ->orWhere('stock_name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['success', 'failed'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('sent_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('sent_at', '<=', $request->date_to);
        }

        $logs = $query->latest('sent_at')->paginate(15)->withQueryString();
        $customers = Customer::orderBy('name')->get();

        return view('email-logs.index', compact('logs', 'customers'));
    }

    public function show(EmailLog $log)
    {
        return response()->json([
            'success' => true,
            'log' => $log,
        ]);
    }

    public function resend(EmailLog $log)
    {
        $customer = $log->customer;
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Associated customer no longer exists.',
            ], 422);
        }

        $mailData = [
            'recipient_email' => $log->recipient_email,
            'stock_name' => $log->stock_name,
            'trade_type' => $log->trade_type,
            'subject' => $log->subject,
            'body_text' => strip_tags($log->body),
            'body_html' => $log->body,
        ];

        $res = $this->smtpService->sendStockEmail($customer, $mailData);

        return response()->json($res);
    }

    public function export(Request $request)
    {
        $query = EmailLog::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('gmail_used', 'like', "%{$search}%")
                  ->orWhere('stock_name', 'like', "%{$search}%");
            });
        }

        $logs = $query->latest('sent_at')->get();

        $csvHeader = ['ID', 'Customer Name', 'Gmail Used', 'Recipient Email', 'Stock', 'Trade Type', 'Subject', 'Status', 'SMTP Error', 'Sent At'];
        
        $callback = function () use ($logs, $csvHeader) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->customer_name,
                    $log->gmail_used,
                    $log->recipient_email,
                    $log->stock_name,
                    $log->trade_type,
                    $log->subject,
                    strtoupper($log->status),
                    $log->smtp_error ?: 'N/A',
                    $log->sent_at ? $log->sent_at->format('Y-m-d H:i:s') : '',
                ]);
            }
            fclose($file);
        };

        $filename = 'email_logs_' . date('Y_m_d_His') . '.csv';

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
