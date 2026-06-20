<?php

namespace App\Http\Controllers;

use App\Models\Notebook;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotebookController extends Controller
{
    public function index(): Response
    {
        $notebooks = Notebook::where('user_id', auth()->id())
            ->withCount('notes')
            ->latest()
            ->get();

        return Inertia::render('notebooks', [
            'notebooks' => $notebooks,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'pillar_id' => 'nullable|exists:pillars,id',
            'domain_id' => 'nullable|exists:domains,id',
        ]);

        $validated['user_id'] = auth()->id();
        
        Notebook::create($validated);

        return redirect()->route('notebooks.index')
            ->with('success', 'Notebook created successfully');
    }

    public function update(Notebook $notebook, Request $request): RedirectResponse
    {
        $this->authorize('update', $notebook);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'pillar_id' => 'nullable|exists:pillars,id',
            'domain_id' => 'nullable|exists:domains,id',
        ]);

        $notebook->update($validated);

        return redirect()->route('notebooks.index')
            ->with('success', 'Notebook updated successfully');
    }

    public function destroy(Notebook $notebook): RedirectResponse
    {
        $this->authorize('delete', $notebook);
        
        $notebook->delete();

        return redirect()->route('notebooks.index')
            ->with('success', 'Notebook deleted successfully');
    }
}
