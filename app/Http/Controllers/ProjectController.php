<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['user', 'tasks'])->get()->map(function($project) {
            return [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'user' => $project->user,
                'tasks_count' => $project->getTasksCount(),
                'completed_tasks' => $project->getCompletedTasksCount(),
                'progress_percentage' => $project->getProgressPercentage(),
                'created_at' => $project->created_at
            ];
        });

        return response()->json([
            'data' => $projects
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'user_id' => ['required', 'exists:users,id']
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $project = Project::create($request->all());

        return response()->json([
            'message' => 'Proyecto creado exitosamente',
            'data' => $project
        ], 201);
    }

    public function show($id)
    {
        $project = Project::with(['user', 'tasks.tags'])->find($id);

        if (!$project) {
            return response()->json([
                'error' => 'Proyecto no encontrado'
            ], 404);
        }

        $projectData = [
            'id' => $project->id,
            'name' => $project->name,
            'description' => $project->description,
            'user' => $project->user,
            'tasks' => $project->tasks,
            'tasks_count' => $project->getTasksCount(),
            'completed_tasks' => $project->getCompletedTasksCount(),
            'progress_percentage' => $project->getProgressPercentage(),
            'created_at' => $project->created_at
        ];

        return response()->json([
            'data' => $projectData
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'error' => 'Proyecto no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'user_id' => ['sometimes', 'exists:users,id']
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $project->update($request->all());

        return response()->json([
            'message' => 'Proyecto actualizado exitosamente',
            'data' => $project
        ], 200);
    }

    public function destroy($id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'error' => 'Proyecto no encontrado'
            ], 404);
        }

        // Eliminar todas las tareas asociadas
        $project->tasks()->delete();
        $project->delete();

        return response()->json([
            'message' => 'Proyecto y sus tareas eliminados exitosamente'
        ], 200);
    }

    public function addTaskToProject(Request $request, $projectId)
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

        $project = Project::find($projectId);

        if (!$project) {
            return response()->json([
                'error' => 'Proyecto no encontrado'
            ], 404);
        }

        $task = $project->tasks()->create($request->all());

        return response()->json([
            'message' => 'Tarea agregada al proyecto exitosamente',
            'data' => $task
        ], 201);
    }

    public function getProjectStats($id)
    {
        $project = Project::with('tasks')->find($id);

        if (!$project) {
            return response()->json([
                'error' => 'Proyecto no encontrado'
            ], 404);
        }

        $stats = [
            'total_tasks' => $project->getTasksCount(),
            'completed_tasks' => $project->getCompletedTasksCount(),
            'progress_percentage' => $project->getProgressPercentage(),
            'tasks_by_priority' => [
                'alta' => $project->tasks()->where('priority', 'Alta')->count(),
                'media' => $project->tasks()->where('priority', 'Media')->count(),
                'baja' => $project->tasks()->where('priority', 'Baja')->count(),
            ],
            'tasks_by_status' => [
                'pendiente' => $project->tasks()->where('status', 'Pendiente')->count(),
                'en_progreso' => $project->tasks()->where('status', 'En progreso')->count(),
                'completada' => $project->tasks()->where('status', 'Completada')->count(),
                'cancelada' => $project->tasks()->where('status', 'Cancelada')->count(),
            ]
        ];

        return response()->json([
            'data' => $stats
        ], 200);
    }
}
