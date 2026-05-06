# Paper Alignment

This repository is aligned with the research paper:

Aldwairi, M., Hasan, M., & Balbahaith, Z. (2017). **Detection of Drive-by Download Attacks Using Machine Learning Approach**. *International Journal of Information Security and Privacy*, 11(4), 16-28. IGI Global. https://doi.org/10.4018/IJISP.2017100102

## Research Contribution Reflected in the Portal

The paper proposed a machine-learning approach for detecting drive-by download infected webpages through static HTML source analysis. Its workflow included:

- Collection of benign and malicious webpage URLs.
- Safe retrieval of HTML source code without browser execution.
- Extraction of 15 static features from webpage source.
- Evaluation of 23 machine-learning classifiers.
- Selection of the top five classifiers.
- A graphical inspection interface for checking a URL before visiting it.

## Portal Translation

This repository translates those ideas into a PHP/MySQL research lab:

- `SafeSourceFetcher` retrieves static source with strict URL controls.
- `HtmlFeatureExtractor` calculates the 15 paper-aligned feature signals.
- `DetectionService` compares the weighted risk score against classifier profiles inspired by the paper's reported top models.
- `classifier_profiles` stores top model cards and reported metrics.
- `inspections` stores feature and prediction evidence for reviewed sources.
- `experiments` tracks dataset and model improvement work.

## Reported Classifier Results Represented

| Classifier | TP Rate | FP Rate | Accuracy |
| --- | ---: | ---: | ---: |
| Bagged Trees | 96% | 33% | 90.1% |
| Weighted KNN, k=10 | 97% | 36% | 89.8% |
| Boosted Trees | 97% | 45% | 87.7% |
| Medium Tree | 96% | 44% | 87.3% |
| Cubic KNN, k=10 | 96% | 44% | 87.2% |

## Scope Note

The portal is a research and education scaffold. It does not include the original MATLAB-trained classifier artifacts. It is designed so trained models, exported feature tables, or an external scoring service can be integrated later without changing the inspection workflow.

## Extension Direction

The most valuable next step is to collect a modern webpage corpus and compare the 2017 static features against current browser exploit, malvertising, and redirect-chain behavior.
