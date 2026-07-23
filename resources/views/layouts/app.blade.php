<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Notion TradeDesk') — NSE Brokerage</title>

    <!-- Google Fonts: Inter (NotionInter), Source Serif Pro (Lyon Text editorial replacement), JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Source+Serif+4:ital,wght@0,400;0,600;1,400&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Global Helper: window.ajaxFetch definition FIRST -->
    <script>
        window.ajaxFetch = function(url, options = {}) {
            options.headers = {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(options.headers || {})
            };
            return fetch(url, options);
        };

        // Tailwind Config Script BEFORE Tailwind CDN
        window.tailwind = {
            theme: {
                extend: {
                    colors: {
                        'notion-blue': '#0075de',
                        'paper-warmth': '#f6f5f4',
                        'pure-white': '#ffffff',
                        'ink-black': '#000000',
                        'charcoal': '#111111',
                        'stone': '#757575',
                        'graphite': '#615d59',
                        'slate': '#696969',
                        'sky-tint': '#e6f3fe',
                        'marigold': '#ffb110',
                        'coral': '#f64932',
                        'saffron': '#e89d01',
                        'mocha': '#b18164',
                        'signal-blue': '#097fe8',
                        'sky-wash': '#62aef0',
                        'midnight-ink': '#02093a',
                    },
                    fontFamily: {
                        sans: ['"Inter"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        serif: ['"Source Serif 4"', 'Georgia', 'serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    borderRadius: {
                        'card': '12px',
                        'pill': '9999px',
                        'button': '8px',
                        'small': '4px',
                    }
                }
            }
        };
    </script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Quill.js WYSIWYG Editor CDN -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

    <style>
        :root {
            --color-notion-blue: #0075de;
            --color-paper-warmth: #f6f5f4;
            --color-pure-white: #ffffff;
            --color-ink-black: #000000;
            --color-stone: #757575;
            --color-graphite: #615d59;
            --color-sky-tint: #e6f3fe;
            --color-marigold: #ffb110;
            --color-coral: #f64932;
            --color-midnight-ink: #02093a;

            --font-sans: 'Inter', sans-serif;
            --font-serif: 'Source Serif 4', serif;
        }

        body {
            background-color: var(--color-paper-warmth);
            color: var(--color-ink-black);
            font-family: var(--font-sans);
            font-weight: 400;
            margin: 0;
            padding: 0;
        }

        /* Headings: Tight negative tracking */
        h1, h2, h3, h4, .font-heading {
            font-family: var(--font-sans);
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1.1;
        }

        /* Editorial Subhead / Pull quote */
        .font-lyon {
            font-family: var(--font-serif);
            font-weight: 400;
            color: var(--color-graphite);
        }

        /* Notion White Card: Pure White on Warm Paper, 1px Hairline Border, 12px Corners, No Shadows */
        .notion-card {
            background-color: var(--color-pure-white);
            border-radius: 12px;
            padding: 24px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: none !important;
        }

        /* Accent Feature Cards */
        .notion-card-marigold {
            background-color: var(--color-marigold);
            color: var(--color-ink-black);
            border-radius: 12px;
            padding: 24px;
            box-shadow: none !important;
        }

        .notion-card-coral {
            background-color: var(--color-coral);
            color: #ffffff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: none !important;
        }

        .notion-card-midnight {
            background-color: var(--color-midnight-ink);
            color: #ffffff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: none !important;
        }

        /* Notion Buttons */
        .btn-notion-primary {
            background-color: var(--color-notion-blue);
            color: #ffffff;
            border-radius: 8px;
            padding: 6px 16px;
            font-family: var(--font-sans);
            font-weight: 500;
            font-size: 14px;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background-color 0.15s ease, transform 0.1s ease;
            box-shadow: none !important;
            cursor: pointer;
        }
        .btn-notion-primary:hover {
            background-color: #0060b8;
        }

        .btn-notion-ghost {
            background-color: var(--color-sky-tint);
            color: var(--color-notion-blue);
            border-radius: 8px;
            padding: 6px 16px;
            font-family: var(--font-sans);
            font-weight: 500;
            font-size: 14px;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background-color 0.15s ease;
            cursor: pointer;
        }
        .btn-notion-ghost:hover {
            background-color: #d6ebfe;
        }

        .btn-notion-outlined {
            background-color: transparent;
            color: rgba(0, 0, 0, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.15);
            border-radius: 6px;
            padding: 5px 12px;
            font-family: var(--font-sans);
            font-weight: 500;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background-color 0.15s ease;
            cursor: pointer;
        }
        .btn-notion-outlined:hover {
            background-color: rgba(0, 0, 0, 0.04);
        }

        /* Notion Hero Highlight Pill */
        .highlight-pill-marigold {
            background-color: var(--color-marigold);
            color: var(--color-ink-black);
            border-radius: 9999px;
            padding: 4px 16px;
            display: inline-block;
        }

        .highlight-pill-coral {
            background-color: var(--color-coral);
            color: #ffffff;
            border-radius: 9999px;
            padding: 4px 16px;
            display: inline-block;
        }

        .highlight-pill-sky {
            background-color: var(--color-sky-wash);
            color: var(--color-ink-black);
            border-radius: 9999px;
            padding: 4px 16px;
            display: inline-block;
        }

        .tag-pill {
            border-radius: 9999px;
            padding: 2px 10px;
            font-size: 12px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* Inputs */
        .notion-input {
            background-color: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.12);
            border-radius: 8px;
            padding: 10px 14px;
            font-family: var(--font-sans);
            font-size: 14px;
            color: var(--color-ink-black);
            outline: none;
            width: 100%;
            transition: border-color 0.15s ease;
        }
        .notion-input:focus {
            border-color: var(--color-notion-blue);
            box-shadow: 0 0 0 2px rgba(0, 117, 222, 0.15);
        }

        /* Marquee Ticker */
        @keyframes marquee {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee {
            display: flex;
            width: 200%;
            animation: marquee 30s linear infinite;
        }
        .animate-marquee:hover {
            animation-play-state: paused;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col selection:bg-sky-tint selection:text-notion-blue">

    <!-- Sticky Top Wrapper (Ticker + Navbar pinned on scroll) -->
    <div class="sticky top-0 z-50">
        <!-- Top Ticker Ribbon (TradingView Style Infinite NSE Ticker) -->
        <div class="bg-[#111111] text-white border-b border-white/10 py-2.5 overflow-hidden text-xs font-mono shadow-md">
            <div class="max-w-[1440px] mx-auto px-4 flex items-center gap-3">
                <span class="tag-pill bg-[#ffb110] text-black font-extrabold text-[11px] shrink-0 tracking-wider flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> NSE REALTIME
                </span>
                <div class="overflow-hidden relative w-full">
                    <div class="animate-marquee whitespace-nowrap flex items-center gap-8 text-white">
                        <!-- Track Set 1 -->
                        <div class="flex items-center gap-8">
                            <span class="inline-flex items-center gap-1.5"><strong class="text-amber-400 font-bold">NIFTY 50</strong> <span class="text-slate-200">24,580.40</span> <span class="text-emerald-400 font-extrabold">+112.50 (+0.46%) ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-amber-400 font-bold">BANKNIFTY</strong> <span class="text-slate-200">52,340.10</span> <span class="text-emerald-400 font-extrabold">+285.30 (+0.55%) ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">RELIANCE.NS</strong> <span class="text-slate-200">₹1,284.90</span> <span class="text-emerald-400 font-extrabold">+1.45% ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">TCS.NS</strong> <span class="text-slate-200">₹2,215.90</span> <span class="text-emerald-400 font-extrabold">+0.85% ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">HDFCBANK.NS</strong> <span class="text-slate-200">₹1,642.00</span> <span class="text-rose-400 font-extrabold">-0.35% ▼</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">ICICIBANK.NS</strong> <span class="text-slate-200">₹1,230.50</span> <span class="text-emerald-400 font-extrabold">+1.90% ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">INFY.NS</strong> <span class="text-slate-200">₹1,846.00</span> <span class="text-rose-400 font-extrabold">-0.40% ▼</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">SBIN.NS</strong> <span class="text-slate-200">₹845.20</span> <span class="text-emerald-400 font-extrabold">+2.10% ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">BHARTIARTL.NS</strong> <span class="text-slate-200">₹1,480.00</span> <span class="text-emerald-400 font-extrabold">+1.15% ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">ITC.NS</strong> <span class="text-slate-200">₹472.30</span> <span class="text-emerald-400 font-extrabold">+0.60% ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">LT.NS</strong> <span class="text-slate-200">₹3,610.00</span> <span class="text-rose-400 font-extrabold">-0.80% ▼</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">KOTAKBANK.NS</strong> <span class="text-slate-200">₹1,790.00</span> <span class="text-emerald-400 font-extrabold">+0.45% ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">TATAMOTORS.NS</strong> <span class="text-slate-200">₹1,010.50</span> <span class="text-emerald-400 font-extrabold">+3.20% ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">TATASTEEL.NS</strong> <span class="text-slate-200">₹168.40</span> <span class="text-rose-400 font-extrabold">-1.10% ▼</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">AXISBANK.NS</strong> <span class="text-slate-200">₹1,240.00</span> <span class="text-emerald-400 font-extrabold">+1.05% ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">ASIANPAINT.NS</strong> <span class="text-slate-200">₹2,910.00</span> <span class="text-rose-400 font-extrabold">-0.25% ▼</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">MARUTI.NS</strong> <span class="text-slate-200">₹12,450.00</span> <span class="text-emerald-400 font-extrabold">+1.80% ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">HCLTECH.NS</strong> <span class="text-slate-200">₹1,580.00</span> <span class="text-emerald-400 font-extrabold">+0.90% ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">SUNPHARMA.NS</strong> <span class="text-slate-200">₹1,710.00</span> <span class="text-emerald-400 font-extrabold">+0.75% ▲</span></span>
                        </div>

                        <!-- Track Set 2 (Seamless Infinite Loop) -->
                        <div class="flex items-center gap-8">
                            <span class="inline-flex items-center gap-1.5"><strong class="text-amber-400 font-bold">NIFTY 50</strong> <span class="text-slate-200">24,580.40</span> <span class="text-emerald-400 font-extrabold">+112.50 (+0.46%) ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-amber-400 font-bold">BANKNIFTY</strong> <span class="text-slate-200">52,340.10</span> <span class="text-emerald-400 font-bold">+285.30 (+0.55%) ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">RELIANCE.NS</strong> <span class="text-slate-200">₹1,284.90</span> <span class="text-emerald-400 font-extrabold">+1.45% ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">TCS.NS</strong> <span class="text-slate-200">₹2,215.90</span> <span class="text-emerald-400 font-extrabold">+0.85% ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">HDFCBANK.NS</strong> <span class="text-slate-200">₹1,642.00</span> <span class="text-rose-400 font-extrabold">-0.35% ▼</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">ICICIBANK.NS</strong> <span class="text-slate-200">₹1,230.50</span> <span class="text-emerald-400 font-extrabold">+1.90% ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">INFY.NS</strong> <span class="text-slate-200">₹1,846.00</span> <span class="text-rose-400 font-extrabold">-0.40% ▼</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">SBIN.NS</strong> <span class="text-slate-200">₹845.20</span> <span class="text-emerald-400 font-extrabold">+2.10% ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">BHARTIARTL.NS</strong> <span class="text-slate-200">₹1,480.00</span> <span class="text-emerald-400 font-extrabold">+1.15% ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">ITC.NS</strong> <span class="text-slate-200">₹472.30</span> <span class="text-emerald-400 font-extrabold">+0.60% ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">LT.NS</strong> <span class="text-slate-200">₹3,610.00</span> <span class="text-rose-400 font-extrabold">-0.80% ▼</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">KOTAKBANK.NS</strong> <span class="text-slate-200">₹1,790.00</span> <span class="text-emerald-400 font-extrabold">+0.45% ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">TATAMOTORS.NS</strong> <span class="text-slate-200">₹1,010.50</span> <span class="text-emerald-400 font-extrabold">+3.20% ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">TATASTEEL.NS</strong> <span class="text-slate-200">₹168.40</span> <span class="text-rose-400 font-extrabold">-1.10% ▼</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">AXISBANK.NS</strong> <span class="text-slate-200">₹1,240.00</span> <span class="text-emerald-400 font-extrabold">+1.05% ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">ASIANPAINT.NS</strong> <span class="text-slate-200">₹2,910.00</span> <span class="text-rose-400 font-extrabold">-0.25% ▼</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">MARUTI.NS</strong> <span class="text-slate-200">₹12,450.00</span> <span class="text-emerald-400 font-extrabold">+1.80% ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">HCLTECH.NS</strong> <span class="text-slate-200">₹1,580.00</span> <span class="text-emerald-400 font-extrabold">+0.90% ▲</span></span>
                            <span class="inline-flex items-center gap-1.5"><strong class="text-white font-bold">SUNPHARMA.NS</strong> <span class="text-slate-200">₹1,710.00</span> <span class="text-emerald-400 font-extrabold">+0.75% ▲</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notion Fixed Header Navigation -->
        <header class="bg-[#f6f5f4]/95 backdrop-blur-md border-b border-black/10 shadow-xs transition-all">
            <div class="max-w-[1440px] mx-auto px-6 h-16 flex items-center justify-between">
                
                <!-- Logo Lockup -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-black text-white flex items-center justify-center font-bold text-sm">
                        N
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="font-bold text-lg text-ink-black tracking-tight">TradeDesk</span>
                        <span class="text-xs font-mono text-stone">Notion Edition</span>
                    </div>
                </a>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('dashboard') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-black/70 hover:text-black hover:bg-black/5 transition {{ request()->routeIs('dashboard') ? 'bg-black/5 text-black font-semibold' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('customers.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-black/70 hover:text-black hover:bg-black/5 transition {{ request()->routeIs('customers.*') ? 'bg-black/5 text-black font-semibold' : '' }}">
                        Clients
                    </a>
                    <a href="{{ route('send-mail.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-black/70 hover:text-black hover:bg-black/5 transition {{ request()->routeIs('send-mail.*') ? 'bg-black/5 text-black font-semibold' : '' }}">
                        Send NSE Mail
                    </a>
                    <a href="{{ route('templates.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-black/70 hover:text-black hover:bg-black/5 transition {{ request()->routeIs('templates.*') ? 'bg-black/5 text-black font-semibold' : '' }}">
                        Templates Deck
                    </a>
                    <a href="{{ route('email-logs.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-black/70 hover:text-black hover:bg-black/5 transition {{ request()->routeIs('email-logs.*') ? 'bg-black/5 text-black font-semibold' : '' }}">
                        Audit Logs
                    </a>
                    <a href="{{ route('settings.index') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-medium text-black/70 hover:text-black hover:bg-black/5 transition {{ request()->routeIs('settings.*') ? 'bg-black/5 text-black font-semibold' : '' }}">
                        Settings
                    </a>
                </nav>

                <!-- Actions -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('send-mail.index') }}" class="btn-notion-primary">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                        <span>Send Trade Email</span>
                    </a>
                </div>
            </div>
        </header>
    </div>

    <!-- Main Page Wrapper -->
    <main class="flex-1 max-w-[1440px] w-full mx-auto p-6 md:p-10 space-y-8">
        
        @if(session('success'))
            <div class="p-4 rounded-card bg-sky-tint border border-notion-blue/20 text-notion-blue text-sm font-medium flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <span class="tag-pill bg-notion-blue text-white text-[11px]">Success</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-card bg-rose-100 border border-rose-300 text-rose-800 text-sm font-medium flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <span class="tag-pill bg-rose-600 text-white text-[11px]">Error</span>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Notion Footer -->
    <footer class="mt-16 py-8 px-6 border-t border-black/10 text-xs font-medium text-stone max-w-[1440px] mx-auto w-full flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <span class="font-bold text-ink-black">Notion TradeDesk</span> — Multi-Gmail Brokerage System
        </div>
        <div class="flex items-center gap-4 text-graphite">
            <span>NSE Live API</span>
            <span>•</span>
            <span>Gmail SMTP App Passwords</span>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
