@extends('layouts.app')

@section('title', 'Send NSE Trade Mail — Notion TradeDesk')

@section('content')
<div class="space-y-8">
    
    <!-- Hero Header -->
    <div class="py-4 space-y-2">
        <div class="inline-flex items-center gap-2">
            <span class="tag-pill bg-sky-tint text-notion-blue font-semibold">NSE EXCHANGE LIVE API</span>
            <span class="text-xs text-stone font-mono">WYSIWYG Email Template Dispatcher</span>
        </div>
        <h1 class="text-3xl md:text-5xl font-bold tracking-tight text-ink-black">
            Send Stock <span class="highlight-pill-marigold">Recommendation</span> Email
        </h1>
        <p class="font-lyon text-lg text-graphite max-w-2xl">
            Select a saved email template or compose custom HTML instructions. Only Stock Name &amp; Entry Range are compulsory — all other metrics are optional.
        </p>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Form Controls (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Mode Switcher -->
            <div class="notion-card bg-paper-warmth flex items-center justify-between p-3">
                <span class="text-xs font-mono font-bold text-stone">DATA FETCH MODE:</span>
                <div class="flex items-center gap-2">
                    <button type="button" id="btnLiveMode" onclick="setMode('live')" class="btn-notion-primary text-xs py-1.5 px-3">
                        ⚡ Live NSE Quote
                    </button>
                    <button type="button" id="btnManualMode" onclick="setMode('manual')" class="btn-notion-outlined text-xs py-1.5 px-3">
                        ⌨️ Manual Override
                    </button>
                </div>
            </div>

            <form id="sendMailForm" onsubmit="handleSendMail(event)" class="space-y-6">
                @csrf
                
                <!-- Section 1: Client Gmail Account (COMPULSORY) -->
                <div class="notion-card space-y-4">
                    <div class="pb-3 border-b border-black/10 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-ink-black flex items-center gap-2">
                            <i class="fa-solid fa-user-check text-notion-blue"></i>
                            1. Select Client Gmail Sender
                        </h3>
                        <span class="tag-pill bg-rose-100 text-rose-800 text-[11px] font-bold">COMPULSORY</span>
                    </div>

                    <div>
                        <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1.5">Client Gmail Account *</label>
                        <select id="customer_id" name="customer_id" required onchange="updatePreview()" class="notion-input font-medium">
                            <option value="">-- Choose Client Gmail Account --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" 
                                    data-name="{{ $customer->name }}" 
                                    data-gmail="{{ $customer->gmail }}"
                                    {{ $selectedCustomerId == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} ({{ $customer->gmail }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1.5">Order Recipient Email *</label>
                        <input type="email" id="recipient_email" name="recipient_email" required value="{{ $defaultBrokerageEmail }}" oninput="updatePreview()" placeholder="orders@capitalbrokerage.com" class="notion-input font-mono">
                    </div>
                </div>

                <!-- Section 2: NSE Recommendation Details (ONLY Stock Name & Entry Range are Compulsory) -->
                <div class="notion-card space-y-5">
                    <div class="pb-3 border-b border-black/10 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-ink-black flex items-center gap-2">
                            <i class="fa-solid fa-chart-line text-notion-blue"></i>
                            2. Stock &amp; Entry Price Details
                        </h3>
                        <span id="fetchTimeBadge" class="tag-pill bg-marigold text-black font-mono text-xs">NSE REALTIME</span>
                    </div>

                    <!-- Preset & Search -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1.5">Preset Indian Stocks (NSE)</label>
                            <select id="preset_stock" onchange="loadStockData(this.value)" class="notion-input font-mono font-medium">
                                @foreach($stockList as $stk)
                                    <option value="{{ $stk['symbol'] }}">{{ $stk['symbol'] }} - {{ $stk['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1.5">Custom Ticker Lookup</label>
                            <div class="flex gap-2">
                                <input type="text" id="custom_symbol" placeholder="RELIANCE, SBIN, TCS" class="notion-input font-mono uppercase">
                                <button type="button" onclick="fetchCustomStock()" class="btn-notion-ghost shrink-0">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Live Quote Info Box -->
                    <div id="liveQuoteInfo" class="p-4 rounded-lg bg-paper-warmth border border-black/10 flex flex-wrap items-center justify-between gap-4 font-mono">
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-notion-blue animate-pulse"></span>
                            <div>
                                <p class="text-[10px] text-stone font-bold tracking-wider" id="liveExchangeText">NSE INDIA REAL-TIME QUOTE</p>
                                <div class="flex items-baseline gap-3 mt-0.5">
                                    <span id="livePriceText" class="text-2xl font-bold text-ink-black">₹1,284.90</span>
                                    <span id="liveChangeText" class="text-xs font-bold text-emerald-600">+1.45% ▲</span>
                                </div>
                            </div>
                        </div>
                        <span id="liveSymbolBadge" class="tag-pill bg-sky-tint text-notion-blue font-bold text-xs">RELIANCE.NS</span>
                    </div>

                    <!-- COMPULSORY FIELDS -->
                    <div class="p-3.5 bg-paper-warmth rounded-lg border border-black/10 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono font-bold text-ink-black uppercase tracking-wider">Compulsory Fields</span>
                            <span class="tag-pill bg-rose-600 text-white font-bold text-[10px]">REQUIRED</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1.5">Stock Name / Symbol *</label>
                                <input type="text" id="stock_name" name="stock_name" required oninput="updatePreview()" placeholder="RELIANCE" class="notion-input font-bold uppercase border-black/30">
                            </div>

                            <div>
                                <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1.5">Entry Range (₹) *</label>
                                <input type="text" id="entry_range" name="entry_range" required oninput="updatePreview()" placeholder="1280 - 1285" class="notion-input font-mono text-xs border-black/30">
                            </div>
                        </div>
                    </div>

                    <!-- OPTIONAL FIELDS -->
                    <div class="space-y-4 pt-2">
                        <div class="flex items-center justify-between border-b border-black/10 pb-2">
                            <span class="text-xs font-mono font-bold text-stone uppercase tracking-wider">Optional Trade Parameters (Leave blank if not needed)</span>
                            <span class="tag-pill bg-paper-warmth text-stone text-[10px]">OPTIONAL</span>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-mono font-bold text-stone uppercase tracking-wider mb-1.5">Trade Action (Optional)</label>
                                <select id="trade_type" name="trade_type" onchange="updatePreview()" class="notion-input font-bold text-xs">
                                    <option value="BUY" selected>BUY</option>
                                    <option value="SELL">SELL</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-mono font-bold text-stone uppercase tracking-wider mb-1.5">Stop Loss (₹) (Optional)</label>
                                <input type="text" id="stop_loss" name="stop_loss" oninput="updatePreview()" placeholder="Optional SL" class="notion-input font-mono text-xs text-coral">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-mono font-bold text-stone uppercase tracking-wider mb-1.5">Target 1 (₹) (Optional)</label>
                                <input type="text" id="target_1" name="target_1" oninput="updatePreview()" placeholder="Target 1" class="notion-input font-mono text-xs">
                            </div>

                            <div>
                                <label class="block text-xs font-mono font-bold text-stone uppercase tracking-wider mb-1.5">Target 2 (₹) (Optional)</label>
                                <input type="text" id="target_2" name="target_2" oninput="updatePreview()" placeholder="Target 2" class="notion-input font-mono text-xs">
                            </div>

                            <div>
                                <label class="block text-xs font-mono font-bold text-stone uppercase tracking-wider mb-1.5">Target 3 (₹) (Optional)</label>
                                <input type="text" id="target_3" name="target_3" oninput="updatePreview()" placeholder="Target 3" class="notion-input font-mono text-xs">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-mono font-bold text-stone uppercase tracking-wider mb-1.5">Profit Booking (Optional)</label>
                                <input type="text" id="profit_booking" name="profit_booking" oninput="updatePreview()" placeholder="Partial at T1" class="notion-input text-xs">
                            </div>

                            <div>
                                <label class="block text-xs font-mono font-bold text-stone uppercase tracking-wider mb-1.5">Holding Period (Optional)</label>
                                <input type="text" id="holding_period" name="holding_period" oninput="updatePreview()" placeholder="2-3 Trading Days" class="notion-input text-xs">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-mono font-bold text-stone uppercase tracking-wider mb-1.5">Additional Technical Rationale / Notes (Optional)</label>
                            <textarea id="notes" name="notes" rows="2" oninput="updatePreview()" placeholder="Optional trade notes..." class="notion-input text-xs"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Select Template & WYSIWYG Inline Editor -->
                <div class="notion-card space-y-4">
                    <div class="pb-3 border-b border-black/10 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-ink-black flex items-center gap-2">
                            <i class="fa-solid fa-pen-nib text-notion-blue"></i>
                            3. Email Template &amp; WYSIWYG Inline Editor
                        </h3>
                        <a href="{{ route('templates.index') }}" target="_blank" class="tag-pill bg-sky-tint text-notion-blue hover:underline text-xs">
                            <i class="fa-solid fa-gear"></i> Manage Templates Deck
                        </a>
                    </div>

                    <!-- Template Selection Dropdown -->
                    <div>
                        <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1.5">
                            Choose Saved Email Template:
                        </label>
                        <select id="template_select" onchange="loadSelectedTemplate(this.value)" class="notion-input font-bold text-xs bg-sky-tint/30 text-notion-blue border-notion-blue/30">
                            <option value="">-- Choose Template --</option>
                            @foreach($templates as $tmpl)
                                <option value="{{ $tmpl->id }}" {{ $tmpl->is_default ? 'selected' : '' }}>
                                    {{ $tmpl->name }} {{ $tmpl->is_default ? '(Default)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Email Subject Line Section with Quick Tags -->
                    <div class="space-y-2">
                        <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider">
                            Email Subject Line (Editable) *
                        </label>
                        <input type="text" id="custom_subject" name="custom_subject" oninput="updatePreview()" value="Trade Instruction - {TRADE_TYPE} {STOCK_NAME}" class="notion-input font-mono font-bold text-xs text-notion-blue">
                        
                        <!-- Subject Line Quick Tag Chips -->
                        <div class="space-y-1">
                            <span class="text-[11px] font-mono text-stone font-bold">Click to Insert Dynamic Tag into Subject:</span>
                            <div class="flex flex-wrap items-center gap-1.5">
                                <button type="button" onclick="insertSubjectTag('{STOCK_NAME}')" class="tag-pill bg-amber-100 text-amber-900 hover:bg-amber-200 transition cursor-pointer text-[11px]">+{STOCK_NAME}</button>
                                <button type="button" onclick="insertSubjectTag('{TRADE_TYPE}')" class="tag-pill bg-amber-100 text-amber-900 hover:bg-amber-200 transition cursor-pointer text-[11px]">+{TRADE_TYPE}</button>
                                <button type="button" onclick="insertSubjectTag('{ENTRY_RANGE}')" class="tag-pill bg-amber-100 text-amber-900 hover:bg-amber-200 transition cursor-pointer text-[11px]">+{ENTRY_RANGE}</button>
                                <button type="button" onclick="insertSubjectTag('{STOP_LOSS}')" class="tag-pill bg-amber-100 text-amber-900 hover:bg-amber-200 transition cursor-pointer text-[11px]">+{STOP_LOSS}</button>
                                <button type="button" onclick="insertSubjectTag('{TARGET_1}')" class="tag-pill bg-amber-100 text-amber-900 hover:bg-amber-200 transition cursor-pointer text-[11px]">+{TARGET_1}</button>
                                <button type="button" onclick="insertSubjectTag('{TARGET_2}')" class="tag-pill bg-amber-100 text-amber-900 hover:bg-amber-200 transition cursor-pointer text-[11px]">+{TARGET_2}</button>
                                <button type="button" onclick="insertSubjectTag('{TARGET_3}')" class="tag-pill bg-amber-100 text-amber-900 hover:bg-amber-200 transition cursor-pointer text-[11px]">+{TARGET_3}</button>
                                <button type="button" onclick="insertSubjectTag('{PROFIT_BOOKING}')" class="tag-pill bg-amber-100 text-amber-900 hover:bg-amber-200 transition cursor-pointer text-[11px]">+{PROFIT_BOOKING}</button>
                                <button type="button" onclick="insertSubjectTag('{HOLDING_PERIOD}')" class="tag-pill bg-amber-100 text-amber-900 hover:bg-amber-200 transition cursor-pointer text-[11px]">+{HOLDING_PERIOD}</button>
                                <button type="button" onclick="insertSubjectTag('{CLIENT_NAME}')" class="tag-pill bg-amber-100 text-amber-900 hover:bg-amber-200 transition cursor-pointer text-[11px]">+{CLIENT_NAME}</button>
                                <button type="button" onclick="insertSubjectTag('{CLIENT_GMAIL}')" class="tag-pill bg-amber-100 text-amber-900 hover:bg-amber-200 transition cursor-pointer text-[11px]">+{CLIENT_GMAIL}</button>
                                <button type="button" onclick="insertSubjectTag('{NOTES}')" class="tag-pill bg-amber-100 text-amber-900 hover:bg-amber-200 transition cursor-pointer text-[11px]">+{NOTES}</button>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Insert Dynamic Variable Chips for Body -->
                    <div class="space-y-1.5 pt-2">
                        <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider">Click to Insert Dynamic Tag into WYSIWYG Body Editor:</label>
                        <div class="flex flex-wrap items-center gap-1.5">
                            <button type="button" onclick="insertBodyTag('{STOCK_NAME}')" class="tag-pill bg-sky-tint text-notion-blue border border-notion-blue/20 hover:bg-notion-blue hover:text-white transition cursor-pointer text-[11px] font-bold">+{STOCK_NAME}</button>
                            <button type="button" onclick="insertBodyTag('{TRADE_TYPE}')" class="tag-pill bg-sky-tint text-notion-blue border border-notion-blue/20 hover:bg-notion-blue hover:text-white transition cursor-pointer text-[11px] font-bold">+{TRADE_TYPE}</button>
                            <button type="button" onclick="insertBodyTag('{ENTRY_RANGE}')" class="tag-pill bg-sky-tint text-notion-blue border border-notion-blue/20 hover:bg-notion-blue hover:text-white transition cursor-pointer text-[11px] font-bold">+{ENTRY_RANGE}</button>
                            <button type="button" onclick="insertBodyTag('{STOP_LOSS}')" class="tag-pill bg-sky-tint text-notion-blue border border-notion-blue/20 hover:bg-notion-blue hover:text-white transition cursor-pointer text-[11px] font-bold">+{STOP_LOSS}</button>
                            <button type="button" onclick="insertBodyTag('{TARGET_1}')" class="tag-pill bg-sky-tint text-notion-blue border border-notion-blue/20 hover:bg-notion-blue hover:text-white transition cursor-pointer text-[11px] font-bold">+{TARGET_1}</button>
                            <button type="button" onclick="insertBodyTag('{TARGET_2}')" class="tag-pill bg-sky-tint text-notion-blue border border-notion-blue/20 hover:bg-notion-blue hover:text-white transition cursor-pointer text-[11px] font-bold">+{TARGET_2}</button>
                            <button type="button" onclick="insertBodyTag('{TARGET_3}')" class="tag-pill bg-sky-tint text-notion-blue border border-notion-blue/20 hover:bg-notion-blue hover:text-white transition cursor-pointer text-[11px] font-bold">+{TARGET_3}</button>
                            <button type="button" onclick="insertBodyTag('{PROFIT_BOOKING}')" class="tag-pill bg-sky-tint text-notion-blue border border-notion-blue/20 hover:bg-notion-blue hover:text-white transition cursor-pointer text-[11px] font-bold">+{PROFIT_BOOKING}</button>
                            <button type="button" onclick="insertBodyTag('{HOLDING_PERIOD}')" class="tag-pill bg-sky-tint text-notion-blue border border-notion-blue/20 hover:bg-notion-blue hover:text-white transition cursor-pointer text-[11px] font-bold">+{HOLDING_PERIOD}</button>
                            <button type="button" onclick="insertBodyTag('{CLIENT_NAME}')" class="tag-pill bg-sky-tint text-notion-blue border border-notion-blue/20 hover:bg-notion-blue hover:text-white transition cursor-pointer text-[11px] font-bold">+{CLIENT_NAME}</button>
                            <button type="button" onclick="insertBodyTag('{CLIENT_GMAIL}')" class="tag-pill bg-sky-tint text-notion-blue border border-notion-blue/20 hover:bg-notion-blue hover:text-white transition cursor-pointer text-[11px] font-bold">+{CLIENT_GMAIL}</button>
                            <button type="button" onclick="insertBodyTag('{NOTES}')" class="tag-pill bg-sky-tint text-notion-blue border border-notion-blue/20 hover:bg-notion-blue hover:text-white transition cursor-pointer text-[11px] font-bold">+{NOTES}</button>
                        </div>
                    </div>

                    <!-- Quill WYSIWYG Container -->
                    <div class="rounded-lg border border-black/15 overflow-hidden bg-white">
                        <div id="quillEditor" class="min-h-[220px] text-sm text-ink-black"></div>
                    </div>

                    <!-- Hidden inputs for submission -->
                    <input type="hidden" id="custom_body_html" name="custom_body_html">
                </div>

                <!-- Submit Button -->
                <button type="submit" id="sendBtn" class="btn-notion-primary w-full py-3 text-sm font-semibold">
                    <i class="fa-solid fa-paper-plane mr-2"></i> Send NSE Trade Email
                </button>
            </form>
        </div>

        <!-- Email Live Preview (5 Cols) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="notion-card sticky top-24 space-y-4">
                <div class="pb-3 border-b border-black/10 flex items-center justify-between">
                    <h3 class="text-base font-bold text-ink-black flex items-center gap-2">
                        <i class="fa-solid fa-envelope-circle-check text-notion-blue"></i>
                        Live Email Preview
                    </h3>
                    <span class="tag-pill bg-paper-warmth text-stone text-xs font-mono">WYSIWYG Render</span>
                </div>

                <div class="bg-paper-warmth rounded-lg p-3 border border-black/10 text-xs font-mono space-y-1.5">
                    <div class="flex"><span class="w-14 text-stone font-bold">FROM:</span> <span id="previewFrom" class="font-bold text-ink-black truncate">Select Client</span></div>
                    <div class="flex"><span class="w-14 text-stone font-bold">TO:</span> <span id="previewTo" class="text-ink-black truncate">{{ $defaultBrokerageEmail }}</span></div>
                    <div class="flex"><span class="w-14 text-stone font-bold">SUBJECT:</span> <span id="previewSubject" class="text-notion-blue font-bold truncate">Trade Instruction - BUY RELIANCE</span></div>
                </div>

                <div id="emailBodyPreview" class="bg-white rounded-lg p-4 border border-black/10 text-xs text-ink-black font-sans space-y-3 max-h-[560px] overflow-y-auto leading-relaxed">
                    <!-- Dynamic preview -->
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentMode = 'live';
    let quill;
    const templateDataMap = {};

    @foreach($templates as $tmpl)
        templateDataMap['{{ $tmpl->id }}'] = {
            name: @json($tmpl->name),
            subject: @json($tmpl->subject),
            body_html: @json($tmpl->body_html)
        };
    @endforeach

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Quill WYSIWYG Editor
        quill = new Quill('#quillEditor', {
            theme: 'snow',
            placeholder: 'Edit full email body HTML or template content here at sending time...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['blockquote', 'code-block'],
                    ['clean']
                ]
            }
        });

        // Load initial default template if available
        const defaultTmplSelect = document.getElementById('template_select');
        if (defaultTmplSelect.value && templateDataMap[defaultTmplSelect.value]) {
            loadSelectedTemplate(defaultTmplSelect.value);
        } else {
            quill.root.innerHTML = '<p>Dear Brokerage Team,</p><p>Please execute the trade for <strong>{STOCK_NAME}</strong> at entry range <strong>₹{ENTRY_RANGE}</strong>.</p><p>Regards,<br><strong>{CLIENT_NAME}</strong></p>';
        }

        // Realtime update on Quill text change
        quill.on('text-change', function() {
            document.getElementById('custom_body_html').value = quill.root.innerHTML;
            updatePreview();
        });

        loadStockData('RELIANCE');
    });

    function loadSelectedTemplate(templateId) {
        if (!templateId || !templateDataMap[templateId]) return;

        const tmpl = templateDataMap[templateId];
        document.getElementById('custom_subject').value = tmpl.subject;
        quill.root.innerHTML = tmpl.body_html;
        document.getElementById('custom_body_html').value = tmpl.body_html;
        updatePreview();
    }

    function insertSubjectTag(tag) {
        const input = document.getElementById('custom_subject');
        if (!input) return;

        const start = input.selectionStart;
        const end = input.selectionEnd;
        const text = input.value;
        
        input.value = text.substring(0, start) + tag + text.substring(end);
        input.selectionStart = input.selectionEnd = start + tag.length;
        input.focus();
        updatePreview();
    }

    function insertBodyTag(text) {
        if (!quill) return;
        const range = quill.getSelection(true);
        quill.insertText(range.index, text, 'user');
        quill.setSelection(range.index + text.length);
        updatePreview();
    }

    function setMode(mode) {
        currentMode = mode;
        const btnLive = document.getElementById('btnLiveMode');
        const btnManual = document.getElementById('btnManualMode');

        if (mode === 'live') {
            btnLive.className = 'btn-notion-primary text-xs py-1.5 px-3';
            btnManual.className = 'btn-notion-outlined text-xs py-1.5 px-3';
            document.getElementById('fetchTimeBadge').textContent = 'NSE REALTIME';
        } else {
            btnManual.className = 'btn-notion-primary text-xs py-1.5 px-3 bg-marigold text-black hover:bg-amber-400';
            btnLive.className = 'btn-notion-outlined text-xs py-1.5 px-3';
            document.getElementById('fetchTimeBadge').textContent = 'MANUAL MODEL';
        }
    }

    function loadStockData(symbol) {
        if (currentMode === 'manual') return;

        document.getElementById('fetchTimeBadge').textContent = 'FETCHING NSE...';

        window.ajaxFetch(`/send-mail/stock-details?symbol=${symbol}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data) {
                fillStockForm(data.data);
            }
        })
        .catch(err => {
            document.getElementById('fetchTimeBadge').textContent = 'MANUAL FALLBACK';
        });
    }

    function fetchCustomStock() {
        const sym = document.getElementById('custom_symbol').value.trim();
        if (!sym) return;
        loadStockData(sym);
    }

    function fillStockForm(stk) {
        document.getElementById('stock_name').value = stk.symbol;
        document.getElementById('entry_range').value = stk.entry_range || '';
        document.getElementById('trade_type').value = stk.trade_type || 'BUY';
        document.getElementById('stop_loss').value = stk.stop_loss || '';
        document.getElementById('target_1').value = stk.target_1 || '';
        document.getElementById('target_2').value = stk.target_2 || '';
        document.getElementById('target_3').value = stk.target_3 || '';
        document.getElementById('profit_booking').value = stk.profit_booking || '';
        document.getElementById('holding_period').value = stk.holding_period || '';
        
        if (stk.research_notes) {
            document.getElementById('notes').value = stk.research_notes;
        }

        if (stk.live_price) {
            document.getElementById('livePriceText').textContent = `${stk.currency || '₹'}${stk.live_price}`;
            document.getElementById('liveChangeText').textContent = `${stk.change_percent >= 0 ? '+' : ''}${stk.change_percent}% ${stk.change_percent >= 0 ? '▲' : '▼'}`;
            document.getElementById('liveExchangeText').textContent = `${stk.exchange || 'NSE INDIA'} REAL-TIME QUOTE`;
            document.getElementById('liveSymbolBadge').textContent = `${stk.symbol}.NS`;
            document.getElementById('fetchTimeBadge').textContent = stk.is_live ? `NSE Verified (${stk.fetched_at})` : 'Manual Model';
        }

        updatePreview();
    }

    function updatePreview() {
        const customerSelect = document.getElementById('customer_id');
        const selectedOpt = customerSelect.options[customerSelect.selectedIndex];
        
        const clientName = selectedOpt && selectedOpt.dataset.name ? selectedOpt.dataset.name : '[Select Client Name]';
        const clientGmail = selectedOpt && selectedOpt.dataset.gmail ? selectedOpt.dataset.gmail : '[client@gmail.com]';
        const recipient = document.getElementById('recipient_email').value || '[Brokerage Email]';

        const stockName = document.getElementById('stock_name').value.toUpperCase() || '[STOCK_NAME]';
        const tradeType = document.getElementById('trade_type').value || 'BUY';
        const entryRange = document.getElementById('entry_range').value || '[ENTRY_RANGE]';
        const stopLoss = document.getElementById('stop_loss').value || 'N/A';
        const t1 = document.getElementById('target_1').value || 'N/A';
        const t2 = document.getElementById('target_2').value || 'N/A';
        const t3 = document.getElementById('target_3').value || 'N/A';
        const profitBooking = document.getElementById('profit_booking').value || 'N/A';
        const holdingPeriod = document.getElementById('holding_period').value || 'N/A';
        const notes = document.getElementById('notes').value || '';

        let subject = document.getElementById('custom_subject').value || `Trade Instruction - ${tradeType} ${stockName}`;
        subject = subject.replace(/\{STOCK_NAME\}/g, stockName)
                         .replace(/\{TRADE_TYPE\}/g, tradeType)
                         .replace(/\{ENTRY_RANGE\}/g, entryRange)
                         .replace(/\{STOP_LOSS\}/g, stopLoss)
                         .replace(/\{TARGET_1\}/g, t1)
                         .replace(/\{TARGET_2\}/g, t2)
                         .replace(/\{TARGET_3\}/g, t3)
                         .replace(/\{PROFIT_BOOKING\}/g, profitBooking)
                         .replace(/\{HOLDING_PERIOD\}/g, holdingPeriod)
                         .replace(/\{CLIENT_NAME\}/g, clientName)
                         .replace(/\{CLIENT_GMAIL\}/g, clientGmail)
                         .replace(/\{NOTES\}/g, notes);

        document.getElementById('previewFrom').textContent = `${clientName} <${clientGmail}>`;
        document.getElementById('previewTo').textContent = recipient;
        document.getElementById('previewSubject').textContent = subject;

        let wysiwygHtml = quill ? quill.root.innerHTML : '';
        
        // Perform live placeholder interpolation for real-time preview
        wysiwygHtml = wysiwygHtml.replace(/\{STOCK_NAME\}/g, stockName)
                                 .replace(/\{TRADE_TYPE\}/g, tradeType)
                                 .replace(/\{ENTRY_RANGE\}/g, entryRange)
                                 .replace(/\{STOP_LOSS\}/g, stopLoss)
                                 .replace(/\{TARGET_1\}/g, t1)
                                 .replace(/\{TARGET_2\}/g, t2)
                                 .replace(/\{TARGET_3\}/g, t3)
                                 .replace(/\{PROFIT_BOOKING\}/g, profitBooking)
                                 .replace(/\{HOLDING_PERIOD\}/g, holdingPeriod)
                                 .replace(/\{CLIENT_NAME\}/g, clientName)
                                 .replace(/\{CLIENT_GMAIL\}/g, clientGmail)
                                 .replace(/\{NOTES\}/g, notes);

        document.getElementById('emailBodyPreview').innerHTML = wysiwygHtml;
    }

    function handleSendMail(e) {
        e.preventDefault();

        if (quill) {
            document.getElementById('custom_body_html').value = quill.root.innerHTML;
        }

        const form = e.target;
        const formData = new FormData(form);
        const sendBtn = document.getElementById('sendBtn');

        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-sm"></i> Authenticating SMTP &amp; Dispatching...';

        Swal.fire({
            title: 'Sending Trade Mail...',
            text: 'Connecting to client Gmail SMTP relay...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); },
            background: '#ffffff',
            color: '#000000'
        });

        fetch('{{ route("send-mail.send") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fa-solid fa-paper-plane mr-2"></i> Send NSE Trade Email';

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Email Dispatched!',
                    text: data.message,
                    confirmButtonColor: '#0075de',
                    background: '#ffffff',
                    color: '#000000'
                }).then(() => {
                    window.location.href = "{{ route('email-logs.index') }}";
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Dispatch Failed',
                    text: data.message,
                    confirmButtonColor: '#0075de',
                    background: '#ffffff',
                    color: '#000000'
                });
            }
        })
        .catch(err => {
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fa-solid fa-paper-plane mr-2"></i> Send NSE Trade Email';
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An unexpected error occurred.',
                background: '#ffffff',
                color: '#000000'
            });
        });
    }
</script>
@endpush
