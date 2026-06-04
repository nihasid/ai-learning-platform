<?php

namespace App\Http\Controllers;

use App\Models\ChildWorksheetAssignment;
use App\Models\Permission;
use App\Models\Worksheet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WorksheetController extends Controller
{
    public function index(Request $request): View
    {
        $selectedAgeGroup = $request->string('age_group')->toString();
        $ageGroups = $this->ageGroupLabels();

        if (! array_key_exists($selectedAgeGroup, $ageGroups)) {
            $selectedAgeGroup = '';
        }

        $children = $request->user()
            ->childProfiles()
            ->with(['worksheetAssignments.worksheet'])
            ->latest()
            ->get();

        $worksheets = Worksheet::query()
            ->withCount('assignments')
            ->when($selectedAgeGroup !== '', function ($query) use ($selectedAgeGroup) {
                $query->where('age_group', $selectedAgeGroup);
            })
            ->latest()
            ->get();

        return view('worksheets.index', [
            'children' => $children,
            'worksheets' => $worksheets,
            'ageGroups' => $ageGroups,
            'selectedAgeGroup' => $selectedAgeGroup,
            'subjectLabels' => $this->subjectLabels(),
            'isAdmin' => $request->user()->isAdmin(),
            'canManageWorksheets' => $request->user()->hasPermission(Permission::MANAGE_WORKSHEETS),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission(Permission::MANAGE_WORKSHEETS), 403);

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:120'],
            'subject' => ['required', 'in:'.implode(',', Worksheet::SUBJECTS)],
            'age_group' => ['required', 'in:'.implode(',', Worksheet::AGE_GROUPS)],
            'description' => ['nullable', 'string', 'max:800'],
            'worksheet_files' => ['required', 'array', 'max:20'],
            'worksheet_files.*' => ['required', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:51200'],
        ]);

        $files = $request->file('worksheet_files');

        foreach ($files as $file) {
            $path = $file->store('worksheets', 'public');
            $filenameTitle = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $title = $validated['title'] ?: $filenameTitle;

            if ($validated['title'] && count($files) > 1) {
                $title = $validated['title'].' - '.$filenameTitle;
            }

            Worksheet::create([
                'uploaded_by' => $request->user()->id,
                'title' => $title,
                'subject' => $validated['subject'],
                'age_group' => $validated['age_group'],
                'description' => $validated['description'] ?? null,
                'file_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
            ]);
        }

        return back()->with('status', count($files).' worksheet(s) uploaded to the library.');
    }

    public function assign(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'child_profile_id' => ['required', 'exists:child_profiles,id'],
            'worksheet_id' => ['required', 'exists:worksheets,id'],
        ]);

        $child = $request->user()->childProfiles()->findOrFail($validated['child_profile_id']);
        $worksheet = Worksheet::findOrFail($validated['worksheet_id']);

        ChildWorksheetAssignment::firstOrCreate(
            [
                'child_profile_id' => $child->id,
                'worksheet_id' => $worksheet->id,
            ],
            [
                'status' => 'assigned',
                'assigned_at' => now(),
            ]
        );

        return back()->with('status', 'Worksheet assigned to child.');
    }

    public function start(Request $request, ChildWorksheetAssignment $assignment): RedirectResponse
    {
        $this->authorizeAssignment($request, $assignment);

        $assignment->update([
            'status' => 'in_progress',
            'started_at' => $assignment->started_at ?? now(),
        ]);

        return back()->with('status', 'Worksheet marked in progress.');
    }

    public function complete(Request $request, ChildWorksheetAssignment $assignment): RedirectResponse
    {
        $this->authorizeAssignment($request, $assignment);

        $assignment->update([
            'status' => 'completed',
            'started_at' => $assignment->started_at ?? now(),
            'completed_at' => now(),
        ]);

        return back()->with('status', 'Worksheet marked complete.');
    }

    public function download(Request $request, Worksheet $worksheet): StreamedResponse
    {
        return Storage::disk('public')->download($worksheet->file_path, $worksheet->original_filename);
    }

    public function view(Request $request, Worksheet $worksheet): StreamedResponse
    {
        return Storage::disk('public')->response($worksheet->file_path, $worksheet->original_filename, [
            'Content-Type' => $worksheet->mime_type ?: 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$worksheet->original_filename.'"',
        ]);
    }

    public function destroy(Request $request, Worksheet $worksheet): RedirectResponse
    {
        abort_unless($request->user()->hasPermission(Permission::MANAGE_WORKSHEETS), 403);

        $worksheet->delete();

        return back()->with('status', 'Worksheet removed from the library.');
    }

    private function authorizeAssignment(Request $request, ChildWorksheetAssignment $assignment): void
    {
        abort_unless($assignment->childProfile->parent_id === $request->user()->id, 403);
    }

    private function subjectLabels(): array
    {
        return [
            'math' => 'Math',
            'literature' => 'Literature',
            'general_knowledge' => 'General Knowledge',
            'activity_book' => 'Activity Book',
            'drawing' => 'Drawing',
        ];
    }

    private function ageGroupLabels(): array
    {
        return [
            '2-3' => 'Ages 2-3',
            '4-5' => 'Ages 4-5',
            '6-8' => 'Ages 6-8',
            '9-12' => 'Ages 9-12',
        ];
    }
}
