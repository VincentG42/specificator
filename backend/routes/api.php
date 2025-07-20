<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\QuestionnaireController;
use App\Http\Controllers\LLMController;

Route::middleware('api')->group(function () {
    Route::apiResource('projects', ProjectController::class);
    Route::post('projects/{project}/export', [ProjectController::class, 'export']);

    Route::get('sections', [QuestionnaireController::class, 'sections']);
    Route::get('sections/{section}/questions', [QuestionnaireController::class, 'questions']);
    Route::post('answers', [QuestionnaireController::class, 'storeAnswer']);
    Route::put('answers/{answer}', [QuestionnaireController::class, 'updateAnswer']);

    Route::post('help', [LLMController::class, 'getHelp']);
});
