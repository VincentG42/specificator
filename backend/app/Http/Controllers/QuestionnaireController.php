<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    public function sections() {
        return response()->json(config('questionnaire.sections'));
    }

    public function questions($section) {
        $questions = config("questionnaire.sections.{$section}.questions");

        if (!$questions) {
            return response()->json(['message' => 'Section not found'], 404);
        }

        return response()->json($questions);
    }

    public function storeAnswer(Request $request) {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'section_key' => 'required|string',
            'question_key' => 'required|string',
            'answer_value' => 'present',
            'is_not_applicable' => 'boolean',
        ]);

        $answer = Answer::updateOrCreate(
            [
                'project_id' => $validated['project_id'],
                'section_key' => $validated['section_key'],
                'question_key' => $validated['question_key'],
            ],
            [
                'answer_value' => $validated['answer_value'],
                'is_not_applicable' => $validated['is_not_applicable'] ?? false,
            ]
        );

        return response()->json($answer, 201);
    }

    public function updateAnswer(Request $request, Answer $answer) {
        $validated = $request->validate([
            'answer_value' => 'present',
            'is_not_applicable' => 'boolean',
        ]);

        $answer->update($validated);

        return response()->json($answer);
    }
}