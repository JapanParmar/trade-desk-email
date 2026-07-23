@extends('layouts.app')

@section('title', 'Email Templates Deck — Notion TradeDesk')

@section('content')
<div class="space-y-6">
    
    <!-- Action Header -->
    <div class="notion-card flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="tag-pill bg-marigold text-black font-semibold mb-2 inline-block">WYSIWYG TEMPLATE BUILDER</span>
            <h1 class="text-3xl font-bold tracking-tight text-ink-black">Email Templates Deck</h1>
            <p class="text-xs text-graphite">Create and store reusable rich HTML email templates with dynamic placeholders</p>
        </div>

        <button onclick="openCreateTemplateModal()" class="btn-notion-primary text-xs">
            <i class="fa-solid fa-plus text-xs mr-1"></i> Create New Template
        </button>
    </div>

    <!-- Search & Filters -->
    <div class="notion-card py-3">
        <form method="GET" action="{{ route('templates.index') }}" class="flex items-center gap-3">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-stone text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search templates by title or subject..." class="notion-input pl-10 text-xs">
            </div>
            @if(request('search'))
                <a href="{{ route('templates.index') }}" class="btn-notion-outlined text-xs py-2 whitespace-nowrap">
                    Reset Filter
                </a>
            @endif
        </form>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($templates as $template)
        <div class="notion-card flex flex-col justify-between space-y-4 relative group">
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="font-mono text-xs text-stone font-bold">#TMP-{{ $template->id }}</span>
                    @if($template->is_default)
                        <span class="tag-pill bg-emerald-100 text-emerald-800 text-[11px] font-bold">Default</span>
                    @endif
                </div>

                <h3 class="text-lg font-bold text-ink-black group-hover:text-notion-blue transition">
                    {{ $template->name }}
                </h3>

                <p class="text-xs font-mono text-stone truncate">
                    <strong>Subject:</strong> {{ $template->subject }}
                </p>

                <div class="p-3 bg-paper-warmth rounded-lg border border-black/10 text-xs text-graphite font-sans max-h-32 overflow-hidden leading-relaxed">
                    {!! $template->body_html !!}
                </div>
            </div>

            <div class="pt-3 border-t border-black/10 flex items-center justify-between">
                <a href="{{ route('send-mail.index', ['template_id' => $template->id]) }}" class="btn-notion-ghost text-xs py-1 px-3">
                    <i class="fa-solid fa-paper-plane mr-1 text-xs"></i> Use to Send
                </a>

                <div class="flex items-center gap-1.5">
                    <button onclick="openEditTemplateModal({{ json_encode($template) }})" class="btn-notion-outlined text-xs p-1.5" title="Edit Template">
                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                    </button>
                    <button onclick="deleteTemplate({{ $template->id }}, '{{ addslashes($template->name) }}')" class="btn-notion-ghost text-xs p-1.5 text-rose-600 hover:bg-rose-50" title="Delete Template">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full notion-card py-12 text-center text-stone space-y-3">
            <i class="fa-solid fa-layer-group text-3xl"></i>
            <p class="text-sm font-medium">No email templates created yet.</p>
            <button onclick="openCreateTemplateModal()" class="btn-notion-primary text-xs mx-auto">
                Create First Template
            </button>
        </div>
        @endforelse
    </div>

    @if($templates->hasPages())
        <div class="p-3 font-mono">
            {{ $templates->links() }}
        </div>
    @endif

</div>

<!-- Create Template Modal -->
<div id="createTemplateModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="notion-card w-full max-w-2xl space-y-4 relative shadow-xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-black/10">
            <h3 class="text-lg font-bold text-ink-black flex items-center gap-2">
                <i class="fa-solid fa-pen-nib text-notion-blue"></i> Create WYSIWYG Email Template
            </h3>
            <button onclick="closeCreateTemplateModal()" class="text-stone hover:text-ink-black">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="createTemplateForm" action="{{ route('templates.store') }}" method="POST" onsubmit="submitCreateTemplate(event)" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1">Template Name *</label>
                <input type="text" name="name" required placeholder="e.g. Swing Trading High Conviction" class="notion-input font-bold">
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1">Email Subject *</label>
                <input type="text" name="subject" required placeholder="Trade Instruction - {TRADE_TYPE} {STOCK_NAME}" class="notion-input font-mono text-notion-blue font-bold">
                <p class="text-[11px] text-stone font-mono mt-1">Available Tags: {STOCK_NAME}, {TRADE_TYPE}, {ENTRY_RANGE}, {STOP_LOSS}, {TARGET_1}, {CLIENT_NAME}</p>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1">HTML Email Body (WYSIWYG) *</label>
                <div class="rounded-lg border border-black/15 overflow-hidden bg-white">
                    <div id="createQuillEditor" class="min-h-[220px] text-sm"></div>
                </div>
                <input type="hidden" id="create_body_html" name="body_html">
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="create_is_default" name="is_default" value="1" class="rounded border-black/20 text-notion-blue focus:ring-notion-blue">
                <label for="create_is_default" class="text-xs font-medium text-ink-black cursor-pointer">Set as Default Template</label>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-black/10">
                <button type="button" onclick="closeCreateTemplateModal()" class="btn-notion-outlined text-xs">Cancel</button>
                <button type="submit" class="btn-notion-primary text-xs">Save Template</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Template Modal -->
