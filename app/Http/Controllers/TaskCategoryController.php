<?php

namespace App\Http\Controllers;

use App\Models\TaskCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskCategoryController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:task_categories,name'],
        ]);

        return response()->json(TaskCategory::create($validated), 201);
    }

    public function updateCategory(Request $request, TaskCategory $taskCategory)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:task_categories,name,' . $taskCategory->id],
        ]);

        $taskCategory->update($validated);

        return response()->json($taskCategory);
    }

    public function viewCategory(TaskCategory $taskCategory)
    {
        return response()->json($taskCategory);
    }

}
