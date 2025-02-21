<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'file' => 'required|file|max:10240' // máximo 10MB
        ]);

        $file = $request->file('file');
        $path = $file->store('attachments', 'public');

        $attachment = new Attachment([
            'task_id' => $request->task_id,
            'file_path' => $path,
            'uploaded_by' => $request->user()->id
        ]);
        
        $attachment->save();

        return response()->json([
            'data' => $attachment->load('uploader:id,name')
        ], 201);
    }

    public function show(Attachment $attachment)
    {
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return response()->download(storage_path('app/public/' . $attachment->file_path));
    }

    public function destroy(Request $request, Attachment $attachment)
    {
        if ($attachment->uploaded_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return response()->json(['message' => 'Attachment deleted successfully'], 200);
    }
}
