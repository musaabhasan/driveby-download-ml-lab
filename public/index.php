<?php

declare(strict_types=1);

if (PHP_SAPI === 'cli-server') {
    $assetPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $assetFile = realpath(__DIR__ . $assetPath);

    if ($assetFile !== false && str_starts_with($assetFile, __DIR__) && is_file($assetFile)) {
        return false;
    }
}

use DrivebyLab\Repository\LabRepository;
use DrivebyLab\Security\Csrf;
use DrivebyLab\Security\SecurityHeaders;
use DrivebyLab\Service\DetectionService;
use DrivebyLab\Service\HtmlFeatureExtractor;
use DrivebyLab\Service\SafeSourceFetcher;
use DrivebyLab\Support\Database;
use DrivebyLab\Support\Json;
use DrivebyLab\Support\View;

require __DIR__ . '/../src/bootstrap.php';

SecurityHeaders::apply();
Csrf::start();

$config = require __DIR__ . '/../config/paper.php';
$repository = new LabRepository(Database::tryConnection());
$detector = new DetectionService(new HtmlFeatureExtractor());
$fetcher = new SafeSourceFetcher();
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($path === '/health') {
    jsonResponse(['status' => 'ok', 'service' => $config['portal']['slug']]);
}

if ($path === '/api/summary') {
    jsonResponse([
        'portal' => $config['portal'],
        'paper' => $config['paper'],
        'summary' => $repository->summary(),
        'classifiers' => $config['classifiers'],
        'features' => $config['feature_definitions'],
    ]);
}

if ($path === '/api/inspect' && $method === 'POST') {
    handleApiInspect($config, $detector, $fetcher);
}

if ($path === '/inspector' && $method === 'POST') {
    handleInspectorPost($config, $repository, $detector, $fetcher);
}

if ($path === '/inspector') {
    sendPage($config, 'Inspector', renderInspector($repository));
}

if ($path === '/paper') {
    sendPage($config, 'Paper Alignment', renderPaper($config));
}

sendPage($config, 'Dashboard', renderDashboard($config, $repository));

function handleApiInspect(array $config, DetectionService $detector, SafeSourceFetcher $fetcher): void
{
    $mode = (string) ($_POST['mode'] ?? 'html');

    try {
        [$source, $inputType, $sourceUrl] = resolveSource($mode, $_POST, $fetcher);
        $analysis = $detector->analyze($source, $config, $inputType, $sourceUrl);
        jsonResponse($analysis);
    } catch (Throwable $exception) {
        jsonResponse(['message' => $exception->getMessage()], 422);
    }
}

function handleInspectorPost(array $config, LabRepository $repository, DetectionService $detector, SafeSourceFetcher $fetcher): void
{
    if (!Csrf::valid($_POST['_csrf_token'] ?? null)) {
        sendPage($config, 'Session expired', '<section class="panel"><h1>Session expired</h1><p>Please refresh and try again.</p></section>', 419);
    }

    try {
        [$source, $inputType, $sourceUrl] = resolveSource((string) ($_POST['mode'] ?? 'html'), $_POST, $fetcher);
        $analysis = $detector->analyze($source, $config, $inputType, $sourceUrl);
        $uuid = $repository->saveInspection($source, $analysis);
        sendPage($config, 'Inspection Result', renderInspectionResult($analysis, $uuid));
    } catch (Throwable $exception) {
        $message = View::e($exception->getMessage());
        sendPage($config, 'Validation error', '<section class="panel form-panel"><h1>Validation error</h1><p>' . $message . '</p><p><a class="button-link" href="/inspector">Return to inspector</a></p></section>', 422);
    }
}

function resolveSource(string $mode, array $input, SafeSourceFetcher $fetcher): array
{
    if ($mode === 'url') {
        $url = trim((string) ($input['url'] ?? ''));
        $result = $fetcher->fetch($url);
        return [$result['source'], 'url', $result['url']];
    }

    $source = trim((string) ($input['html_source'] ?? $input['source'] ?? ''));
    if ($source === '' || strlen($source) > 1000000) {
        throw new RuntimeException('Paste HTML source under 1 MB or provide a URL.');
    }

    return [$source, 'html', null];
}

