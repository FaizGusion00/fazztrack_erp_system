<?php

namespace App\Http\Controllers;

use App\Models\DesignTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DesignTemplateController extends Controller
{
    /**
     * Display a listing of design templates
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Only designers can access templates
        if (!$user->isDesigner()) {
            abort(403, 'Access denied. Only designers can access design templates.');
        }

        $query = DesignTemplate::with('designer');

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by search term
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('tags', 'like', '%' . $request->search . '%');
            });
        }

        // Show user's templates and public templates
        $query->where(function($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhere('is_public', true);
        });

        $templates = $query->latest()->paginate(12)->withQueryString();

        // Get unique categories for filter
        $categories = DesignTemplate::distinct()->pluck('category')->filter();

        return view('design-templates.index', compact('templates', 'categories'));
    }

    /**
     * Show the form for creating a new template
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isDesigner()) {
            abort(403, 'Access denied. Only designers can create templates.');
        }

        return view('design-templates.create');
    }

    /**
     * Store a newly created template
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isDesigner()) {
            abort(403, 'Access denied. Only designers can create templates.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|string|max:500',
            'template_files.*' => 'required|file|mimes:jpg,jpeg,png,pdf,psd,ai,zip|max:61440',
            'is_public' => 'boolean'
        ]);

        $templateFiles = [];
        
        // Handle template file uploads
        if ($request->hasFile('template_files')) {
            foreach ($request->file('template_files') as $file) {
                $templateFiles[] = $file->store('templates', 'public');
            }
        }

        $template = DesignTemplate::create([
            'name' => $request->name,
            'description' => $request->description,
            'template_files' => $templateFiles,
            'category' => $request->category,
            'tags' => $request->tags,
            'created_by' => $user->id,
            'is_public' => $request->boolean('is_public', false)
        ]);

        return redirect()->route('design-templates.index')
            ->with('success', 'Design template created successfully.');
    }

    /**
     * Display the specified template
     */
    public function show(DesignTemplate $template)
    {
        $user = Auth::user();
        
        if (!$user->isDesigner()) {
            abort(403, 'Access denied. Only designers can view templates.');
        }

        // Check if user can view this template
        if ($template->created_by !== $user->id && !$template->is_public) {
            abort(403, 'Access denied. You can only view your own templates or public templates.');
        }

        return view('design-templates.show', compact('template'));
    }

    /**
     * Show the form for editing the specified template
     */
    public function edit(DesignTemplate $template)
    {
        $user = Auth::user();
        
        if (!$user->isDesigner()) {
            abort(403, 'Access denied. Only designers can edit templates.');
        }

        if ($template->created_by !== $user->id) {
            abort(403, 'Access denied. You can only edit your own templates.');
        }

        return view('design-templates.edit', compact('template'));
    }

    /**
     * Update the specified template
     */
    public function update(Request $request, DesignTemplate $template)
    {
        $user = Auth::user();
        
        if (!$user->isDesigner()) {
            abort(403, 'Access denied. Only designers can update templates.');
        }

        if ($template->created_by !== $user->id) {
            abort(403, 'Access denied. You can only update your own templates.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|string|max:500',
            'template_files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,psd,ai,zip|max:61440',
            'is_public' => 'boolean'
        ]);

        $templateData = $request->only(['name', 'description', 'category', 'tags']);
        $templateData['is_public'] = $request->boolean('is_public', false);

        // Handle new template file uploads
        if ($request->hasFile('template_files')) {
            $templateFiles = $template->template_files ?? [];
            foreach ($request->file('template_files') as $file) {
                $templateFiles[] = $file->store('templates', 'public');
            }
            $templateData['template_files'] = $templateFiles;
        }

        $template->update($templateData);

        return redirect()->route('design-templates.index')
            ->with('success', 'Design template updated successfully.');
    }

    /**
     * Remove the specified template
     */
    public function destroy(DesignTemplate $template)
    {
        $user = Auth::user();
        
        if (!$user->isDesigner()) {
            abort(403, 'Access denied. Only designers can delete templates.');
        }

        if ($template->created_by !== $user->id) {
            abort(403, 'Access denied. You can only delete your own templates.');
        }

        $template->delete();

        return redirect()->route('design-templates.index')
            ->with('success', 'Design template deleted successfully.');
    }
}
