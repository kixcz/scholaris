# Scholaris - Complete Implementation Guide

This guide contains all remaining code needed to complete the migration from Dashboard.html to Scholaris.

---

## Phase 1: Controllers (Create these files)

### 1. ReferenceController
**File:** `app/Http/Controllers/ReferenceController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Reference;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReferenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Reference::query();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('tier')) {
            $query->byTier($request->tier);
        }

        if ($request->filled('pillar')) {
            $query->byPillar($request->pillar);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        $references = $query->orderBy('created_at', 'desc')->paginate(20);

        return Inertia::render('references/index', [
            'references' => $references,
            'filters' => $request->only(['search', 'tier', 'pillar', 'status', 'type']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'authors' => 'required|string',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'journal' => 'nullable|string|max:255',
            'doi' => 'nullable|string|max:255',
            'url' => 'nullable|url|max:255',
            'type' => 'required|string',
            'tier' => 'required|integer|min:1|max:5',
            'pillar' => 'required|string',
            'status' => 'required|string',
            'pdf_path' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        auth()->user()->references()->create($validated);

        return redirect()->route('references.index')
            ->with('success', 'Reference created successfully');
    }

    public function update(Request $request, Reference $reference)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'authors' => 'required|string',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'journal' => 'nullable|string|max:255',
            'doi' => 'nullable|string|max:255',
            'url' => 'nullable|url|max:255',
            'type' => 'required|string',
            'tier' => 'required|integer|min:1|max:5',
            'pillar' => 'required|string',
            'status' => 'required|string',
            'pdf_path' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        $reference->update($validated);

        return redirect()->route('references.index')
            ->with('success', 'Reference updated successfully');
    }

    public function destroy(Reference $reference)
    {
        $reference->delete();

        return redirect()->route('references.index')
            ->with('success', 'Reference deleted successfully');
    }

    public function export(Request $request)
    {
        $references = Reference::all();
        
        if ($request->format === 'csv') {
            return $this->exportCsv($references);
        }
        
        return $this->exportJson($references);
    }

    private function exportCsv($references)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="references.csv"',
        ];

        $callback = function() use ($references) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Title', 'Authors', 'Year', 'Journal', 'DOI', 'URL', 'Type', 'Tier', 'Pillar', 'Status']);
            
            foreach ($references as $ref) {
                fputcsv($file, [
                    $ref->title, $ref->authors, $ref->year, $ref->journal,
                    $ref->doi, $ref->url, $ref->type, $ref->tier, $ref->pillar, $ref->status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportJson($references)
    {
        return response()->json($references, 200, [], JSON_PRETTY_PRINT);
    }
}
```

### 2. NotebookController
**File:** `app/Http/Controllers/NotebookController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Notebook;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotebookController extends Controller
{
    public function index()
    {
        $notebooks = Notebook::withCount('notes')->get();
        
        return Inertia::render('notebooks/index', [
            'notebooks' => $notebooks,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
        ]);

        auth()->user()->notebooks()->create($validated);

        return redirect()->route('notebooks.index')
            ->with('success', 'Notebook created successfully');
    }

    public function update(Request $request, Notebook $notebook)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
        ]);

        $notebook->update($validated);

        return redirect()->route('notebooks.index')
            ->with('success', 'Notebook updated successfully');
    }

    public function destroy(Notebook $notebook)
    {
        $notebook->delete();

        return redirect()->route('notebooks.index')
            ->with('success', 'Notebook deleted successfully');
    }
}
```

### 3. NoteController
**File:** `app/Http/Controllers/NoteController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index($notebookId)
    {
        $notes = Note::where('notebook_id', $notebookId)
            ->with('reference')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'notebook_id' => 'required|exists:notebooks,id',
            'ref_id' => 'nullable|exists:references,id',
            'text' => 'required|string',
        ]);

        $note = auth()->user()->notes()->create($validated);
        $note->load('reference');

        return response()->json($note, 201);
    }

    public function update(Request $request, Note $note)
    {
        $validated = $request->validate([
            'ref_id' => 'nullable|exists:references,id',
            'text' => 'required|string',
        ]);

        $note->update($validated);
        $note->load('reference');

        return response()->json($note);
    }

    public function destroy(Note $note)
    {
        $note->delete();
        return response()->json(['message' => 'Note deleted successfully']);
    }
}
```