function sendPage(array $config, string $title, string $body, int $status = 200): void
{
    http_response_code($status);
    echo layout($config, $title, $body);
    exit;
}

function jsonResponse(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo Json::encode($payload);
    exit;
}

function layout(array $config, string $title, string $body): string
{
    $appTitle = View::e((string) $config['portal']['title']);
    $pageTitle = View::e($title);

    return <<<HTML
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{$pageTitle} | {$appTitle}</title>
  <link rel="stylesheet" href="/assets/app.css">
</head>
<body>
  <header class="topbar">
    <a class="brand" href="/"><span class="brand-mark">DBD</span><span>{$appTitle}</span></a>
    <nav>
      <a href="/">Dashboard</a>
      <a href="/inspector">Inspector</a>
      <a href="/paper">Paper</a>
      <a href="/api/summary">API</a>
    </nav>
  </header>
  <main class="page-shell">{$body}</main>
</body>
</html>
HTML;
}

function renderDashboard(array $config, LabRepository $repository): string
{
    $summary = $repository->summary();
    $paper = $config['paper'];
    $classifiers = $repository->classifierProfiles() ?: $config['classifiers'];
    $experiments = $repository->experiments();
    $recent = $repository->recentInspections();
    $classifierCards = renderClassifierCards($classifiers);
    $experimentCards = renderExperiments($experiments);
    $recentCards = renderRecentInspections($recent);
    $dbStatus = $summary['connected'] ? 'MySQL connected' : 'MySQL not connected';
    $inspectionCount = (string) ($summary['inspection_count'] ?? 0);
    $classifierCount = (string) ($summary['classifier_count'] ?: count($config['classifiers']));
    $featureCount = (string) ($summary['feature_count'] ?: count($config['feature_definitions']));
    $paperTitle = View::e((string) $paper['title']);
    $doiUrl = View::e((string) $paper['url']);
    $tagline = View::e((string) $config['portal']['tagline']);

    return <<<HTML
<section class="hero panel">
  <div>
    <p class="eyebrow">Drive-by download research portal</p>
    <h1>Static webpage inspection before browser execution.</h1>
    <p>{$tagline}</p>
    <div class="hero-actions">
      <a class="button-link" href="/inspector">Inspect source</a>
      <a class="secondary-link" href="{$doiUrl}" target="_blank" rel="noopener">Open paper DOI</a>
    </div>
  </div>
  <aside class="paper-card">
    <span>Research reference</span>
    <strong>{$paperTitle}</strong>
    <small>IJISP {$paper['volume']}({$paper['issue']}), {$paper['pages']}, DOI {$paper['doi']}</small>
  </aside>
</section>
<section class="metric-grid">
  <article><span>Paper dataset</span><strong>{$paper['dataset_size']}</strong><small>{$paper['benign_urls']} benign / {$paper['malicious_urls']} malicious webpages</small></article>
  <article><span>Features</span><strong>{$featureCount}</strong><small>{$paper['initial_features']} initial features, {$paper['features_selected']} selected</small></article>
  <article><span>Classifiers</span><strong>{$classifierCount}</strong><small>{$paper['classifiers_evaluated']} evaluated in the paper</small></article>
  <article><span>Inspections</span><strong>{$inspectionCount}</strong><small>{$dbStatus}</small></article>
</section>
<section class="section-head"><h2>Reported Top Classifiers</h2><a href="/paper">Citation details</a></section>
<section class="classifier-grid">{$classifierCards}</section>
<section class="split-layout">
  <div>
    <section class="section-head"><h2>Research Experiments</h2><a href="/inspector">Run inspection</a></section>
    <div class="stack">{$experimentCards}</div>
  </div>
  <aside class="panel">
    <h2>Recent Inspections</h2>
    {$recentCards}
  </aside>
</section>
HTML;
}

