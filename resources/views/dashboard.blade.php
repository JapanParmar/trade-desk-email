@extends('layouts.app')

@section('title', 'Dashboard — Notion TradeDesk')

@section('content')
<div class="space-y-10">
    
    <!-- Hero Section with Notion Highlight Pill & Editorial Serif Subhead -->
    <div class="py-6 space-y-4">
        <div class="inline-flex items-center gap-2">
            <span class="tag-pill bg-marigold text-black font-semibold">NOTION BROKERAGE DECK</span>
            <span class="text-xs text-stone font-mono">Internal Operational System</span>
        </div>

        <h1 class="text-4xl md:text-6xl font-bold tracking-tight text-ink-black max-w-4xl leading-tight">
            Where teams and clients <span class="highlight-pill-marigold">dispatch</span> trade recommendations.
        </h1>

        <p class="font-lyon text-xl text-graphite max-w-2xl leading-relaxed">
            A quiet, tactile workspace to send live NSE stock instructions directly from verified client Gmail accounts using real-time price feeds.
        </p>

        <div class="pt-2 flex flex-wrap items-center gap-3">
            <a href="{{ route('send-mail.index') }}" class="btn-notion-primary">
                <i class="fa-solid fa-paper-plane text-xs"></i>
                <span>Open NSE Live Mailer</span>
            </a>
            <a href="{{ route('customers.index') }}" class="btn-notion-ghost">
                <i class="fa-solid fa-users text-xs"></i>
                <span>Manage Client Accounts</span>
            </a>
        </div>
    </div>

    <!-- 4 Feature Metric Cards Grid (Rotating Notion Accents) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: Marigold Yellow Accent Card -->
        <div class="notion-card-marigold flex flex-col justify-between min-h-[160px]">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-black uppercase tracking-wider">Total Clients</span>
                <i class="fa-solid fa-users text-lg text-black"></i>
            </div>
            <div>
                <div class="text-5xl font-bold tracking-tight text-black">{{ number_format($totalCustomers) }}</div>
                <p class="text-xs font-medium text-black/70 mt-1">Registered Gmail Accounts</p>
            </div>
        </div>

        <!-- Card 2: Pure White Card -->
        <div class="notion-card flex flex-col justify-between min-h-[160px]">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone uppercase tracking-wider">Active SMTP</span>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
            </div>
            <div>
                <div class="text-5xl font-bold tracking-tight text-ink-black">{{ number_format($activeCustomers) }}</div>
                <p class="text-xs font-medium text-slate mt-1">Ready for Dispatch</p>
            </div>
        </div>

        <!-- Card 3: Pure White Card -->
        <div class="notion-card flex flex-col justify-between min-h-[160px]">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone uppercase tracking-wider">Total Dispatches</span>
                <i class="fa-solid fa-paper-plane text-lg text-notion-blue"></i>
            </div>
            <div>
                <div class="text-5xl font-bold tracking-tight text-ink-black">{{ number_format($totalEmailsSent) }}</div>
                <p class="text-xs font-medium text-slate mt-1">Lifetime Trade Instructions</p>
            </div>
        </div>

        <!-- Card 4: Sky Tint Card -->
        <div class="notion-card bg-sky-tint border-sky-200 flex flex-col justify-between min-h-[160px]">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-notion-blue uppercase tracking-wider">Sent Today</span>
                <i class="fa-solid fa-calendar-day text-lg text-notion-blue"></i>
            </div>
            <div>
                <div class="text-5xl font-bold tracking-tight text-notion-blue">{{ number_format($emailsSentToday) }}</div>
                <p class="text-xs font-medium text-notion-blue/80 mt-1">{{ date('M j, Y') }}</p>
            </div>
        </div>

    </div>

    <!-- Chart & Assistant Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Dispatch Chart (8 Cols) -->
        <div class="lg:col-span-8 notion-card space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-black/10">
                <div>
                    <h3 class="text-lg font-bold text-ink-black">Dispatch Velocity &amp; Delivery</h3>
                    <p class="text-xs text-slate">7-Day successful vs failed delivery history</p>
                </div>
                <span class="tag-pill bg-sky-tint text-notion-blue font-mono text-xs">7-Day Log</span>
            </div>
            <div class="h-64 relative">
                <canvas id="dispatchChart"></canvas>
            </div>
        </div>

        <!-- Notion Midnight Dark Feature Card (4 Cols) -->
        <div class="lg:col-span-4 notion-card-midnight flex flex-col justify-between space-y-6">
            <div class="space-y-3">
                <span class="tag-pill bg-white/20 text-white font-mono text-xs">NSE LIVE API</span>
                <h3 class="text-2xl font-bold text-white tracking-tight">Real-Time Indian Market Engine</h3>
                <p class="text-xs text-white/80 leading-relaxed font-sans">
                    Fetch live prices for RELIANCE, SBIN, TCS, INFY with automated stop-loss, profit target calculation, and technical notes.
                </p>
            </div>

            <div class="space-y-2 pt-4 border-t border-white/10">
                <a href="{{ route('send-mail.index') }}" class="btn-notion-primary w-full text-center py-2.5">
                    Launch Trade Dispatcher
                </a>
                <a href="{{ route('customers.index') }}" class="btn-notion-ghost w-full text-center py-2 text-xs text-white bg-white/10 hover:bg-white/20">
                    View Client Accounts
                </a>
            </div>
        </div>

    </div>

    <!-- Recent Audit Logs Table -->
    <div class="notion-card space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-black/10">
            <div>
                <h3 class="text-lg font-bold text-ink-black">Recent Trade Email Audit Trail</h3>
                <p class="text-xs text-slate">Latest recommendation dispatches via Gmail SMTP</p>
            </div>
            <a href="{{ route('email-logs.index') }}" class="btn-notion-outlined text-xs">
                View Full Audit Logs <i class="fa-solid fa-angle-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-black/10 text-xs font-mono text-stone uppercase">
                        <th class="py-3 px-4">Client Name</th>
                        <th class="py-3 px-4">Gmail Sender</th>
                        <th class="py-3 px-4">Stock &amp; Action</th>
                        <th class="py-3 px-4">Recipient Email</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 text-xs">
                    @forelse($recentLogs as $log)
                    <tr class="hover:bg-paper-warmth/60 transition">
                        <td class="py-3.5 px-4 font-bold text-ink-black">
                            {{ $log->customer_name }}
                        </td>
                        <td class="py-3.5 px-4 font-mono text-notion-blue">
                            {{ $log->gmail_used }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="tag-pill bg-paper-warmth border border-black/10 font-mono font-bold text-ink-black">
                                {{ $log->trade_type }} {{ $log->stock_name }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-slate">
                            {{ $log->recipient_email }}
                        </td>
                        <td class="py-3.5 px-4">
                            @if($log->status === 'success')
                                <span class="tag-pill bg-emerald-100 text-emerald-800 font-bold">
                                    Success
                                </span>
                            @else
                                <span class="tag-pill bg-rose-100 text-rose-800 font-bold" title="{{ $log->smtp_error }}">
                                    Failed
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right text-stone font-mono">
                            {{ $log->sent_at ? $log->sent_at->diffForHumans() : 'N/A' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-stone font-mono text-xs">
                            No dispatch history recorded yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('dispatchChart').getContext('2d');
        const labels = @json($chartLabels);
        const successData = @json($chartSuccess);
        const failedData = @json($chartFailed);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Successful Dispatches',
                        data: successData,
                        backgroundColor: '#0075de',
                        borderRadius: 6,
                    },
                    {
                        label: 'Failed Dispatches',
                        data: failedData,
                        backgroundColor: '#f64932',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#615d59', font: { family: 'Inter', size: 12 } }
                    },
                    y: {
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: { color: '#615d59', font: { family: 'Inter', size: 12 }, precision: 0 }
                    }
                },
                plugins: {
                    legend: {
                        labels: { color: '#000000', font: { family: 'Inter', size: 12, weight: '500' } }
                    }
                }
            }
        });
    });
</script>
@endpush
