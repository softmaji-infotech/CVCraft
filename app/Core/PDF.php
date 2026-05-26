<?php

declare(strict_types=1);

namespace App\Core;

use Mpdf\Mpdf;

final class PDF
{
    public function generate(string $html, string $filename): string
    {
        if (!class_exists(Mpdf::class)) {
            throw new \RuntimeException('mPDF is not installed. Run composer install.');
        }

        $pdfDir = dirname(__DIR__, 2) . '/storage/pdf';
        $tempDir = dirname(__DIR__, 2) . '/storage/temp';
        if (!is_dir($pdfDir)) {
            mkdir($pdfDir, 0775, true);
        }
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $mpdf = new Mpdf([
            'tempDir' => $tempDir,
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 14,
            'margin_bottom' => 16,
            'margin_left' => 12,
            'margin_right' => 12,
        ]);

        $watermark = $_ENV['PDF_WATERMARK'] ?? 'Built with SoftMaji';
        $footer = $_ENV['PDF_FOOTER'] ?? 'Built with SoftMaji | resume.softmaji.in';

        $mpdf->SetWatermarkText($watermark, 0.07);
        $mpdf->showWatermarkText = true;
        $mpdf->SetHTMLFooter('<div style="text-align:center;color:#6b7280;font-size:10px;">' . htmlspecialchars($footer, ENT_QUOTES, 'UTF-8') . '</div>');
        $mpdf->WriteHTML($html);

        $fullPath = $pdfDir . '/' . preg_replace('/[^a-z0-9_-]/i', '-', $filename) . '-' . time() . '.pdf';
        $mpdf->Output($fullPath, 'F');

        $this->cleanupOld($pdfDir, 600);

        return $fullPath;
    }

    public function cleanupFile(string $path): void
    {
        if (is_file($path)) {
            unlink($path);
        }
    }

    private function cleanupOld(string $path, int $ttl): void
    {
        foreach (glob($path . '/*.pdf') ?: [] as $file) {
            if (is_file($file) && (time() - filemtime($file)) > $ttl) {
                unlink($file);
            }
        }
    }
}
