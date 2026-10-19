<?php

namespace App\Http\Controllers;

use App\Models\Bod;
use App\Models\Division;
use App\Models\Document;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    private array $validTypes = ['sop', 'job_desk', 'bpm', 'struktur'];

    /**
     * Level 1: Show all BODs for a document type
     */
    public function index(string $type)
    {
        abort_unless(in_array($type, $this->validTypes), 404);

        $bods = Bod::withCount([
            'documents as doc_count' => fn($q) => $q->where('type', $type),
            'departments',
        ])->orderBy('order')->get();

        $typeLabel = Document::$typeLabels[$type] ?? strtoupper($type);
        $typeColor = Document::$typeColors[$type] ?? '#3b82f6';
        $typeIcon  = Document::$typeIcons[$type] ?? 'fa-folder';

        return view('documents.index', compact('type', 'typeLabel', 'typeColor', 'typeIcon', 'bods'));
    }

    /**
     * Level 2: Show all Departments (divisions) within a BOD
     */
    public function departments(string $type, Bod $bod)
    {
        abort_unless(in_array($type, $this->validTypes), 404);

        $departments = Division::where('bod_id', $bod->id)
            ->withCount([
                'documents as doc_count' => fn($q) => $q->where('type', $type),
                'positions',
            ])
            ->orderBy('order')
            ->get();

        $typeLabel = Document::$typeLabels[$type] ?? strtoupper($type);
        $typeColor = Document::$typeColors[$type] ?? '#3b82f6';
        $typeIcon  = Document::$typeIcons[$type] ?? 'fa-folder';

        return view('documents.departments', compact('type', 'typeLabel', 'typeColor', 'typeIcon', 'bod', 'departments'));
    }

    /**
     * Level 3a: Show positions within a department (only for job_desk)
     * Level 3b: Show documents in department (for sop, bpm, struktur)
     */
    public function positions(string $type, Bod $bod, Division $department)
    {
        abort_unless(in_array($type, $this->validTypes), 404);

        if ($type === 'job_desk') {
            // For job_desk: show positions (jabatan) list
            $positions = Position::where('division_id', $department->id)
                ->withCount([
                    'documents as doc_count' => fn($q) => $q->where('type', $type),
                ])
                ->orderBy('order')
                ->get();

            $typeLabel = Document::$typeLabels[$type] ?? strtoupper($type);
            $typeColor = Document::$typeColors[$type] ?? '#3b82f6';
            $typeIcon  = Document::$typeIcons[$type] ?? 'fa-folder';

            return view('documents.positions', compact('type', 'typeLabel', 'typeColor', 'typeIcon', 'bod', 'department', 'positions'));
        } else {
            // For sop, bpm, struktur: show documents directly
            $documents = Document::where('type', $type)
                ->where('bod_id', $bod->id)
                ->where('division_id', $department->id)
                ->with('uploader')
                ->latest()
                ->get();

            $typeLabel = Document::$typeLabels[$type] ?? strtoupper($type);
            $typeColor = Document::$typeColors[$type] ?? '#3b82f6';
            $typeIcon  = Document::$typeIcons[$type] ?? 'fa-folder';

            return view('documents.files', compact('type', 'typeLabel', 'typeColor', 'typeIcon', 'bod', 'department', 'documents'));
        }
    }

    /**
     * Level 4: Show documents for a position (job_desk only)
     */
    public function files(string $type, Bod $bod, Division $department, Position $position)
    {
        abort_unless($type === 'job_desk', 404);

        $documents = Document::where('type', $type)
            ->where('bod_id', $bod->id)
            ->where('division_id', $department->id)
            ->where('position_id', $position->id)
            ->with('uploader')
            ->latest()
            ->get();

        $typeLabel = Document::$typeLabels[$type] ?? strtoupper($type);
        $typeColor = Document::$typeColors[$type] ?? '#3b82f6';
        $typeIcon  = Document::$typeIcons[$type] ?? 'fa-folder';

        return view('documents.files', compact('type', 'typeLabel', 'typeColor', 'typeIcon', 'bod', 'department', 'position', 'documents'));
    }

    /** Preview a document */
    public function preview(Document $document)
    {
        abort_unless(Storage::disk('public')->exists($document->file_path), 404);
        return view('documents.preview', compact('document'));
    }

    /** Download a document */
    public function download(Document $document)
    {
        abort_unless(Storage::disk('public')->exists($document->file_path), 404);
        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    /** Upload form */
    public function create(Request $request)
    {
        abort_unless(auth()->user()->canManageDocuments(), 403);

        $bods        = Bod::orderBy('order')->get();
        $departments = collect();
        $positions   = collect();
        $type        = $request->query('type', 'sop');
        $bodId       = $request->query('bod_id');
        $departmentId = $request->query('department_id');
        $positionId  = $request->query('position_id');

        if ($bodId) {
            $departments = Division::where('bod_id', $bodId)->orderBy('order')->get();
        }

        if ($departmentId) {
            $positions = Position::where('division_id', $departmentId)->orderBy('order')->get();
        }

        return view('admin.documents.create', compact(
            'bods', 'departments', 'positions', 'type',
            'bodId', 'departmentId', 'positionId'
        ));
    }

    /** Store uploaded document */
    public function store(Request $request)
    {
        abort_unless(auth()->user()->canManageDocuments(), 403);

        $rules = [
            'type'          => 'required|in:sop,job_desk,bpm,struktur',
            'bod_id'        => 'required|exists:bods,id',
            'division_id'   => 'required|exists:divisions,id',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'file'          => 'required|file|max:51200', // 50MB max
        ];

        // position_id required for job_desk only
        if ($request->type === 'job_desk') {
            $rules['position_id'] = 'required|exists:positions,id';
        }

        $request->validate($rules);

        $file      = $request->file('file');
        $fileName  = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $path      = $file->store('documents/' . $request->type, 'public');

        Document::create([
            'type'           => $request->type,
            'bod_id'         => $request->bod_id,
            'division_id'    => $request->division_id,
            'position_id'    => $request->type === 'job_desk' ? $request->position_id : null,
            'title'          => $request->title,
            'description'    => $request->description,
            'file_path'      => $path,
            'file_name'      => $fileName,
            'file_type'      => $file->getMimeType(),
            'file_extension' => $extension,
            'file_size'      => $file->getSize(),
            'uploaded_by'    => auth()->id(),
        ]);

        // Redirect based on type
        if ($request->type === 'job_desk' && $request->position_id) {
            return redirect()
                ->route('documents.files', [$request->type, $request->bod_id, $request->division_id, $request->position_id])
                ->with('success', 'Dokumen berhasil diupload!');
        }

        return redirect()
            ->route('documents.positions', [$request->type, $request->bod_id, $request->division_id])
            ->with('success', 'Dokumen berhasil diupload!');
    }

    /** Edit form */
    public function edit(Document $document)
    {
        abort_unless(auth()->user()->canManageDocuments(), 403);

        $bods        = Bod::orderBy('order')->get();
        $departments = Division::where('bod_id', $document->bod_id)->orderBy('order')->get();
        $positions   = Position::where('division_id', $document->division_id)->orderBy('order')->get();

        return view('admin.documents.edit', compact('document', 'bods', 'departments', 'positions'));
    }

    /** Update document */
    public function update(Request $request, Document $document)
    {
        abort_unless(auth()->user()->canManageDocuments(), 403);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'file'        => 'nullable|file|max:51200',
        ]);

        $data = [
            'title'       => $request->title,
            'description' => $request->description,
        ];

        if ($request->hasFile('file')) {
            // Delete old file
            Storage::disk('public')->delete($document->file_path);

            $file = $request->file('file');
            $data['file_path']      = $file->store('documents/' . $document->type, 'public');
            $data['file_name']      = $file->getClientOriginalName();
            $data['file_type']      = $file->getMimeType();
            $data['file_extension'] = strtolower($file->getClientOriginalExtension());
            $data['file_size']      = $file->getSize();
        }

        $document->update($data);

        // Redirect based on type
        if ($document->type === 'job_desk' && $document->position_id) {
            return redirect()
                ->route('documents.files', [$document->type, $document->bod_id, $document->division_id, $document->position_id])
                ->with('success', 'Dokumen berhasil diperbarui!');
        }

        return redirect()
            ->route('documents.positions', [$document->type, $document->bod_id, $document->division_id])
            ->with('success', 'Dokumen berhasil diperbarui!');
    }

    /** Delete document */
    public function destroy(Document $document)
    {
        abort_unless(auth()->user()->canManageDocuments(), 403);

        $type       = $document->type;
        $bodId      = $document->bod_id;
        $divId      = $document->division_id;
        $posId      = $document->position_id;

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        if ($type === 'job_desk' && $posId) {
            return redirect()
                ->route('documents.files', [$type, $bodId, $divId, $posId])
                ->with('success', 'Dokumen berhasil dihapus!');
        }

        return redirect()
            ->route('documents.positions', [$type, $bodId, $divId])
            ->with('success', 'Dokumen berhasil dihapus!');
    }
}
