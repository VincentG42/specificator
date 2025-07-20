<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ExportService
{
    public function generateAllFiles(Project $project): array
    {
        $files = [];
        $sections = Config::get('questionnaire.sections');

        // Generate section files
        foreach ($sections as $sectionKey => $section) {
            $filename = sprintf('%02d-%s.md', $section['order'], $sectionKey);
            $content = $this->generateSectionFile($project, $sectionKey);
            $files[$filename] = $content;
        }

        // Generate index file
        $files['00-index.md'] = $this->generateIndexFile($project, $sections);

        // Generate metadata file
        $files['metadata.json'] = json_encode($this->generateMetadata($project, $files), JSON_PRETTY_PRINT);

        return $files;
    }

    public function generateSectionFile(Project $project, string $sectionKey): string
    {
        $section = Config::get("questionnaire.sections.{$sectionKey}");
        $answers = $project->answers()->where('section_key', $sectionKey)->get();

        $content = "# {$section['icon']} {$section['title']}\n\n";
        $content .= "> {$section['description']}\n\n";
        $content .= "**Projet :** {$project->name}\n";
        $content .= "**Généré le :** " . now()->format('d/m/Y à H:i') . "\n\n";
        $content .= "---\n\n";

        foreach ($section['questions'] as $questionKey => $question) {
            $answer = $answers->where('question_key', $questionKey)->first();

            $content .= "## {$question['text']}\n\n";

            if ($answer && !$answer->is_not_applicable) {
                $value = $answer->answer_value['value'] ?? '';
                if (is_array($value)) {
                    $content .= "- " . implode("\n- ", $value) . "\n\n";
                } else {
                    $content .= $value . "\n\n";
                }
            } elseif ($answer && $answer->is_not_applicable) {
                $content .= "*Non applicable*\n\n";
            } else {
                $content .= "*Non renseigné*\n\n";
            }

            $content .= "---\n\n";
        }

        return $content;
    }

    private function generateIndexFile(Project $project, array $sections): string
    {
        $content = "# Index des Spécifications - {$project->name}\n\n";
        $content .= "**Projet :** {$project->name}\n";
        $content .= "**Généré le :** " . now()->format('d/m/Y à H:i') . "\n\n";
        $content .= "---\n\n";
        $content .= "## Sections\n\n";

        foreach ($sections as $sectionKey => $section) {
            $filename = sprintf('%02d-%s.md', $section['order'], $sectionKey);
            $content .= "- [{$section['icon']} {$section['title']}]({$filename})\n";
        }

        return $content;
    }

    private function generateMetadata(Project $project, array $files): array
    {
        $totalSize = 0;
        foreach ($files as $fileContent) {
            $totalSize += strlen($fileContent);
        }

        $totalQuestions = 0;
        $answeredQuestions = 0;
        $notApplicable = 0;

        foreach (Config::get('questionnaire.sections') as $section) {
            foreach ($section['questions'] as $questionKey => $question) {
                $totalQuestions++;
                $answer = $project->answers->where('section_key', $section['key'] ?? '')->where('question_key', $questionKey)->first();
                if ($answer) {
                    if ($answer->is_not_applicable) {
                        $notApplicable++;
                    } else if (!empty($answer->answer_value['value'])) {
                        $answeredQuestions++;
                    }
                }
            }
        }

        $completionRate = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100, 0) : 0;

        return [
            'project' => [
                'name' => $project->name,
                'id' => $project->id,
                'created_at' => $project->created_at->toIso8601String(),
                'exported_at' => now()->toIso8601String()
            ],
            'questionnaire' => [
                'version' => '1.0',
                'completion_rate' => $completionRate,
                'sections_completed' => $project->answers->unique('section_key')->count(),
                'total_sections' => count(Config::get('questionnaire.sections'))
            ],
            'export' => [
                'format' => 'markdown',
                'files_generated' => count($files),
                'total_size' => round($totalSize / 1024, 2) . 'KB',
                'generator' => 'Générateur de Specs v1.0'
            ],
            'stats' => [
                'total_questions' => $totalQuestions,
                'answered_questions' => $answeredQuestions,
                'not_applicable' => $notApplicable,
                'help_requests' => 0 // Not implemented yet
            ]
        ];
    }

    public function createZip(array $files, string $zipFilename): string
    {
        $zip = new ZipArchive();
        $zipPath = Storage::path($zipFilename);

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($files as $filename => $content) {
                $zip->addFromString($filename, $content);
            }
            $zip->close();
            return $zipPath;
        } else {
            throw new \Exception('Could not create zip archive.');
        }
    }
}