function renderInspector(LabRepository $repository): string
{
    $csrf = Csrf::field();
    $samples = renderSamples($repository->samples());
    $warning = $repository->connected() ? '' : '<div class="notice warning">MySQL is not connected. Inspection works, but results are not persisted.</div>';

    return <<<HTML
{$warning}
<section class="panel form-panel">
  <p class="eyebrow">Static source inspector</p>
  <h1>Inspect a URL or pasted HTML source</h1>
  <p class="muted">URL inspection retrieves source text with strict controls and does not execute JavaScript, plugins, frames, or browser-rendered content.</p>
  <form method="post" action="/inspector">
    {$csrf}
    <div class="mode-grid">
      <label><input type="radio" name="mode" value="html" checked> Pasted HTML</label>
      <label><input type="radio" name="mode" value="url"> URL fetch</label>
    </div>
    <label>URL <input name="url" type="url" placeholder="https://example.com/page"></label>
    <label>HTML source <textarea name="html_source" rows="8" maxlength="1000000" placeholder="Paste webpage source here for static analysis"></textarea></label>
    <button type="submit">Analyze source</button>
  </form>
</section>
<section class="section-head"><h2>Seed Samples</h2><span>Local demonstration</span></section>
<section class="sample-grid">{$samples}</section>
HTML;
}

function renderInspectionResult(array $analysis, ?string $uuid): string
{
    $label = View::e((string) $analysis['consensus_label']);
    $score = number_format((float) $analysis['risk_score'], 2);
    $source = View::e((string) $analysis['source_preview']);
    $uuidText = $uuid ? '<p class="muted">Saved inspection ID: ' . View::e($uuid) . '</p>' : '<p class="muted">Database not connected. Result was not persisted.</p>';
    $sourceUrl = $analysis['source_url'] ? '<p><strong>URL:</strong> ' . View::e((string) $analysis['source_url']) . '</p>' : '';
    $features = '';
    foreach ($analysis['features'] as $key => $value) {
        $features .= '<article><span>' . View::e((string) $key) . '</span><strong>' . View::e((string) $value) . '</strong></article>';
    }

    $predictions = '';
    foreach ($analysis['predictions'] as $prediction) {
        $predictions .= '<article class="panel prediction"><strong>' . View::e((string) $prediction['classifier']) . '</strong><span>' . View::e((string) $prediction['label']) . '</span><small>Score ' . View::e((string) $prediction['score']) . ' / threshold ' . View::e((string) $prediction['threshold']) . '</small></article>';
    }

    return <<<HTML
<section class="panel result-panel">
  <p class="eyebrow">Inspection result</p>
  <h1>Consensus: {$label}</h1>
  <p>Risk score: <strong>{$score}%</strong>. Votes: {$analysis['malicious_votes']} of {$analysis['total_votes']} classifier profiles flagged the source as malicious.</p>
  {$sourceUrl}
  {$uuidText}
  <pre><code>{$source}</code></pre>
</section>
<section class="section-head"><h2>Extracted Features</h2><a href="/inspector">Analyze another</a></section>
<section class="feature-grid">{$features}</section>
<section class="section-head"><h2>Classifier Profile Output</h2><span>Research-aligned comparison</span></section>
<section class="classifier-grid">{$predictions}</section>
HTML;
}