### 4. VocabularyController
**File:** `app/Http/Controllers/VocabularyController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Vocabulary;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VocabularyController extends Controller
{
    public function index(Request $request)
    {
        $query = Vocabulary::query();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $vocabulary = $query->orderBy('term')->get();

        return Inertia::render('vocabulary/index', [
            'vocabulary' => $vocabulary,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'term' => 'required|string|max:255',
            'definition' => 'required|string',
            'personal_understanding' => 'nullable|string',
            'example' => 'nullable|string',
            'related_refs' => 'nullable|array',
        ]);

        auth()->user()->vocabulary()->create($validated);

        return redirect()->route('vocabulary.index')
            ->with('success', 'Term added successfully');
    }

    public function update(Request $request, Vocabulary $vocabulary)
    {
        $validated = $request->validate([
            'term' => 'required|string|max:255',
            'definition' => 'required|string',
            'personal_understanding' => 'nullable|string',
            'example' => 'nullable|string',
            'related_refs' => 'nullable|array',
        ]);

        $vocabulary->update($validated);

        return redirect()->route('vocabulary.index')
            ->with('success', 'Term updated successfully');
    }

    public function destroy(Vocabulary $vocabulary)
    {
        $vocabulary->delete();

        return redirect()->route('vocabulary.index')
            ->with('success', 'Term deleted successfully');
    }
}
```

### 5. WeeklyGoalController
**File:** `app/Http/Controllers/WeeklyGoalController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\WeeklyGoal;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WeeklyGoalController extends Controller
{
    public function index(Request $request)
    {
        $query = WeeklyGoal::query();

        if ($request->filled('urgency')) {
            $query->byUrgency($request->urgency);
        }

        if ($request->filled('day')) {
            $query->byDay($request->day);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        $goals = $query->orderBy('urgency')->get();

        return Inertia::render('weekly-goals/index', [
            'goals' => $goals,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'task' => 'required|string|max:255',
            'urgency' => 'required|in:critical,important,routine',
            'day_of_week' => 'required|string',
            'status' => 'required|in:todo,in_progress,completed',
            'deadline' => 'nullable|date',
        ]);

        auth()->user()->weeklyGoals()->create($validated);

        return redirect()->route('weekly-goals.index')
            ->with('success', 'Goal added successfully');
    }

    public function update(Request $request, WeeklyGoal $weeklyGoal)
    {
        $validated = $request->validate([
            'task' => 'required|string|max:255',
            'urgency' => 'required|in:critical,important,routine',
            'day_of_week' => 'required|string',
            'status' => 'required|in:todo,in_progress,completed',
            'deadline' => 'nullable|date',
        ]);

        $weeklyGoal->update($validated);

        return redirect()->route('weekly-goals.index')
            ->with('success', 'Goal updated successfully');
    }

    public function destroy(WeeklyGoal $weeklyGoal)
    {
        $weeklyGoal->delete();

        return redirect()->route('weekly-goals.index')
            ->with('success', 'Goal deleted successfully');
    }
}
```

### 6. ReportsController
**File:** `app/Http/Controllers/ReportsController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Reference;
use App\Models\Note;
use App\Models\Vocabulary;
use App\Models\WeeklyGoal;
use App\Models\Roadmap;
use App\Models\Link;
use Inertia\Inertia;

class ReportsController extends Controller
{
    public function index()
    {
        $totalReferences = Reference::count();
        $byStatus = Reference::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
            
        $byType = Reference::selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type');
            
        $byTier = Reference::selectRaw('tier, count(*) as count')
            ->groupBy('tier')
            ->get()
            ->pluck('count', 'tier');
            
        $byPillar = Reference::selectRaw('pillar, count(*) as count')
            ->groupBy('pillar')
            ->get()
            ->pluck('count', 'pillar');

        $reading = Reference::where('status', 'reading')->count();
        $completed = Reference::where('status', 'completed')->count();
        $readingProgress = $totalReferences > 0 ? round(($completed / $totalReferences) * 100, 2) : 0;

        $totalGoals = WeeklyGoal::count();
        $completedGoals = WeeklyGoal::where('status', 'completed')->count();
        $completionRate = $totalGoals > 0 ? round(($completedGoals / $totalGoals) * 100, 2) : 0;

        $reports = [
            'total_references' => $totalReferences,
            'by_status' => $byStatus,
            'by_type' => $byType,
            'by_tier' => $byTier,
            'by_pillar' => $byPillar,
            'reading_progress' => $readingProgress,
            'total_notes' => Note::count(),
            'total_vocabulary' => Vocabulary::count(),
            'weekly_completion_rate' => $completionRate,
            'total_roadmaps' => Roadmap::count(),
            'total_links' => Link::count(),
        ];

        return Inertia::render('reports/index', [
            'reports' => $reports,
        ]);
    }
}
```

