<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', Rule::in(['pending', 'in_progress', 'completed'])],
            'due_date' => ['nullable', 'date'],
            'task_category_id' => ['nullable', 'integer', 'exists:task_categories,id'],
        ]);

        $task = Task::create([
            ...$validated,
            'status' => $validated['status'] ?? 'pending',
        ]);

        return response()->json($task->load('taskCategory'), 201);
    }
}
