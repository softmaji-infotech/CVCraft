<?php

declare(strict_types=1);

use App\Core\ATS;
use App\Core\Helpers;
use App\Core\PDF;
use App\Core\ResumeEngine;

$root = dirname(__DIR__);
if (is_file($root . '/vendor/autoload.php')) {
    require $root . '/vendor/autoload.php';
} else {
    spl_autoload_register(static function (string $class) use ($root): void {
        $prefix = 'App\\';
        if (!str_starts_with($class, $prefix)) {
            return;
        }
        $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
        $path = $root . '/app/' . $relative . '.php';
        if (is_file($path)) {
            require $path;
        }
    });
}

if (class_exists(\Dotenv\Dotenv::class) && is_file($root . '/.env')) {
    \Dotenv\Dotenv::createImmutable($root)->safeLoad();
}

Helpers::startSecureSession();
$engine = new ResumeEngine(new ATS(), new PDF());

$action = $_GET['action'] ?? 'home';
$template = preg_replace('/[^a-z]/', '', (string) ($_REQUEST['template'] ?? ($_SESSION['template'] ?? 'modern')));
$_SESSION['template'] = $template ?: 'modern';

function requestData(): array
{
    $raw = file_get_contents('php://input') ?: '';
    $json = json_decode($raw, true);

    return is_array($json) ? $json : $_POST;
}

function page(string $title, string $body, string $description = 'Build ATS-friendly resumes quickly.'): void
{
    echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . Helpers::e($title) . '</title>';
    echo '<meta name="description" content="' . Helpers::e($description) . '">';
    echo '<script src="https://cdn.tailwindcss.com"></script>';
    echo '<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>';
    echo '<link rel="stylesheet" href="/assets/css/app.css">';
    echo '</head><body class="bg-[#f9fafb] text-[#111827]">' . $body . '<script src="/assets/js/app.js"></script></body></html>';
}

