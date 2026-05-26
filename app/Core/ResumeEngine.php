<?php

declare(strict_types=1);

namespace App\Core;

final class ResumeEngine
{
    public function __construct(
        private readonly ATS $ats,
        private readonly PDF $pdf,
    ) {
    }

    public function getResume(): array
    {
        return $_SESSION['resume'] ?? Helpers::defaultResume();
    }

    public function setResume(array $input): array
    {
        $resume = Helpers::sanitizeResume($input);
        $_SESSION['resume'] = $resume;

        return $resume;
    }

    public function renderTemplate(string $template, array $resume): string
    {
        $templateFile = dirname(__DIR__, 2) . '/templates/' . basename($template) . '.php';
        if (!is_file($templateFile)) {
            $templateFile = dirname(__DIR__, 2) . '/templates/modern.php';
        }

        ob_start();
        $helper = Helpers::class;
        include $templateFile;

        return (string) ob_get_clean();
    }

    public function calculateATS(array $resume): array
    {
        return $this->ats->score($resume);
    }

    public function generatePDF(string $template, array $resume): string
    {
        $html = $this->renderTemplate($template, $resume);
        $name = $resume['name'] ?: 'resume';

        return $this->pdf->generate($html, $name);
    }

    public function cleanupPdf(string $path): void
    {
        $this->pdf->cleanupFile($path);
    }
}
