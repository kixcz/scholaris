<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\Roadmap;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class RoadmapController extends Controller
{
    public function index(): Response
    {
        $roadmaps = Roadmap::where('user_id', auth()->id())
            ->withCount('milestones')
            ->with(['milestones' => function ($query) {
                $query->orderBy('target_date');
            }])
            ->latest()
            ->get();

        return Inertia::render('roadmaps', [
            'roadmaps' => $roadmaps,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:50',
            'start_date' => 'required|date',
            'target_end_date' => 'required|date|after:start_date',
            'success_criteria' => 'nullable|string',
            'milestones' => 'nullable|array',
            'milestones.*.title' => 'required|string|max:255',
            'milestones.*.description' => 'nullable|string',
            'milestones.*.target_date' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            // Create roadmap
            $roadmap = Roadmap::create([
                'user_id' => auth()->id(),
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'status' => 'planned',
                'start_date' => $validated['start_date'],
                'target_end_date' => $validated['target_end_date'],
                'color' => '#3498db',
            ]);

            // Create milestones
            if (!empty($validated['milestones'])) {
                foreach ($validated['milestones'] as $milestoneData) {
                    Milestone::create([
                        'roadmap_id' => $roadmap->id,
                        'title' => $milestoneData['title'],
                        'description' => $milestoneData['description'] ?? null,
                        'target_date' => $milestoneData['target_date'],
                        'status' => 'pending',
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('roadmaps.index')
                ->with('success', 'Roadmap created with ' . count($validated['milestones'] ?? []) . ' milestones');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create roadmap: ' . $e->getMessage()]);
        }
    }

    public function update(Roadmap $roadmap, Request $request): RedirectResponse
    {
        $this->authorize('update', $roadmap);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:planned,active,completed',
            'start_date' => 'required|date',
            'target_end_date' => 'required|date',
        ]);

        $roadmap->update($validated);

        return redirect()->route('roadmaps.index')
            ->with('success', 'Roadmap updated successfully');
    }

    public function destroy(Roadmap $roadmap): RedirectResponse
    {
        $this->authorize('delete', $roadmap);
        
        $roadmap->delete();

        return redirect()->route('roadmaps.index')
            ->with('success', 'Roadmap deleted successfully');
    }
}
