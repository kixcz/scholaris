<?php

namespace App\Http\Controllers;

use App\Models\Vocabulary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VocabularyController extends Controller
{
    public function index(): Response
    {
        $vocabulary = Vocabulary::where('user_id', auth()->id())
            ->latest()
            ->get();

        return Inertia::render('vocabulary', [
            'vocabulary' => $vocabulary,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'term' => 'required|string|max:255',
            'definition' => 'required|string',
            'example' => 'nullable|string',
            'category' => 'nullable|string|max:50',
            'importance' => 'nullable|string|max:20',
            'mastery_level' => 'nullable|string|max:20',
            'related_terms' => 'nullable|string',
            'pillar_id' => 'nullable|exists:pillars,id',
            'domain_id' => 'nullable|exists:domains,id',
            'topic_id' => 'nullable|exists:topics,id',
        ]);

        $validated['user_id'] = auth()->id();
        Vocabulary::create($validated);

        return redirect()->route('vocabulary.index')
            ->with('success', 'Vocabulary term added successfully');
    }

    public function update(Vocabulary $vocabulary, Request $request): RedirectResponse
    {
        $this->authorize('update', $vocabulary);

        $validated = $request->validate([
            'term' => 'required|string|max:255',
            'definition' => 'required|string',
            'example' => 'nullable|string',
            'category' => 'nullable|string|max:50',
            'importance' => 'nullable|string|max:20',
            'mastery_level' => 'nullable|string|max:20',
            'related_terms' => 'nullable|string',
        ]);

        $vocabulary->update($validated);

        return redirect()->route('vocabulary.index')
            ->with('success', 'Vocabulary term updated successfully');
    }

    public function destroy(Vocabulary $vocabulary): RedirectResponse
    {
        $this->authorize('delete', $vocabulary);
        
        $vocabulary->delete();

        return redirect()->route('vocabulary.index')
            ->with('success', 'Vocabulary term deleted successfully');
    }
}
