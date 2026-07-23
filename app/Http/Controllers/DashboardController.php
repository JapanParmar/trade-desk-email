<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('status', 'active')->count();
        $totalEmailsSent = EmailLog::where('status', 'success')->count();
        $emailsSentToday = EmailLog::where('status', 'success')
            ->whereDate('sent_at', now()->toDateString())
            ->count();
        $failedEmails = EmailLog::where('status', 'failed')->count();

        $recentLogs = EmailLog::with('customer')
            ->latest('sent_at')
            ->take(6)
            ->get();

        // Chart Data for last 7 days dispatches
        $chartDates = collect(range(6, 0))->map(fn($days) => now()->subDays($days)->format('Y-m-d'));
        
        $chartSuccess = $chartDates->map(function ($date) {
            return EmailLog::where('status', 'success')
                ->whereDate('sent_at', $date)
                ->count();
        });

        $chartFailed = $chartDates->map(function ($date) {
            return EmailLog::where('status', 'failed')
                ->whereDate('sent_at', $date)
                ->count();
        });

        $chartLabels = $chartDates->map(fn($date) => date('D, M j', strtotime($date)));

        return view('dashboard', compact(
            'totalCustomers',
            'activeCustomers',
            'totalEmailsSent',
            'emailsSentToday',
            'failedEmails',
            'recentLogs',
            'chartLabels',
            'chartSuccess',
            'chartFailed'
        ));
    }
}
