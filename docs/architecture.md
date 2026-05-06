# Architecture

Drive-by Download ML Lab uses a compact PHP service-and-repository architecture.

## Layers

- `public/index.php`: routing, rendering, CSRF validation, JSON endpoints, and inspector submission handling.
- `src/Service/SafeSourceFetcher.php`: URL validation, private-network blocking, timeout control, and static source retrieval.
- `src/Service/HtmlFeatureExtractor.php`: 15-feature static webpage source extraction.
- `src/Service/DetectionService.php`: classifier-profile comparison and consensus labeling.
- `src/Repository/LabRepository.php`: database persistence through PDO prepared statements.
- `config/paper.php`: paper metadata, feature definitions, and top classifier profiles.
- `database/migrations` and `database/seeders`: repeatable MySQL setup.

## Main Routes

- `/`: dashboard with paper metrics, model cards, experiments, and recent inspections.
- `/inspector`: URL or pasted HTML source inspection interface.
- `/paper`: paper citation, workflow alignment, and feature definitions.
- `/health`: liveness endpoint.
- `/api/summary`: JSON summary for reporting integrations.
- `/api/inspect`: POST endpoint for detector integrations.

## Detection Flow

1. A user provides a URL or pasted HTML source.
2. URL mode retrieves source as text without browser rendering or script execution.
3. The feature extractor calculates paper-aligned static features.
4. The detector calculates an aggregate risk score.
5. The score is compared against the top classifier profiles represented from the paper.
6. A consensus label is produced.
7. If MySQL is connected, features, predictions, and a source hash are stored as an inspection event.

## Design Principle

The project keeps detector logic transparent and explainable so the research concept can be reviewed, extended, and replaced with trained model artifacts later.
