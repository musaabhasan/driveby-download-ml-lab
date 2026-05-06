<?php

declare(strict_types=1);

namespace DrivebyLab\Repository;

use DrivebyLab\Support\Json;
use DrivebyLab\Support\Uuid;
use PDO;
use Throwable;

final class LabRepository
{
    public function __construct(private readonly ?PDO $pdo)
    {
    }

    public function connected(): bool
    {
        return $this->pdo instanceof PDO;
    }

    public function summary(): array
    {
        if (!$this->connected()) {
            return [
                'connected' => false,
                'inspection_count' => 0,
                'feature_count' => 0,
                'classifier_count' => 0,
                'experiment_count' => 0,
            ];
        }

        return [
            'connected' => true,
            'inspection_count' => $this->count('inspections'),
            'feature_count' => $this->count('feature_definitions'),
            'classifier_count' => $this->count('classifier_profiles'),
            'experiment_count' => $this->count('experiments'),
        ];
    }

    public function classifierProfiles(): array
    {
        if (!$this->connected()) {
            return [];
        }

        return $this->pdo->query('SELECT rank_order AS rank, name, tp_rate, fp_rate, accuracy, threshold_score AS threshold, sensitivity, source_note FROM classifier_profiles ORDER BY rank_order')->fetchAll();
    }

    public function samples(): array
    {
        if (!$this->connected()) {
            return [];
        }

        return $this->pdo->query('SELECT label, title, source_html, source_note FROM sample_sources ORDER BY id LIMIT 8')->fetchAll();
    }

    public function experiments(): array
    {
        if (!$this->connected()) {
            return [];
        }

        return $this->pdo->query('SELECT title, objective, dataset_size, feature_count, classifier_count, status FROM experiments ORDER BY id')->fetchAll();
    }

    public function recentInspections(): array
    {
        if (!$this->connected()) {
            return [];
        }

        return $this->pdo->query('SELECT uuid, input_type, source_url, source_preview, risk_score, consensus_label, created_at FROM inspections ORDER BY created_at DESC LIMIT 6')->fetchAll();
    }

    public function saveInspection(string $source, array $analysis): ?string
    {
        if (!$this->connected()) {
            return null;
        }

        $uuid = Uuid::v4();

        try {
            $statement = $this->pdo->prepare(
                'INSERT INTO inspections (uuid, input_type, source_url, source_hash, source_preview, bytes_analyzed, risk_score, consensus_label, feature_json, prediction_json)
                 VALUES (:uuid, :input_type, :source_url, :source_hash, :source_preview, :bytes_analyzed, :risk_score, :consensus_label, :feature_json, :prediction_json)'
            );
            $statement->execute([
                'uuid' => $uuid,
                'input_type' => $analysis['input_type'],
                'source_url' => $analysis['source_url'],
                'source_hash' => hash('sha256', $source),
                'source_preview' => substr((string) $analysis['source_preview'], 0, 500),
                'bytes_analyzed' => (int) $analysis['bytes_analyzed'],
                'risk_score' => (float) $analysis['risk_score'],
                'consensus_label' => $analysis['consensus_label'],
                'feature_json' => Json::encode($analysis['features']),
                'prediction_json' => Json::encode($analysis['predictions']),
            ]);

            $this->audit('inspection.saved', ['uuid' => $uuid, 'label' => $analysis['consensus_label']]);
            return $uuid;
        } catch (Throwable) {
            return null;
        }
    }

    private function audit(string $action, array $payload): void
    {
        if (!$this->connected()) {
            return;
        }

        $statement = $this->pdo->prepare('INSERT INTO audit_events (action, actor, payload_json) VALUES (:action, :actor, :payload)');
        $statement->execute([
            'action' => $action,
            'actor' => 'system',
            'payload' => Json::encode($payload),
        ]);
    }

    private function count(string $table): int
    {
        $allowed = ['inspections', 'feature_definitions', 'classifier_profiles', 'experiments'];
        if (!in_array($table, $allowed, true)) {
            return 0;
        }

        return (int) $this->pdo->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
    }
}