function renderPaper(array $config): string
{
    $paper = $config['paper'];
    $authors = View::e(implode(', ', $paper['authors']));
    $title = View::e((string) $paper['title']);
    $journal = View::e((string) $paper['journal']);
    $doi = View::e((string) $paper['doi']);
    $url = View::e((string) $paper['url']);
    $featureCards = '';
    foreach ($config['feature_definitions'] as $feature) {
        $featureCards .= '<article class="panel dimension-card"><span>' . View::e((string) $feature['key']) . '</span><h3>' . View::e((string) $feature['label']) . '</h3><p>' . View::e((string) $feature['description']) . '</p></article>';
    }

    return <<<HTML
<section class="panel paper-detail">
  <p class="eyebrow">Paper alignment</p>
  <h1>{$title}</h1>
  <p><strong>Authors:</strong> {$authors}</p>
  <p><strong>Journal:</strong> {$journal}, {$paper['volume']}({$paper['issue']}), {$paper['pages']}, {$paper['publisher']}, {$paper['year']}</p>
  <p><strong>DOI:</strong> <a href="{$url}" target="_blank" rel="noopener">{$doi}</a></p>
  <p>The portal follows the paper's research path: collect webpage sources, parse HTML safely as text, extract static features, compare classifier performance, and provide a practical pre-visit inspection interface.</p>
</section>
<section class="metric-grid">
  <article><span>Dataset</span><strong>{$paper['dataset_size']}</strong><small>Webpages analyzed</small></article>
  <article><span>Features</span><strong>{$paper['features_selected']}</strong><small>Selected static HTML signals</small></article>
  <article><span>Classifiers</span><strong>{$paper['classifiers_evaluated']}</strong><small>Compared in MATLAB</small></article>
  <article><span>Best accuracy</span><strong>{$paper['reported_best_accuracy']}%</strong><small>{$paper['best_classifier']}</small></article>
</section>
<section class="section-head"><h2>Feature Extraction Signals</h2><span>Paper-aligned basis</span></section>
<section class="dimension-grid">{$featureCards}</section>
HTML;
}

function renderClassifierCards(array $classifiers): string
{
    $html = '';
    foreach ($classifiers as $classifier) {
        $name = View::e((string) ($classifier['name'] ?? 'Classifier'));
        $accuracy = View::e((string) ($classifier['accuracy'] ?? ''));
        $tp = View::e((string) ($classifier['tp_rate'] ?? ''));
        $fp = View::e((string) ($classifier['fp_rate'] ?? ''));
        $html .= "<article class=\"panel classifier-card\"><span>Accuracy {$accuracy}%</span><h3>{$name}</h3><p>TP rate {$tp}% / FP rate {$fp}%</p></article>";
    }

    return $html;
}

function renderExperiments(array $experiments): string
{
    if ($experiments === []) {
        return '<article class="initiative"><strong>Connect MySQL to view seeded experiments.</strong><span>Docker Compose loads the schema and seed data automatically.</span></article>';
    }

    $html = '';
    foreach ($experiments as $experiment) {
        $title = View::e((string) $experiment['title']);
        $objective = View::e((string) $experiment['objective']);
        $status = View::e((string) $experiment['status']);
        $html .= "<article class=\"initiative\"><div><strong>{$title}</strong><span>{$objective}</span></div><span class=\"badge\">{$status}</span></article>";
    }

    return $html;
}

function renderRecentInspections(array $inspections): string
{
    if ($inspections === []) {
        return '<p class="muted">No inspection events are stored yet.</p>';
    }

    $html = '<div class="recent-list">';
    foreach ($inspections as $row) {
        $label = View::e((string) $row['consensus_label']);
        $score = number_format((float) $row['risk_score'], 2);
        $preview = View::e((string) $row['source_preview']);
        $html .= "<div><strong>{$label} / {$score}%</strong><span>{$preview}</span></div>";
    }

    return $html . '</div>';
}

function renderSamples(array $samples): string
{
    if ($samples === []) {
        return '<article class="panel"><p class="muted">Connect MySQL to view seeded HTML samples.</p></article>';
    }

    $html = '';
    foreach ($samples as $sample) {
        $label = View::e((string) $sample['label']);
        $title = View::e((string) $sample['title']);
        $source = View::e((string) $sample['source_html']);
        $html .= "<article class=\"panel sample-card\"><span>{$label}</span><h3>{$title}</h3><pre><code>{$source}</code></pre></article>";
    }

    return $html;
}
