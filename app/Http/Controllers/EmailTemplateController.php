<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = EmailTemplate::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $templates = $query->latest()->paginate(10)->withQueryString();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'templates' => $templates,
            ]);
        }

        return view('templates.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'is_default' => 'nullable|boolean',
        ]);

        if ($request->boolean('is_default')) {
            EmailTemplate::query()->update(['is_default' => false]);
        }

        $template = EmailTemplate::create([
            'name' => $validated['name'],
            'subject' => $validated['subject'],
            'body_html' => $validated['body_html'],
            'is_default' => $request->boolean('is_default'),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Email template created successfully!',
                'template' => $template,
            ]);
        }

        return redirect()->route('templates.index')->with('success', 'Email template created successfully!');
    }

    public function show(EmailTemplate $template)
    {
        return response()->json([
            'success' => true,
            'template' => $template,
        ]);
    }

    public function update(Request $request, EmailTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'is_default' => 'nullable|boolean',
        ]);

        if ($request->boolean('is_default')) {
            EmailTemplate::query()->where('id', '!=', $template->id)->update(['is_default' => false]);
        }

        $template->update([
            'name' => $validated['name'],
            'subject' => $validated['subject'],
            'body_html' => $validated['body_html'],
            'is_default' => $request->boolean('is_default'),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Email template updated successfully!',
                'template' => $template,
            ]);
        }

        return redirect()->route('templates.index')->with('success', 'Email template updated successfully!');
    }

    public function destroy(EmailTemplate $template, Request $request)
    {
        $template->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Email template deleted successfully!',
            ]);
        }

        return redirect()->route('templates.index')->with('success', 'Email template deleted successfully!');
    }
}
