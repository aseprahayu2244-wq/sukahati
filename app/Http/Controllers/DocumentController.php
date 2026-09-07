<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Document;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    private array $validTypes = ['sop', 'job', 'job_analysis', 'bpmn'];

    /** Browse: Show all divisions for a document type */
    public function index(string $type)
    {
        abort_unless(in_array($type, $this->validTypes), 404);

        $divisions = Division::withCount([
            'documents as doc_count' => fn($q) => $q->where('type', $type),
            'positions'
        ])->orderBy('order')->get();

        $typeLabel = Document::$typeLabels[$type] ?? strtoupper($type);
        $typeName  = $typeLabel;
        $typeColor = Document::$typeColors[$type] ?? '#3b82f6';

        return view('documents.index', compact('type', 'typeName', 'typeLabel', 'typeColor', 'divisions'));
    }

    /** Browse: Show positions within a division */
    public function positions(string $type, Division $division)
    {
        abort_unless(in_array($type, $this->validTypes), 404);

        $positions = Position::where('division_id', $division->id)
            ->withCount([
                'documents as doc_count' => fn($q) => $q->where('type', $type)
            ])
            ->orderBy('order')
            ->get();

        $typeLabel = Document::$typeLabels[$type] ?? strtoupper($type);
        $typeName  = $typeLabel;
        $typeColor = Document::$typeColors[$type] ?? '#3b82f6';

        return view('documents.positions', compact('type', 'typeName', 'typeLabel', 'typeColor', 'division', 'positions'));
    }

    /** Browse: Show documents for a position */
    public function files(string $type, Division $division, Position $position)
    {
        abort_unless(in_array($type, $this->validTypes), 404);

        $documents = Document::where('type', $type)
            ->where('division_id', $division->id)
            ->where('position_id', $position->id)
            ->with('uploader')
            ->latest()
            ->get();

        $typeLabel = Document::$typeLabels[$type] ?? strtoupper($type);
        $typeName  = $typeLabel;
        $typeColor = Document::$typeColors[$type] ?? '#3b82f6';

        return view('documents.files', compact('type', 'typeName', 'typeLabel', 'typeColor', 'division', 'position', 'documents'));
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
        $divisions = Division::orderBy('order')->get();
        $positions = collect();
        $type      = $request->query('type', 'sop');
        $divisionId = $request->query('division_id');
        $selectedType = $type;
        $selectedDivisionId = $divisionId;

        if ($divisionId) {
            $positions = Position::where('division_id', $divisionId)->orderBy('order')->get();
        }

        return view('admin.documents.create', compact('divisions', 'positions', 'type', 'selectedType', 'selectedDivisionId'));
    }

    /** Store uploaded document */
    public function store(Request $request)
    {
        $request->validate([
            'type'        => 'required|in:sop,job,job_analysis,bpmn',
            'division_id' => 'required|exists:divisions,id',
            'position_id' => 'required|exists:positions,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'file'        => 'required|file|max:51200', // 50MB max
        ]);

        $file      = $request->file('file');
        $fileName  = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $path      = $file->store('documents/' . $request->type, 'public');

        Document::create([
            'type'           => $request->type,
            'division_id'    => $request->division_id,
            'position_id'    => $request->position_id,
            'title'          => $request->title,
            'description'    => $request->description,
            'file_path'      => $path,
            'file_name'      => $fileName,
            'file_type'      => $file->getMimeType(),
            'file_extension' => $extension,
            'file_size'      => $file->getSize(),
            'uploaded_by'    => auth()->id(),
        ]);

        return redirect()
            ->route('documents.files', [$request->type, $request->division_id, $request->position_id])
            ->with('success', 'Dokumen berhasil diupload!');
    }

    /** Edit form */
    public function edit(Document $document)
    {
        $divisions = Division::orderBy('order')->get();
        $positions = Position::where('division_id', $document->division_id)->orderBy('order')->get();
        return view('admin.documents.edit', compact('document', 'divisions', 'positions'));
    }

    /** Update document */
    public function update(Request $request, Document $document)
    {
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

        return redirect()
            ->route('documents.files', [$document->type, $document->division_id, $document->position_id])
            ->with('success', 'Dokumen berhasil diperbarui!');
    }

    /** Delete document */
    public function destroy(Document $document)
    {
        $redirectParams = [$document->type, $document->division_id, $document->position_id];
        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()
            ->route('documents.files', $redirectParams)
            ->with('success', 'Dokumen berhasil dihapus!');
    }
}


