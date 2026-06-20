<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Pillar;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PillarController extends Controller
{
    public function index(): Response
    {
        $pillars = Pillar::forUser(auth()->id())
            ->ordered()
            ->with(['domains.topics'])
            ->withCount(['references', 'vocabulary', 'links'])
            ->get();

        return Inertia::render('pillars', [
            'pillars' => $pillars,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['is_active'] = true;
        
        Pillar::create($validated);

        return redirect()->route('pillars.index')
            ->with('success', 'Pillar created successfully');
    }

    public function update(Pillar $pillar, Request $request): RedirectResponse
    {
        $this->authorize('update', $pillar);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $pillar->update($validated);

        return redirect()->route('pillars.index')
            ->with('success', 'Pillar updated successfully');
    }

    public function destroy(Pillar $pillar): RedirectResponse
    {
        $this->authorize('delete', $pillar);
        
        $pillar->delete();

        return redirect()->route('pillars.index')
            ->with('success', 'Pillar deleted successfully');
    }
}
