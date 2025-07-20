<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Project::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = Project::create($validated);

        return response()->json($project, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return $project->load('answers');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'progress' => 'integer|min:0|max:100',
            'metadata' => 'nullable|json',
        ]);

        $project->update($validated);

        return response()->json($project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json(null, 204);
    }

    public function export(Project $project, ExportService $exportService)
    {
        try {
            $files = $exportService->generateAllFiles($project);
            $zipFilename = "specifications-{$project->id}.zip";
            $zipPath = $exportService->createZip($files, $zipFilename);

            return Response::download($zipPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}