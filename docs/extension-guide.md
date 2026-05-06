# Extension Guide

## Add More Features

Add a feature definition in `config/paper.php`, seed it in `database/seeders/001_seed_research_data.sql`, and extend `HtmlFeatureExtractor`.

## Connect a Trained Model

The current detector is transparent and heuristic. To connect a trained model:

1. Keep `HtmlFeatureExtractor` as the feature source.
2. Export feature vectors to the model service.
3. Replace or supplement `DetectionService` predictions with the external score.
4. Store the external model version in `prediction_json`.

## Expand Dataset Support

Add tables for dataset imports when larger research datasets are available. Recommended fields include source hash, URL, label, source provenance, feature JSON, review status, and reviewer notes.

## Improve Evaluation

Add experiment runs with confusion matrix fields:

- True positive
- True negative
- False positive
- False negative
- Precision
- Recall
- F1 score
- Accuracy
- AUC

## Add Access Control

Before real operational use, add authentication, roles, and approval workflows for reviewing saved inspections.
