CREATE TABLE IF NOT EXISTS feature_definitions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  feature_key VARCHAR(80) NOT NULL UNIQUE,
  label VARCHAR(160) NOT NULL,
  description TEXT NULL,
  weight DECIMAL(8,3) NOT NULL DEFAULT 1.000,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS classifier_profiles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  rank_order TINYINT UNSIGNED NOT NULL,
  name VARCHAR(180) NOT NULL UNIQUE,
  tp_rate DECIMAL(6,2) NOT NULL,
  fp_rate DECIMAL(6,2) NOT NULL,
  accuracy DECIMAL(6,2) NOT NULL,
  threshold_score DECIMAL(8,2) NOT NULL,
  sensitivity DECIMAL(8,3) NOT NULL DEFAULT 1.000,
  source_note VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_classifier_rank (rank_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sample_sources (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  label ENUM('malicious','benign') NOT NULL,
  title VARCHAR(180) NOT NULL,
  source_html MEDIUMTEXT NOT NULL,
  source_note VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_sample_label (label)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS inspections (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uuid CHAR(36) NOT NULL UNIQUE,
  input_type ENUM('url','html') NOT NULL,
  source_url VARCHAR(2048) NULL,
  source_hash CHAR(64) NOT NULL,
  source_preview VARCHAR(500) NOT NULL,
  bytes_analyzed INT UNSIGNED NOT NULL,
  risk_score DECIMAL(8,2) NOT NULL,
  consensus_label ENUM('malicious','benign','review') NOT NULL,
  feature_json JSON NOT NULL,
  prediction_json JSON NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_inspection_label_created (consensus_label, created_at),
  INDEX idx_inspection_hash (source_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS experiments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(220) NOT NULL UNIQUE,
  objective TEXT NOT NULL,
  dataset_size INT UNSIGNED NOT NULL,
  feature_count INT UNSIGNED NOT NULL,
  classifier_count INT UNSIGNED NOT NULL,
  status ENUM('planned','active','completed') NOT NULL DEFAULT 'planned',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_events (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  action VARCHAR(120) NOT NULL,
  actor VARCHAR(160) NULL,
  payload_json JSON NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_audit_action_created (action, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
