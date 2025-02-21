<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'comment' => 'required|string'
        ]);

        $comment = new Comment();
        $comment->task_id = $validated['task_id'];
        $comment->comment = $validated['comment'];
        $comment->user_id = $request->user()->id;
        $comment->save();

        return response()->json([
            'data' => $comment->load(['user:id,name,email', 'task:id,title'])
        ], 201);
    }

    public function destroy(Request $request, Comment $comment)
    {
        if ($comment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();
        return response()->json(['message' => 'Comment deleted successfully'], 200);
    }
}