### 7. RoadmapController
**File:** `app/Http/Controllers/RoadmapController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Roadmap;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoadmapController extends Controller
{
    public function index()
    {
        $roadmaps = Roadmap::with('milestones')->get();
        
        return Inertia::render('roadmap/index', [
            'roadmaps' => $roadmaps,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planned,in_progress,completed',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'color' => 'required|string|max:7',
        ]);

        $roadmap = auth()->user()->roadmaps()->create($validated);

        return redirect()->route('roadmap.index')
            ->with('success', 'Roadmap created successfully');
    }

    public function update(Request $request, Roadmap $roadmap)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planned,in_progress,completed',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'color' => 'required|string|max:7',
        ]);

        $roadmap->update($validated);

        return redirect()->route('roadmap.index')
            ->with('success', 'Roadmap updated successfully');
    }

    public function destroy(Roadmap $roadmap)
    {
        $roadmap->delete();

        return redirect()->route('roadmap.index')
            ->with('success', 'Roadmap deleted successfully');
    }
}
```

### 8. MilestoneController
**File:** `app/Http/Controllers/MilestoneController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'roadmap_id' => 'required|exists:roadmaps,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_date' => 'required|date',
        ]);

        $milestone = Milestone::create($validated);

        return response()->json($milestone, 201);
    }

    public function update(Request $request, Milestone $milestone)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_date' => 'required|date',
            'status' => 'required|in:pending,completed',
        ]);

        if ($validated['status'] === 'completed' && $milestone->status !== 'completed') {
            $validated['completed_at'] = now();
        }

        $milestone->update($validated);

        return response()->json($milestone);
    }

    public function destroy(Milestone $milestone)
    {
        $milestone->delete();
        return response()->json(['message' => 'Milestone deleted successfully']);
    }
}
```

### 9. LinkCategoryController
**File:** `app/Http/Controllers/LinkCategoryController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\LinkCategory;
use Illuminate\Http\Request;

class LinkCategoryController extends Controller
{
    public function index()
    {
        $categories = LinkCategory::withCount('links')->get();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
        ]);

        $category = auth()->user()->linkCategories()->create($validated);
        return response()->json($category, 201);
    }

    public function update(Request $request, LinkCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
        ]);

        $category->update($validated);
        return response()->json($category);
    }

    public function destroy(LinkCategory $category)
    {
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
```

### 10. LinkController
**File:** `app/Http/Controllers/LinkController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LinkController extends Controller
{
    public function index(Request $request)
    {
        $query = Link::with('category');

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('category_id')) {
            $query->byCategory($request->category_id);
        }

        $links = $query->orderBy('created_at', 'desc')->get();

        return Inertia::render('links/index', [
            'links' => $links,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:link_categories,id',
            'type' => 'required|string',
            'tags' => 'nullable|array',
        ]);

        $link = auth()->user()->links()->create($validated);
        $link->load('category');

        return response()->json($link, 201);
    }

    public function update(Request $request, Link $link)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:link_categories,id',
            'type' => 'required|string',
            'tags' => 'nullable|array',
        ]);

        $link->update($validated);
        $link->load('category');

        return response()->json($link);
    }

    public function destroy(Link $link)
    {
        $link->delete();
        return response()->json(['message' => 'Link deleted successfully']);
    }
}
```

---

## Phase 1.4: Routes

**Update:** `routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ReferenceController;
use App\Http\Controllers\NotebookController;
use App\Http\Controllers\VocabularyController;
use App\Http\Controllers\WeeklyGoalController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\RoadmapController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\LinkCategoryController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\NoteController;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    // Resource routes
    Route::resource('references', ReferenceController::class);
    Route::get('references/export', [ReferenceController::class, 'export'])->name('references.export');
    
    Route::resource('notebooks', NotebookController::class);
    Route::get('notebooks/{notebook}/notes', [NoteController::class, 'index'])->name('notes.index');
    Route::resource('notes', NoteController::class)->except(['index']);
    
    Route::resource('vocabulary', VocabularyController::class);
    
    Route::resource('weekly-goals', WeeklyGoalController::class);
    
    Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');
    
    Route::resource('roadmaps', RoadmapController::class);
    Route::resource('milestones', MilestoneController::class)->except(['index', 'show']);
    
    Route::resource('link-categories', LinkCategoryController::class)->except(['create', 'edit']);
    Route::resource('links', LinkController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
```

---

## Next Steps

1. **Run migrations:**
```bash
php artisan migrate
```

2. **Install additional shadcn components:**
```bash
npx shadcn@latest add table
npx shadcn@latest add textarea
npx shadcn@latest add tabs
npx shadcn@latest add separator
npx shadcn@latest add progress
npx shadcn@latest add scroll-area
npx shadcn@latest add popover
```

3. **Install Recharts:**
```bash
npm install recharts
```

4. **Create TypeScript types** (Phase 3)
5. **Update sidebar** (Phase 4)
6. **Build React components** (Phase 5)

The React components will be created in the next phase with full shadcn/ui integration.

---

**Note:** Due to the extensive nature of this migration (8 modules with full CRUD operations, filters, charts, and professional UI), the React components will be delivered in subsequent phases to ensure quality and completeness.
