<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\LLMManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LLMController extends Controller
{
    public function getHelp(Request $request, LLMManager $llmManager)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'question_key' => 'required|string',
            'user_question' => 'nullable|string|max:500'
        ]);

        $project = Project::findOrFail($request->project_id);

        // Vérification quota session
        $sessionRequests = session('llm_requests', 0);
        if ($sessionRequests >= config('llm.limits.max_requests_per_session', 10)) {
            return response()->json([
                'success' => false,
                'error' => 'Limite de demandes d\'aide atteinte pour cette session (10 max)',
                'error_type' => 'quota_exceeded'
            ], 429);
        }

        try {
            $response = $llmManager->getHelp(
                $project,
                $request->question_key,
                $request->user_question ?? ''
            );

            if ($response->success) {
                // Incrémenter compteur session
                session(['llm_requests' => $sessionRequests + 1]);

                // Optionnel : Sauvegarder l\'historique (not implemented yet)
                // $this->saveHelpRequest($project, $request, $response);

                return response()->json([
                    'success' => true,
                    'content' => $response->content,
                    'metadata' => $response->metadata,
                    'remaining_requests' => config('llm.limits.max_requests_per_session', 10) - $sessionRequests - 1
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $this->formatLLMError($response->error),
                    'error_type' => 'llm_error',
                    'retry_available' => true
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('LLM Help Request Failed', [
                'project_id' => $project->id,
                'question_key' => $request->question_key,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Service d\'aide temporairement indisponible',
                'error_type' => 'service_unavailable',
                'retry_available' => true
            ], 503);
        }
    }

    private function formatLLMError(string $error): string
    {
        // Simplifier les erreurs techniques pour l\'utilisateur
        if (str_contains($error, 'timeout')) {
            return 'Le service d\'aide met trop de temps à répondre. Réessayez avec une question plus courte.';
        }

        if (str_contains($error, 'rate limit')) {
            return 'Trop de demandes simultanées. Attendez quelques secondes avant de réessayer.';
        }

        if (str_contains($error, 'invalid_api_key')) {
            return 'Configuration du service d\'aide incorrecte. Contactez l\'administrateur.';
        }

        return 'Erreur temporaire du service d\'aide. Réessayez dans quelques instants.';
    }
}