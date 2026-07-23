@extends('layouts.app')

@section('title', 'Client Accounts — Notion TradeDesk')

@section('content')
<div class="space-y-6">
    
    <!-- Action Header -->
    <div class="notion-card flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="tag-pill bg-marigold text-black font-semibold mb-2 inline-block">CLIENT GMAIL CREDENTIALS</span>
            <h1 class="text-3xl font-bold tracking-tight text-ink-black">Client Gmail Accounts</h1>
            <p class="text-xs text-graphite">Manage client App Passwords and test real-time SMTP connections</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('customers.sample-csv') }}" class="btn-notion-outlined text-xs">
                <i class="fa-solid fa-file-arrow-down text-notion-blue mr-1"></i> Sample CSV Template
            </a>
            <button onclick="openImportModal()" class="btn-notion-outlined text-xs">
                <i class="fa-solid fa-file-csv text-notion-blue mr-1"></i> Import CSV
            </button>
            <button onclick="openAddCustomerModal()" class="btn-notion-primary text-xs">
                <i class="fa-solid fa-plus text-xs mr-1"></i> Add Client Account
            </button>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="notion-card py-3">
        <form method="GET" action="{{ route('customers.index') }}" class="flex flex-col md:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-stone text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search client name, Gmail, mobile, or broker code..." class="notion-input pl-10 text-xs">
            </div>

            <div class="flex items-center gap-2.5 w-full md:w-auto">
                <select name="status" onchange="this.form.submit()" class="notion-input w-full md:w-40 text-xs font-medium">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                </select>

                @if(request('search') || request('status'))
                    <a href="{{ route('customers.index') }}" class="btn-notion-outlined text-xs py-2 whitespace-nowrap">
                        Reset Filter
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="notion-card overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse font-sans">
                <thead>
                    <tr class="bg-paper-warmth border-b border-black/10 text-xs font-mono text-stone uppercase">
                        <th class="py-3.5 px-4">Client Name</th>
                        <th class="py-3.5 px-4">Gmail Address</th>
                        <th class="py-3.5 px-4">App Password</th>
                        <th class="py-3.5 px-4">Mobile</th>
                        <th class="py-3.5 px-4">Broker Code</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Dispatches</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 text-xs font-medium">
                    @forelse($customers as $customer)
                    <tr class="hover:bg-paper-warmth/60 transition">
                        <td class="py-3.5 px-4 font-bold text-ink-black">
                            {{ $customer->name }}
                        </td>
                        <td class="py-3.5 px-4 font-mono text-notion-blue">
                            {{ $customer->gmail }}
                        </td>
                        <td class="py-3.5 px-4 font-mono text-stone">
                            <span class="bg-paper-warmth px-2.5 py-1 rounded border border-black/10 text-ink-black">
                                {{ $customer->app_password }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-graphite">
                            {{ $customer->mobile }}
                        </td>
                        <td class="py-3.5 px-4 font-mono text-stone">
                            {{ $customer->broker_code ?: 'N/A' }}
                        </td>
                        <td class="py-3.5 px-4">
                            <button onclick="toggleCustomerStatus({{ $customer->id }}, this)" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-mono font-bold transition {{ $customer->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $customer->status === 'active' ? 'bg-emerald-600' : 'bg-slate-500' }}"></span>
                                <span>{{ ucfirst($customer->status) }}</span>
                            </button>
                        </td>
                        <td class="py-3.5 px-4 font-mono font-bold text-ink-black">
                            <span class="tag-pill bg-sky-tint text-notion-blue">
                                {{ $customer->email_logs_count }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button onclick="testSmtpConnection({{ $customer->id }}, '{{ addslashes($customer->name) }}', this)" title="Test Gmail Connection" class="btn-notion-ghost text-xs p-1.5">
                                    <i class="fa-solid fa-plug text-xs"></i>
                                </button>
                                <a href="{{ route('send-mail.index', ['customer_id' => $customer->id]) }}" title="Send Mail using this account" class="btn-notion-primary text-xs p-1.5">
                                    <i class="fa-solid fa-paper-plane text-xs"></i>
                                </a>
                                <button onclick="openEditCustomerModal({{ json_encode($customer) }})" title="Edit Customer" class="btn-notion-outlined text-xs p-1.5">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>
                                <button onclick="deleteCustomer({{ $customer->id }}, '{{ addslashes($customer->name) }}')" title="Delete Customer" class="btn-notion-ghost text-xs p-1.5 text-rose-600 hover:bg-rose-50">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-stone font-mono text-xs">
                            No customer accounts found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="p-3 border-t border-black/10 font-mono">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Add Customer Modal -->
<div id="addCustomerModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="notion-card w-full max-w-lg space-y-5 relative shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-black/10">
            <h3 class="text-lg font-bold text-ink-black">Add New Client Account</h3>
            <button onclick="closeAddCustomerModal()" class="text-stone hover:text-ink-black">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="addCustomerForm" action="{{ route('customers.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1">Client Name *</label>
                <input type="text" name="name" required placeholder="e.g. Rajesh Sharma" class="notion-input">
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1">Gmail Address *</label>
                <input type="email" name="gmail" required placeholder="rajesh.sharma@gmail.com" class="notion-input font-mono">
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1">Gmail App Password *</label>
                <input type="text" name="app_password" required placeholder="abcd efgh ijkl mnop" class="notion-input font-mono text-notion-blue font-bold">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1">Mobile Number *</label>
                    <input type="text" name="mobile" required placeholder="+91 98765 43210" class="notion-input">
                </div>

                <div>
                    <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1">Broker Code</label>
                    <input type="text" name="broker_code" placeholder="BRK-8821" class="notion-input">
                </div>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1">Status</label>
                <select name="status" class="notion-input font-mono">
                    <option value="active">Active (Ready for email sending)</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-black/10">
                <button type="button" onclick="closeAddCustomerModal()" class="btn-notion-outlined text-xs">Cancel</button>
                <button type="submit" class="btn-notion-primary text-xs">Save Client</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Customer Modal -->
<div id="editCustomerModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="notion-card w-full max-w-lg space-y-5 relative shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-black/10">
            <h3 class="text-lg font-bold text-ink-black">Edit Client Account</h3>
            <button onclick="closeEditCustomerModal()" class="text-stone hover:text-ink-black">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="editCustomerForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_customer_id">

            <div>
                <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1">Client Name *</label>
                <input type="text" id="edit_name" name="name" required class="notion-input">
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1">Gmail Address *</label>
                <input type="email" id="edit_gmail" name="gmail" required class="notion-input font-mono">
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1">Gmail App Password *</label>
                <input type="text" id="edit_app_password" name="app_password" required class="notion-input font-mono text-notion-blue font-bold">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1">Mobile Number *</label>
                    <input type="text" id="edit_mobile" name="mobile" required class="notion-input">
                </div>

                <div>
                    <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1">Broker Code</label>
                    <input type="text" id="edit_broker_code" name="broker_code" class="notion-input">
                </div>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1">Status</label>
                <select id="edit_status" name="status" class="notion-input font-mono">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-black/10">
                <button type="button" onclick="closeEditCustomerModal()" class="btn-notion-outlined text-xs">Cancel</button>
                <button type="submit" class="btn-notion-primary text-xs">Update Client</button>
            </div>
        </form>
    </div>
</div>

<!-- Import CSV Modal -->
<div id="importModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="notion-card w-full max-w-md space-y-5 relative shadow-xl">
        <div class="flex items-center justify-between pb-3 border-b border-black/10">
            <h3 class="text-lg font-bold text-ink-black flex items-center gap-2">
                <i class="fa-solid fa-file-csv text-notion-blue"></i> Import Clients CSV / Excel
            </h3>
            <button onclick="closeImportModal()" class="text-stone hover:text-ink-black">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('customers.import-csv') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-2">Select CSV File *</label>
                <input type="file" name="csv_file" accept=".csv, .txt" required class="notion-input text-xs">
            </div>

            <div class="p-3.5 bg-paper-warmth rounded-lg border border-black/10 text-xs font-mono text-ink-black space-y-2">
                <div class="flex items-center justify-between">
                    <p class="font-bold">Required CSV Columns:</p>
                    <a href="{{ route('customers.sample-csv') }}" class="text-notion-blue hover:underline font-bold text-[11px] flex items-center gap-1">
                        <i class="fa-solid fa-download"></i> Download Sample Template
                    </a>
                </div>
                <p class="text-notion-blue font-bold">Name, Gmail, AppPassword, Mobile, BrokerCode</p>
                <p class="text-[11px] text-stone font-sans">Download the sample template file above to easily populate your client credentials in the exact required format.</p>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-black/10">
                <a href="{{ route('customers.sample-csv') }}" class="btn-notion-outlined text-xs">
                    <i class="fa-solid fa-file-arrow-down mr-1 text-notion-blue"></i> Download CSV Template
                </a>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeImportModal()" class="btn-notion-outlined text-xs">Cancel</button>
                    <button type="submit" class="btn-notion-primary text-xs">Import Now</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openAddCustomerModal() { document.getElementById('addCustomerModal').classList.remove('hidden'); }
    function closeAddCustomerModal() { document.getElementById('addCustomerModal').classList.add('hidden'); }

    function openEditCustomerModal(customer) {
        document.getElementById('edit_customer_id').value = customer.id;
        document.getElementById('edit_name').value = customer.name;
        document.getElementById('edit_gmail').value = customer.gmail;
        document.getElementById('edit_app_password').value = customer.app_password;
        document.getElementById('edit_mobile').value = customer.mobile;
        document.getElementById('edit_broker_code').value = customer.broker_code || '';
        document.getElementById('edit_status').value = customer.status;

        const form = document.getElementById('editCustomerForm');
        form.action = `/customers/${customer.id}`;

        document.getElementById('editCustomerModal').classList.remove('hidden');
    }
    function closeEditCustomerModal() { document.getElementById('editCustomerModal').classList.add('hidden'); }

    function openImportModal() { document.getElementById('importModal').classList.remove('hidden'); }
    function closeImportModal() { document.getElementById('importModal').classList.add('hidden'); }

    function toggleCustomerStatus(id, btn) {
        window.ajaxFetch(`/customers/${id}/toggle-status`, { method: 'POST' })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Status Updated',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false,
                    background: '#ffffff',
                    color: '#000000'
                }).then(() => window.location.reload());
            }
        });
    }

    function testSmtpConnection(id, name, btn) {
        Swal.fire({
            title: 'Testing Gmail Connection...',
            text: `Authenticating SMTP credentials for ${name}`,
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); },
            background: '#ffffff',
            color: '#000000'
        });

        window.ajaxFetch(`/customers/${id}/test-smtp`, { method: 'POST' })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Gmail SMTP Verified!',
                    text: data.message,
                    confirmButtonColor: '#0075de',
                    background: '#ffffff',
                    color: '#000000'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Failed',
                    text: data.message,
                    confirmButtonColor: '#0075de',
                    background: '#ffffff',
                    color: '#000000'
                });
            }
        });
    }

    function deleteCustomer(id, name) {
        Swal.fire({
            title: 'Delete Client Account?',
            text: `Are you sure you want to remove ${name}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f64932',
            cancelButtonColor: '#000000',
            confirmButtonText: 'Yes, Delete',
            background: '#ffffff',
            color: '#000000'
        }).then((result) => {
            if (result.isConfirmed) {
                window.ajaxFetch(`/customers/${id}`, { method: 'DELETE' })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false,
                            background: '#ffffff',
                            color: '#000000'
                        }).then(() => window.location.reload());
                    }
                });
            }
        });
    }
</script>
@endpush
