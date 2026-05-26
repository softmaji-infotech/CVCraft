# SoftMaji Resume Builder (CVCraft)

Lightweight ATS-friendly resume builder in PHP 8.2+ with single entry routing and session-based storage.

## Features
- Single entry point: `/public/index.php?action=...`
- Templates: modern, ats, executive, fresher
- Live preview (HTML only), ATS scoring, PDF download
- mPDF watermark: `Built with SoftMaji`
- Footer: `Built with SoftMaji | resume.softmaji.in`
- CSRF protection, input sanitization, escaped output
- Session-only v1 (no DB required)

## Project Structure
- `app/Core/ResumeEngine.php`
- `app/Core/PDF.php`
- `app/Core/Helpers.php`
- `app/Core/DB.php` (stub)
- `app/Core/ATS.php`
- `templates/*.php`
- `public/index.php`
- `public/assets/css`, `public/assets/js`
- `storage/pdf`, `storage/temp`

## Local Setup
1. Ensure PHP 8.2+ and Composer are installed.
2. Install dependencies:
   ```bash
   composer install
   ```
3. Configure environment:
   ```bash
   cp .env.example .env
   ```
4. Make storage writable:
   ```bash
   chmod -R 775 storage
   ```
5. Run local server:
   ```bash
   php -S localhost:8000 -t public
   ```
6. Open:
   - `http://localhost:8000/?action=home`
   - `http://localhost:8000/?action=templates`
   - `http://localhost:8000/?action=builder`

## Shared Hosting Deployment
1. Upload project files.
2. Run `composer install --no-dev --optimize-autoloader`.
3. Set document root to `/public`.
4. Ensure `storage/pdf` and `storage/temp` are writable.
5. Create `.env` from `.env.example` (optional; defaults work without it).
6. Access routes with `?action=`.

## Required Routes
- `?action=home`
- `?action=templates`
- `?action=builder`
- `?action=preview` (POST, HTML)
- `?action=ats` (POST, JSON)
- `?action=download` (POST, PDF)
- `?action=seo&page=resume-builder`
