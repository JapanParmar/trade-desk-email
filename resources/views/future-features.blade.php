@extends('layouts.app')

@section('title', 'Future Roadmap - TradeDesk Pro')
@section('header_title', 'Future Capabilities & Extensions')

@section('content')
<div class="space-y-8">
    
    <div class="glass-panel p-6 md:p-8 rounded-3xl border border-white/10 bg-gradient-to-r from-dark-900 via-dark-850 to-indigo-950/30 shadow-2xl">
        <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 mb-3 inline-block">
            <i class="fa-solid fa-wand-magic-sparkles mr-1"></i> Phase 2 Roadmap
        </span>
        <h2 class="text-3xl font-heading font-extrabold text-white tracking-tight">Enterprise Extensions & Roadmap</h2>
        <p class="text-xs md:text-sm text-slate-400 mt-1 max-w-2xl leading-relaxed">
            Planned upcoming modules designed to expand brokerage automation, high-volume batch dispatches, and advanced email template engines.
        </p>
    </div>

    <!-- 8 Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Card 1 -->
        <div class="glass-card p-6 rounded-3xl border border-white/10 flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center border border-cyan-500/20 mb-4">
                    <i class="fa-solid fa-mail-bulk text-xl"></i>
                </div>
                <h3 class="text-base font-heading font-bold text-white mb-2">Bulk Email Sending</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Select multiple registered clients at once to dispatch individual stock recommendation trade instructions simultaneously via their respective Gmail SMTP accounts.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-white/10 flex items-center justify-between font-mono">
                <span class="text-[10px] font-bold text-cyan-400 bg-cyan-500/10 px-2.5 py-1 rounded-full border border-cyan-500/20">Planned v2.0</span>
                <i class="fa-solid fa-lock text-slate-600 text-xs"></i>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="glass-card p-6 rounded-3xl border border-white/10 flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20 mb-4">
                    <i class="fa-solid fa-calendar-check text-xl"></i>
                </div>
                <h3 class="text-base font-heading font-bold text-white mb-2">Schedule Emails</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Queue trade instructions to be automatically dispatched at market open (09:15 AM) or target trading hours using background queue workers.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-white/10 flex items-center justify-between font-mono">
                <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-full border border-emerald-500/20">Planned v2.0</span>
                <i class="fa-solid fa-lock text-slate-600 text-xs"></i>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="glass-card p-6 rounded-3xl border border-white/10 flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center border border-amber-500/20 mb-4">
                    <i class="fa-solid fa-file-excel text-xl"></i>
                </div>
                <h3 class="text-base font-heading font-bold text-white mb-2">Import Excel (.xlsx)</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Direct drag-and-drop support for Microsoft Excel (.xlsx/.xls) spreadsheets with automatic column mapping for customer Gmail App Passwords.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-white/10 flex items-center justify-between font-mono">
                <span class="text-[10px] font-bold text-amber-400 bg-amber-500/10 px-2.5 py-1 rounded-full border border-amber-500/20">CSV Ready</span>
                <i class="fa-solid fa-check text-emerald-400 text-xs"></i>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="glass-card p-6 rounded-3xl border border-white/10 flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-purple-500/10 text-purple-400 flex items-center justify-center border border-purple-500/20 mb-4">
                    <i class="fa-solid fa-file-csv text-xl"></i>
                </div>
                <h3 class="text-base font-heading font-bold text-white mb-2">Export Logs & Audit</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Export filtered email dispatch audit records into CSV format for compliance review and internal trade execution auditing.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-white/10 flex items-center justify-between font-mono">
                <span class="text-[10px] font-bold text-purple-400 bg-purple-500/10 px-2.5 py-1 rounded-full border border-purple-500/20">Active Now</span>
                <i class="fa-solid fa-circle-check text-purple-400 text-xs"></i>
            </div>
        </div>

        <!-- Card 5 -->
        <div class="glass-card p-6 rounded-3xl border border-white/10 flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center border border-cyan-500/20 mb-4">
                    <i class="fa-solid fa-pen-nib text-xl"></i>
                </div>
                <h3 class="text-base font-heading font-bold text-white mb-2">Rich Text Email Editor</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    WYSIWYG HTML editor allowing custom formatting, tables, color highlights, and brand signatures for trade instruction templates.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-white/10 flex items-center justify-between font-mono">
                <span class="text-[10px] font-bold text-cyan-400 bg-cyan-500/10 px-2.5 py-1 rounded-full border border-cyan-500/20">Planned v2.1</span>
                <i class="fa-solid fa-lock text-slate-600 text-xs"></i>
            </div>
        </div>

        <!-- Card 6 -->
        <div class="glass-card p-6 rounded-3xl border border-white/10 flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-400 flex items-center justify-center border border-rose-500/20 mb-4">
                    <i class="fa-solid fa-layer-group text-xl"></i>
                </div>
                <h3 class="text-base font-heading font-bold text-white mb-2">Email Templates Engine</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Save custom reusable email templates for Intraday Call, Futures & Options (F&O), Options Buying, and Mutual Fund instructions.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-white/10 flex items-center justify-between font-mono">
                <span class="text-[10px] font-bold text-rose-400 bg-rose-500/10 px-2.5 py-1 rounded-full border border-rose-500/20">Planned v2.1</span>
                <i class="fa-solid fa-lock text-slate-600 text-xs"></i>
            </div>
        </div>

        <!-- Card 7 -->
        <div class="glass-card p-6 rounded-3xl border border-white/10 flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center border border-indigo-500/20 mb-4">
                    <i class="fa-solid fa-building-columns text-xl"></i>
                </div>
                <h3 class="text-base font-heading font-bold text-white mb-2">Multi-Broker Templates</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Manage multi-firm routing rules with tailored trade instruction layouts for Zerodha, AngelOne, Groww, ICICI Direct, and HDFC Securities.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-white/10 flex items-center justify-between font-mono">
                <span class="text-[10px] font-bold text-indigo-400 bg-indigo-500/10 px-2.5 py-1 rounded-full border border-indigo-500/20">Planned v2.2</span>
                <i class="fa-solid fa-lock text-slate-600 text-xs"></i>
            </div>
        </div>

        <!-- Card 8 -->
        <div class="glass-card p-6 rounded-3xl border border-white/10 flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-teal-500/10 text-teal-400 flex items-center justify-center border border-teal-500/20 mb-4">
                    <i class="fa-solid fa-paperclip text-xl"></i>
                </div>
                <h3 class="text-base font-heading font-bold text-white mb-2">Attachment Support</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Attach research PDF reports, technical chart screenshots, or legal authorization documents directly to trade instruction emails.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-white/10 flex items-center justify-between font-mono">
                <span class="text-[10px] font-bold text-teal-400 bg-teal-500/10 px-2.5 py-1 rounded-full border border-teal-500/20">Planned v2.2</span>
                <i class="fa-solid fa-lock text-slate-600 text-xs"></i>
            </div>
        </div>

    </div>

</div>
@endsection
