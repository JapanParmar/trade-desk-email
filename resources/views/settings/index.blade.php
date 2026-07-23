@extends('layouts.app')

@section('title', 'System Settings — Notion TradeDesk')

@section('content')
<div class="space-y-8 max-w-3xl">
    
    <!-- Header -->
    <div class="py-2 space-y-2">
        <div class="inline-flex items-center gap-2">
            <span class="tag-pill bg-marigold text-black font-semibold">CONFIGURATION</span>
            <span class="text-xs text-stone font-mono">System Defaults</span>
        </div>
        <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-ink-black">
            System <span class="highlight-pill-marigold">Settings</span>
        </h1>
        <p class="font-lyon text-base text-graphite">
            Configure default brokerage firm identity and default recipient email address for trade dispatches.
        </p>
    </div>

    <!-- Settings Form Card -->
    <div class="notion-card space-y-6">
        <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-2">
                    Brokerage Firm Name *
                </label>
                <input type="text" name="company_name" value="{{ $companyName }}" required placeholder="e.g. Capital Brokerage Desk" class="notion-input font-bold text-sm">
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-2">
                    Default Brokerage Order Recipient Email *
                </label>
                <input type="email" name="brokerage_email" value="{{ $brokerageEmail }}" required placeholder="orders@capitalbrokerage.com" class="notion-input font-mono text-sm">
                <p class="text-xs text-stone mt-2">
                    This recipient email address will be automatically pre-filled when sending trade execution instructions on behalf of clients.
                </p>
            </div>

            <div class="pt-4 border-t border-black/10 flex justify-end">
                <button type="submit" class="btn-notion-primary text-xs py-2.5 px-6">
                    <i class="fa-solid fa-floppy-disk text-xs mr-1"></i> Save System Settings
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
