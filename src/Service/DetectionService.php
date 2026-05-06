<?php

declare(strict_types=1);

namespace DrivebyLab\Service;

final class DetectionService
{
    public function __construct(private readonly HtmlFeatureExtractor $extractor)
    {
    }

    public function analyze(string $source, array $config, string $inputType = 'html', ?string $sourceUrl = null): array
    {
        $definitions = $config['feature_definitions'] ?? [];
        $classifiers = $config['classifiers'] ?? [];
        $features = $this->extractor->extract($source);
        $riskScore = $this->extractor->riskScore($features, $definitions);
        $predictions = [];

        foreach ($classifiers as $classifier) {
            $adjusted = round($riskScore * (float) ($classifier['sensitivity'] ?? 1), 2);
            $label = $adjusted >= (float) ($classifier['threshold'] ?? 35) ? 'malicious' : 'benign';
            $predictions[] = [
                'rank' => (int) ($classifier['rank'] ?? 0),
                'classifier' => (string) $classifier['name'],
                'label' => $label,
                'score' => $adjusted,
                'threshold' => (float) $classifier['threshold'],
                'reported_accuracy' => (float) $classifier['accuracy'],
                'reported_tp_rate' => (float) $classifier['tp_rate'],
                'reported_fp_rate' => (float) $classifier['fp_rate'],
            ];
        }

        $maliciousVotes = count(array_filter($predictions, fn (array $prediction): bool => $prediction['label'] === 'malicious'));
        $consensus = match (true) {
            $maliciousVotes >= 3 => 'malicious',
            $maliciousVotes <= 1 => 'benign',
            default => 'review',
        };

        return [
            'input_type' => $inputType,
            'source_url' => $sourceUrl,
            'source_preview' => substr(trim(strip_tags($source)), 0, 500) ?: substr(trim($source), 0, 500),
            'bytes_analyzed' => strlen($source),
            'risk_score' => $riskScore,
            'consensus_label' => $consensus,
            'malicious_votes' => $maliciousVotes,
            'total_votes' => count($predictions),
            'top_indicators' => $this->extractor->topIndicators($features),
            'features' => $features,
            'predictions' => $predictions,
        ];
    }
}