<div id="editTemplateModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="notion-card w-full max-w-2xl space-y-4 relative shadow-xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-black/10">
            <h3 class="text-lg font-bold text-ink-black flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-notion-blue"></i> Edit WYSIWYG Email Template
            </h3>
            <button onclick="closeEditTemplateModal()" class="text-stone hover:text-ink-black">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="editTemplateForm" method="POST" onsubmit="submitEditTemplate(event)" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_template_id">

            <div>
                <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1">Template Name *</label>
                <input type="text" id="edit_name" name="name" required class="notion-input font-bold">
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1">Email Subject *</label>
                <input type="text" id="edit_subject" name="subject" required class="notion-input font-mono text-notion-blue font-bold">
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-ink-black uppercase tracking-wider mb-1">HTML Email Body (WYSIWYG) *</label>
                <div class="rounded-lg border border-black/15 overflow-hidden bg-white">
                    <div id="editQuillEditor" class="min-h-[220px] text-sm"></div>
                </div>
                <input type="hidden" id="edit_body_html" name="body_html">
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="edit_is_default" name="is_default" value="1" class="rounded border-black/20 text-notion-blue focus:ring-notion-blue">
                <label for="edit_is_default" class="text-xs font-medium text-ink-black cursor-pointer">Set as Default Template</label>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-black/10">
                <button type="button" onclick="closeEditTemplateModal()" class="btn-notion-outlined text-xs">Cancel</button>
                <button type="submit" class="btn-notion-primary text-xs">Update Template</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let createQuill, editQuill;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Quill for Create Modal
        createQuill = new Quill('#createQuillEditor', {
            theme: 'snow',
            placeholder: 'Build your HTML template using formatting and placeholders like {STOCK_NAME}, {ENTRY_RANGE}...',
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

        // Initialize Quill for Edit Modal
        editQuill = new Quill('#editQuillEditor', {
            theme: 'snow',
            placeholder: 'Edit your template body...',
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
    });

    function openCreateTemplateModal() {
        document.getElementById('createTemplateModal').classList.remove('hidden');
    }
    function closeCreateTemplateModal() {
        document.getElementById('createTemplateModal').classList.add('hidden');
    }

    function openEditTemplateModal(template) {
        document.getElementById('edit_template_id').value = template.id;
        document.getElementById('edit_name').value = template.name;
        document.getElementById('edit_subject').value = template.subject;
        document.getElementById('edit_is_default').checked = template.is_default ? true : false;

        editQuill.root.innerHTML = template.body_html || '';
        document.getElementById('edit_body_html').value = template.body_html || '';

        const form = document.getElementById('editTemplateForm');
        form.action = `/templates/${template.id}`;

        document.getElementById('editTemplateModal').classList.remove('hidden');
    }
    function closeEditTemplateModal() {
        document.getElementById('editTemplateModal').classList.add('hidden');
    }

    function submitCreateTemplate(e) {
        if (createQuill) {
            document.getElementById('create_body_html').value = createQuill.root.innerHTML;
        }
    }

    function submitEditTemplate(e) {
        if (editQuill) {
            document.getElementById('edit_body_html').value = editQuill.root.innerHTML;
        }
    }

    function deleteTemplate(id, name) {
        Swal.fire({
            title: 'Delete Email Template?',
            text: `Are you sure you want to remove "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f64932',
            cancelButtonColor: '#000000',
            confirmButtonText: 'Yes, Delete',
            background: '#ffffff',
            color: '#000000'
        }).then((result) => {
            if (result.isConfirmed) {
                window.ajaxFetch(`/templates/${id}`, { method: 'DELETE' })
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