switch ($action) {
    case 'templates':
        $cards = ['modern', 'ats', 'executive', 'fresher'];
        $html = '<main class="max-w-6xl mx-auto p-6"><h1 class="text-3xl font-bold mb-6">Choose a Template</h1><div class="grid md:grid-cols-2 gap-4">';
        foreach ($cards as $card) {
            $html .= '<div class="bg-white p-5 rounded-xl shadow"><h2 class="text-xl font-semibold mb-2 capitalize">' . Helpers::e($card) . '</h2><p class="text-gray-500 mb-3">ATS-friendly professional template</p><a class="inline-block bg-[#2563eb] text-white px-4 py-2 rounded" href="?action=builder&template=' . Helpers::e($card) . '">Use This Template</a></div>';
        }
        $html .= '</div></main>';
        page('Templates', $html, 'Preview and choose resume templates.');
        break;

    case 'builder':
        $resume = $engine->getResume();
        $csrf = Helpers::csrfToken();
        $templateEsc = Helpers::e($_SESSION['template']);
        $preview = $engine->renderTemplate($_SESSION['template'], $resume);
        $resumeJson = htmlspecialchars(json_encode($resume, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
        $body = <<<HTML
<main class="max-w-7xl mx-auto p-4" x-data="builderApp('{$csrf}', '{$templateEsc}', '{$resumeJson}')" x-init="init()">
  <div class="mb-4 flex justify-between items-center"><h1 class="text-2xl font-bold">Resume Builder</h1><a class="text-[#2563eb]" href="?action=templates">Change Template</a></div>
  <div class="grid lg:grid-cols-2 gap-4">
    <section class="bg-white rounded-xl shadow p-4 space-y-4">
      <form id="resumeForm" @input.debounce.400ms="refresh()" class="space-y-3">
        <input type="hidden" name="csrf_token" :value="csrf">
        <div class="grid md:grid-cols-2 gap-3">
          <input x-model="resume.name" placeholder="Full Name" class="border p-2 rounded">
          <input x-model="resume.title" placeholder="Job Title" class="border p-2 rounded">
          <input x-model="resume.email" placeholder="Email" class="border p-2 rounded">
          <input x-model="resume.phone" placeholder="Phone" class="border p-2 rounded">
          <input x-model="resume.address" placeholder="Address" class="border p-2 rounded md:col-span-2">
          <input x-model="resume.linkedin" placeholder="LinkedIn URL" class="border p-2 rounded">
          <input x-model="resume.portfolio" placeholder="Portfolio URL" class="border p-2 rounded">
        </div>
        <textarea x-model="resume.summary" placeholder="Professional Summary" class="border p-2 rounded w-full"></textarea>

        <div><h3 class="font-semibold">Skills</h3>
          <template x-for="(skill, i) in resume.skills" :key="'s'+i"><div class="flex gap-2 mb-2"><input x-model="resume.skills[i]" class="border p-2 rounded flex-1" placeholder="Skill"><button type="button" @click="resume.skills.splice(i,1); refresh()" class="text-red-600">Remove</button></div></template>
          <button type="button" class="text-[#2563eb]" @click="resume.skills.push(''); refresh()">+ Add Skill</button>
        </div>

        <div><h3 class="font-semibold">Experience</h3>
          <template x-for="(exp, i) in resume.experience" :key="'e'+i"><div class="border rounded p-2 space-y-2 mb-2"><input x-model="exp.company" placeholder="Company" class="border p-2 rounded w-full"><input x-model="exp.role" placeholder="Role" class="border p-2 rounded w-full"><input x-model="exp.duration" placeholder="Duration" class="border p-2 rounded w-full"><textarea x-model="exp.description" placeholder="Description" class="border p-2 rounded w-full"></textarea><button type="button" @click="resume.experience.splice(i,1); refresh()" class="text-red-600">Remove</button></div></template>
          <button type="button" class="text-[#2563eb]" @click="resume.experience.push({company:'',role:'',duration:'',description:''}); refresh()">+ Add Experience</button>
        </div>

        <div><h3 class="font-semibold">Education</h3>
          <template x-for="(edu, i) in resume.education" :key="'d'+i"><div class="border rounded p-2 space-y-2 mb-2"><input x-model="edu.institution" placeholder="Institution" class="border p-2 rounded w-full"><input x-model="edu.degree" placeholder="Degree" class="border p-2 rounded w-full"><input x-model="edu.duration" placeholder="Duration" class="border p-2 rounded w-full"><button type="button" @click="resume.education.splice(i,1); refresh()" class="text-red-600">Remove</button></div></template>
          <button type="button" class="text-[#2563eb]" @click="resume.education.push({institution:'',degree:'',duration:''}); refresh()">+ Add Education</button>
        </div>

        <div><h3 class="font-semibold">Projects</h3>
          <template x-for="(project, i) in resume.projects" :key="'p'+i"><div class="border rounded p-2 space-y-2 mb-2"><input x-model="project.name" placeholder="Project Name" class="border p-2 rounded w-full"><input x-model="project.url" placeholder="Project URL" class="border p-2 rounded w-full"><textarea x-model="project.description" placeholder="Description" class="border p-2 rounded w-full"></textarea><button type="button" @click="resume.projects.splice(i,1); refresh()" class="text-red-600">Remove</button></div></template>
          <button type="button" class="text-[#2563eb]" @click="resume.projects.push({name:'',url:'',description:''}); refresh()">+ Add Project</button>
        </div>

        <div><h3 class="font-semibold">Certifications</h3><template x-for="(item, i) in resume.certifications" :key="'c'+i"><div class="flex gap-2 mb-2"><input x-model="resume.certifications[i]" class="border p-2 rounded flex-1" placeholder="Certification"><button type="button" @click="resume.certifications.splice(i,1); refresh()" class="text-red-600">Remove</button></div></template><button type="button" class="text-[#2563eb]" @click="resume.certifications.push(''); refresh()">+ Add Certification</button></div>
        <div><h3 class="font-semibold">Languages</h3><template x-for="(item, i) in resume.languages" :key="'l'+i"><div class="flex gap-2 mb-2"><input x-model="resume.languages[i]" class="border p-2 rounded flex-1" placeholder="Language"><button type="button" @click="resume.languages.splice(i,1); refresh()" class="text-red-600">Remove</button></div></template><button type="button" class="text-[#2563eb]" @click="resume.languages.push(''); refresh()">+ Add Language</button></div>
        <div><h3 class="font-semibold">Achievements</h3><template x-for="(item, i) in resume.achievements" :key="'a'+i"><div class="flex gap-2 mb-2"><input x-model="resume.achievements[i]" class="border p-2 rounded flex-1" placeholder="Achievement"><button type="button" @click="resume.achievements.splice(i,1); refresh()" class="text-red-600">Remove</button></div></template><button type="button" class="text-[#2563eb]" @click="resume.achievements.push(''); refresh()">+ Add Achievement</button></div>
      </form>
      <form method="post" action="?action=download&template={$templateEsc}"><input type="hidden" name="csrf_token" value="{$csrf}"><button class="bg-[#111827] text-white px-4 py-2 rounded">Download PDF</button></form>
    </section>
    <section class="space-y-4">
      <div class="bg-white rounded-xl shadow p-4"><h2 class="font-semibold mb-2">Live Preview</h2><div id="previewPane">{$preview}</div></div>
      <div class="bg-white rounded-xl shadow p-4"><h2 class="font-semibold mb-2">ATS Score</h2><p class="text-3xl font-bold text-[#2563eb]" x-text="ats.score + '%'">0%</p><ul class="list-disc pl-5 text-sm text-gray-600"><template x-for="(s,i) in ats.suggestions" :key="i"><li x-text="s"></li></template></ul></div>
    </section>
  </div>
</main>
<script>
function builderApp(csrf, template, initialJson){
  return {
    csrf,
    template,
    resume: JSON.parse(initialJson || '{}'),
    ats: {score:0,suggestions:[]},
    init(){ this.refresh(); },
    async refresh(){
      const payload = {csrf_token:this.csrf, template:this.template, resume:this.resume};
      const [previewRes, atsRes] = await Promise.all([
        fetch('?action=preview', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload)}),
        fetch('?action=ats', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload)})
      ]);
      if (previewRes.ok) {
        document.getElementById('previewPane').innerHTML = await previewRes.text();
      }
      if (atsRes.ok) {
        this.ats = await atsRes.json();
      }
    }
  }
}
</script>
HTML;
        page('Builder', $body, 'Create and preview your ATS-friendly resume.');
        break;

    case 'preview':
        $data = requestData();
        if (!Helpers::verifyCsrf($data['csrf_token'] ?? null)) {
            http_response_code(419);
            exit('Invalid CSRF token');
        }
        $resume = $engine->setResume((array) ($data['resume'] ?? []));
        $_SESSION['template'] = preg_replace('/[^a-z]/', '', (string) ($data['template'] ?? 'modern')) ?: 'modern';
        echo $engine->renderTemplate($_SESSION['template'], $resume);
        break;

    case 'ats':
        header('Content-Type: application/json');
        $data = requestData();
        if (!Helpers::verifyCsrf($data['csrf_token'] ?? null)) {
            http_response_code(419);
            echo json_encode(['error' => 'Invalid CSRF token']);
            break;
        }
        $resume = $engine->setResume((array) ($data['resume'] ?? []));
        echo json_encode($engine->calculateATS($resume), JSON_THROW_ON_ERROR);
        break;

    case 'download':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Helpers::verifyCsrf($_POST['csrf_token'] ?? null)) {
            http_response_code(419);
            exit('Invalid request');
        }
        $resume = $engine->getResume();
        $file = $engine->generatePDF($_SESSION['template'], $resume);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="resume.pdf"');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        $engine->cleanupPdf($file);
        break;

    case 'seo':
        $meta = Helpers::seoMeta((string) ($_GET['page'] ?? 'resume-builder'));
        $body = '<main class="max-w-4xl mx-auto p-6"><h1 class="text-3xl font-bold mb-4">' . Helpers::e($meta['title']) . '</h1><p class="text-gray-600 mb-4">' . Helpers::e($meta['description']) . '</p><a href="?action=builder" class="bg-[#2563eb] text-white px-4 py-2 rounded">Build Resume Free</a></main>';
        page($meta['title'], $body, $meta['description']);
        break;

    case 'home':
    default:
        $body = '<main class="max-w-6xl mx-auto p-6 space-y-8"><section class="bg-white rounded-xl shadow p-8"><h1 class="text-4xl font-bold mb-3">SoftMaji Resume Builder</h1><p class="text-gray-600 mb-5">Fill details → get ATS-ready resume instantly.</p><div class="flex gap-3"><a class="bg-[#2563eb] text-white px-4 py-2 rounded" href="?action=templates">Build Resume Free</a><a class="border border-gray-300 px-4 py-2 rounded" href="?action=seo&page=ats-resume-checker">Check ATS</a></div></section><section><h2 class="text-2xl font-semibold mb-3">How it works</h2><ol class="list-decimal pl-5 text-gray-700"><li>Select template</li><li>Fill resume details</li><li>Preview & ATS check</li><li>Download PDF</li></ol></section><section><h2 class="text-2xl font-semibold mb-3">FAQ</h2><p class="text-gray-700">No signup needed. Session-based free builder for quick ATS-friendly resumes.</p></section></main>';
        page('SoftMaji Resume Builder', $body);
        break;
}
