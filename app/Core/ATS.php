<?php

declare(strict_types=1);

namespace App\Core;

final class ATS
{
    public function score(array $resume): array
    {
        $score = 0;
        $suggestions = [];

        $summary = trim((string) ($resume['summary'] ?? ''));
        $skills = $resume['skills'] ?? [];
        $experience = $resume['experience'] ?? [];
        $education = $resume['education'] ?? [];

        if ($summary !== '') {
            $score += 10;
        } else {
            $suggestions[] = 'Add a concise professional summary.';
        }

        if (count($skills) >= 5) {
            $score += 10;
        } else {
            $score += 5;
            $suggestions[] = 'Add at least 5 relevant skills.';
        }

        if (count($experience) > 0) {
            $score += 20;
            foreach ($experience as $item) {
                if (empty($item['company']) || empty($item['role']) || empty($item['duration']) || empty($item['description'])) {
                    $suggestions[] = 'Complete all experience fields for better ATS parsing.';
                    $score -= 4;
                    break;
                }
            }
        } else {
            $suggestions[] = 'Add work experience entries.';
        }

        if (count($education) > 0) {
            $score += 10;
        } else {
            $suggestions[] = 'Add at least one education entry.';
        }

        $keywords = ['php', 'team', 'project', 'development', 'analysis', 'management', 'engineer', 'communication'];
        $blob = strtolower(json_encode($resume, JSON_THROW_ON_ERROR));
        $matched = 0;
        foreach ($keywords as $keyword) {
            if (str_contains($blob, $keyword)) {
                $matched++;
            }
        }
        $keywordScore = (int) round(($matched / count($keywords)) * 20);
        $score += $keywordScore;
        if ($keywordScore < 12) {
            $suggestions[] = 'Add more role-specific keywords to improve ATS match.';
        }

        $score += 20;

        $contactPoints = 0;
        foreach (['name', 'email', 'phone', 'address'] as $field) {
            if (!empty($resume[$field])) {
                $contactPoints += 2;
            }
        }
        if (!empty($resume['linkedin'])) {
            $contactPoints += 1;
        }
        if (!empty($resume['portfolio'])) {
            $contactPoints += 1;
        }
        $score += min($contactPoints, 10);
        if ($contactPoints < 8) {
            $suggestions[] = 'Complete contact information (email, phone, address, links).';
        }

        $score = max(0, min(100, $score));

        return [
            'score' => $score,
            'suggestions' => array_values(array_unique($suggestions)),
        ];
    }
}
