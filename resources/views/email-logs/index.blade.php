@extends('layouts.app')

@section('title', 'Audit Trail — Notion TradeDesk')

@section('content')
<div class="space-y-6">
    
    <!-- Action Header -->
    <div class="notion-card flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="tag-pill bg-sky-tint text-notion-blue font-semibold mb-2 inline-block">AUDIT LOGS</span>
            <h1 class="text-3xl font-bold tracking-tight text-ink-black">Trade Email Audit Trail</h1>
            <p class="text-xs text-graphite">Complete record of stock dispatches, delivery statuses, and resend utilities</p>
        </div>

        <a href="{{ route('email-logs.export-csv') }}" class="btn-notion-primary text-xs">
            <i class="fa-solid fa-file-csv text-xs mr-1"></i> Export CSV Audit
        </a>
    </div>

    <!-- Search & Filters -->
    <div class="notion-card py-3">
        <form method="GET" action="{{ route('email-logs.index') }}" class="flex flex-col md:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-stone text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by client name, Gmail, stock ticker or recipient..." class="notion-input pl-10 text-xs">
            </div>

            <div class="flex items-center gap-2.5 w-full md:w-auto">
                <select name="status" onchange="this.form.submit()" class="notion-input w-full md:w-44 text-xs font-medium">
                    <option value="">All Delivery Statuses</option>
                    <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success Only</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed Only</option>
                </select>

                @if(request('search') || request('status'))
                    <a href="{{ route('email-logs.index') }}" class="btn-notion-outlined text-xs py-2 whitespace-nowrap">
                        Reset Filter
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="notion-card overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse font-sans">
                <thead>
                    <tr class="bg-paper-warmth border-b border-black/10 text-xs font-mono text-stone uppercase">
                        <th class="py-3.5 px-4">ID</th>
                        <th class="py-3.5 px-4">Client Name</th>
                        <th class="py-3.5 px-4">Gmail Sender</th>
                        <th class="py-3.5 px-4">Stock &amp; Action</th>
                        <th class="py-3.5 px-4">Recipient Email</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Sent Time</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 text-xs font-medium">
                    @forelse($logs as $log)
                    <tr class="hover:bg-paper-warmth/60 transition">
                        <td class="py-3.5 px-4 font-mono text-stone">
                            #{{ $log->id }}
                        </td>
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
                        <td class="py-3.5 px-4 font-mono text-stone">
                            {{ $log->sent_at ? $log->sent_at->format('M j, Y H:i:s') : 'N/A' }}
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <button onclick="resendEmail({{ $log->id }}, this)" class="btn-notion-outlined text-xs py-1 px-3 ml-auto">
                                <i class="fa-solid fa-rotate-right mr-1"></i> Resend
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-stone font-mono text-xs">
                            No dispatch audit logs found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-3 border-t border-black/10 font-mono">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    function resendEmail(logId, btn) {
        Swal.fire({
            title: 'Resending Trade Email...',
            text: 'Authenticating SMTP credentials & dispatching...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); },
            background: '#ffffff',
            color: '#000000'
        });

        window.ajaxFetch(`/email-logs/${logId}/resend`, { method: 'POST' })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Resent Successfully!',
                    text: data.message,
                    confirmButtonColor: '#0075de',
                    background: '#ffffff',
                    color: '#000000'
                }).then(() => window.location.reload());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Resend Failed',
                    text: data.message,
                    confirmButtonColor: '#0075de',
                    background: '#ffffff',
                    color: '#000000'
                });
            }
        });
    }
</script>
@endpush
