<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['user', 'tags'])->get();

        return response()->json([
            'data' => $tasks
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:Pendiente,En progreso,Completada,Cancelada'],
            'priority' => ['required', 'in:Baja,Media,Alta'],
            'due_date' => ['required', 'date'],
            'user_id' => ['required', 'exists:users,id']
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $task = Task::create($request->all());

        if ($task) {
            return response()->json([
                'message' => 'Tarea creada exitosamente',
                'data' => $task
            ], 201);
        }

        return response()->json([
            'error' => 'No se pudo crear la tarea'
        ], 500);
    }

    public function show($id)
    {
        $task = Task::with(['user', 'tags'])->find($id);

        if (!$task) {
            return response()->json([
                'error' => 'Tarea no encontrada'
            ], 404);
        }

        return response()->json([
            'data' => $task
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'error' => 'Tarea no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:Pendiente,En progreso,Completada,Cancelada'],
            'priority' => ['sometimes', 'in:Baja,Media,Alta'],
            'due_date' => ['sometimes', 'date'],
            'user_id' => ['sometimes', 'exists:users,id']
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $task->update($request->all());

        return response()->json([
            'message' => 'Tarea actualizada exitosamente',
            'data' => $task
        ], 200);
    }

    public function attachTags(Request $request, $taskId)
    {
        $validator = Validator::make($request->all(), [
            'tags' => ['required', 'array'],
            'tags.*' => ['exists:tags,id']
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $task = Task::find($taskId);

        if (!$task) {
            return response()->json([
                'error' => 'Tarea no encontrada'
            ], 404);
        }

        $task->tags()->sync($request->tags);

        return response()->json([
            'message' => 'Tags actualizados exitosamente',
            'data' => $task->load('tags')
        ], 200);
    }

    public function getByStatus($status)
    {
        $tasks = Task::where('status', $status)->with(['user', 'tags'])->get();

        return response()->json([
            'data' => $tasks
        ], 200);
    }

    public function getByPriority($priority)
    {
        $tasks = Task::where('priority', $priority)->with(['user', 'tags'])->get();

        return response()->json([
            'data' => $tasks
        ], 200);
    }

    public function getUserTasks($userId)
    {
        $tasks = Task::where('user_id', $userId)
                    ->with(['tags'])
                    ->orderBy('due_date')
                    ->get();

        return response()->json([
            'data' => $tasks
        ], 200);
    }
}
