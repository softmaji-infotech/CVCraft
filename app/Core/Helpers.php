<?php

declare(strict_types=1);

namespace App\Core;

final class Helpers
{
    public static function startSecureSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $isHttps = ($_SERVER['HTTPS'] ?? 'off') !== 'off'
            || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';

        session_set_cookie_params([
            'httponly' => true,
            'secure' => $isHttps,
            'samesite' => 'Lax',
        ]);
        session_start();

        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = time();
        }
    }

    public static function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }

        return (string) $_SESSION['_csrf'];
    }

    public static function verifyCsrf(?string $token): bool
    {
        return !empty($_SESSION['_csrf']) && !empty($token) && hash_equals($_SESSION['_csrf'], $token);
    }

    public static function sanitizeString(mixed $value): string
    {
        return trim(strip_tags((string) $value));
    }

    public static function sanitizeUrl(mixed $value): string
    {
        $url = trim((string) $value);
        if ($url === '') {
            return '';
        }

        if (!preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }

        return filter_var($url, FILTER_VALIDATE_URL) ? $url : '';
    }

    public static function sanitizeResume(array $input): array
    {
        $resume = self::defaultResume();

        foreach (array_keys($resume) as $key) {
            if (!array_key_exists($key, $input)) {
                continue;
            }

            if (is_array($resume[$key])) {
                $resume[$key] = self::sanitizeListField($key, $input[$key]);
                continue;
            }

            $resume[$key] = in_array($key, ['linkedin', 'portfolio'], true)
                ? self::sanitizeUrl($input[$key])
                : self::sanitizeString($input[$key]);
        }

        return $resume;
    }

    private static function sanitizeListField(string $key, mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $stringsOnly = ['skills', 'languages', 'certifications', 'achievements'];
        if (in_array($key, $stringsOnly, true)) {
            return array_values(array_filter(array_map([self::class, 'sanitizeString'], $value), static fn($v) => $v !== ''));
        }

        $result = [];
        foreach ($value as $entry) {
            if (!is_array($entry)) {
                continue;
            }

            $clean = [];
            foreach ($entry as $k => $v) {
                $clean[$k] = $k === 'url' ? self::sanitizeUrl($v) : self::sanitizeString($v);
            }

            if (implode('', $clean) !== '') {
                $result[] = $clean;
            }
        }

        return $result;
    }

    public static function defaultResume(): array
    {
        return [
            'name' => '',
            'title' => '',
            'email' => '',
            'phone' => '',
            'address' => '',
            'linkedin' => '',
            'portfolio' => '',
            'summary' => '',
            'skills' => [],
            'experience' => [],
            'education' => [],
            'projects' => [],
            'certifications' => [],
            'languages' => [],
            'achievements' => [],
        ];
    }

    public static function seoMeta(string $page): array
    {
        $pages = [
            'resume-builder' => ['Free Resume Builder', 'Build an ATS-friendly resume online for free.'],
            'ats-resume-checker' => ['ATS Resume Checker', 'Check ATS score instantly with improvement suggestions.'],
            'fresher-resume-builder' => ['Fresher Resume Builder', 'Create a job-ready fresher resume in minutes.'],
            'software-engineer-resume-builder' => ['Software Engineer Resume Builder', 'Generate an ATS-optimized software engineer resume.'],
            'teacher-resume-builder' => ['Teacher Resume Builder', 'Create a professional teacher CV quickly and free.'],
            'cv-builder-free' => ['Free CV Builder', 'Design and download your CV for free with SoftMaji.'],
        ];

        [$title, $description] = $pages[$page] ?? ['Resume Builder', 'Build resumes fast with SoftMaji Resume Builder.'];

        return compact('title', 'description', 'page');
    }
}
