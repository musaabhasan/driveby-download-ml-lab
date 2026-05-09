# Adversarial Dataset Shift Review

Use this worksheet before comparing, retraining, or publishing drive-by download detection results. Web malware changes quickly: exploit kits rotate infrastructure, benign sites adopt more JavaScript, campaigns reuse templates, and attackers adapt to visible features. A model can look strong on a static benchmark and still fail when the collection window, label source, or adversarial pressure changes.

## Review Header

| Field | Value |
| --- | --- |
| Evaluation name |  |
| Dataset version |  |
| Collection window |  |
| Benign source families |  |
| Malicious source families |  |
| Feature extractor version |  |
| Model or detector |  |
| Reviewer |  |
| Review date |  |

## Shift Risk Matrix

| Shift Type | Drive-by Download Example | Evaluation Risk | Required Control |
| --- | --- | --- | --- |
| Temporal drift | New exploit kit markup, changed redirect chain, expired campaign pages | Model learns old campaign traits | Time-based train/test split and dated reporting |
| Campaign clustering | Many samples from one malware campaign or mirror | Performance is inflated by near-duplicates | URL, DOM, script, and feature-level deduplication |
| Benign web evolution | Modern benign sites use heavy scripts, iframes, tracking, and obfuscation | False positives rise in production | Benign holdout from current normal web pages |
| Evasion pressure | Attackers reduce static indicators or load payload later | False negatives concentrate in sparse pages | Error review by missed feature pattern |
| Collection bias | Malicious samples from one feed; benign samples from another source type | Model learns source artifacts | Source-balanced sampling and source-aware reporting |
| Feature leakage | Labels, source names, folder paths, or feed metadata leak into features | Unrealistic benchmark scores | Feature audit and leakage probe |
| Fetch policy effects | Timeouts, blocked private IPs, redirects, or oversized pages excluded | Dataset underrepresents risky pages | Report denial rates and exclusion reasons |
| Label staleness | URLs change from malicious to benign or disappear | Noisy or outdated ground truth | Label timestamp, reviewer evidence, and uncertain-label handling |

## Temporal Split Checklist

| Check | Evidence | Status |
| --- | --- | --- |
| Training, validation, and test windows are separated by collection date | Split summary |  |
| Malicious campaigns are not split across train and test by near-duplicate pages | Cluster report |  |
| Benign pages in the test set reflect current normal site behavior | Benign source note |  |
| Results are reported with collection dates, not only sample counts | Report draft |  |
| Old and new feature distributions are compared | Drift summary |  |
| Thresholds are reviewed after drift analysis | Threshold decision |  |

## Feature Leakage Review

| Leakage Probe | Question | Result |
| --- | --- | --- |
| Source metadata | Are feed names, labels, filenames, or directory names excluded from features? |  |
| URL artifacts | Does the detector overfit domains, paths, timestamps, or campaign IDs? |  |
| Duplicate content | Are mirrored pages, repeated templates, and identical scripts deduplicated? |  |
| Fetch outcome | Are timeout, denial, or error codes correlated with labels? |  |
| Sanitization artifacts | Do cleaned examples contain markers that reveal the label? |  |
| Class balance | Are accuracy and AUC masking poor precision or false-positive burden? |  |

## Adversarial Error Review

| Error Pattern | Review Question | Improvement Path |
| --- | --- | --- |
| Sparse malicious page | Did the malicious page avoid static indicators by loading behavior remotely? | Add dynamic-analysis handoff or network-context flag |
| Benign script-heavy site | Did normal analytics, ads, or frameworks trigger malicious features? | Add benign subcategory review and threshold adjustment |
| Hidden iframe false positive | Is iframe usage benign, embedded, or tracking-related? | Add source context and false-positive examples |
| Encoded content false positive | Is encoding used for normal performance or internationalization? | Separate suspicious encoding from normal minification |
| Redirect-chain false negative | Was the redirect hidden outside static HTML source? | Preserve redirect metadata and safe fetch logs |
| Obfuscated exploit false negative | Did the feature extractor miss packing, eval, or encoded script patterns? | Extend feature extraction and regression tests |

## Operational Readiness Gate

| Gate | Minimum Evidence |
| --- | --- |
| Dataset lineage | Source, collection date, label basis, reviewer, and exclusion reason |
| Drift measurement | Feature distribution comparison between old and current windows |
| Evasion review | False negatives grouped by missing feature or campaign behavior |
| False-positive review | Benign false positives grouped by site type and feature trigger |
| Threshold rationale | Alert, review, and block thresholds tied to analyst capacity |
| Safe storage | HTML stored as inert text; active exploit material is excluded or isolated |
| Reproducibility | Split seed, feature extractor version, model version, and metric script |
| Limitation statement | Clear boundary between research scoring and production blocking |

## Reporting Language

| Situation | Suggested Language |
| --- | --- |
| Dataset is time-bound | "The result reflects the collection window and should be revalidated against newer pages before operational use." |
| Campaign clustering remains | "The score may overstate generalization because related campaign samples are present." |
| Static features miss behavior | "Static source analysis is used as a triage signal and does not replace browser isolation or dynamic analysis." |
| False positives are concentrated | "Operational deployment requires threshold tuning for benign pages with similar feature patterns." |
| Labels are uncertain | "Uncertain or stale labels were excluded or reviewed separately to reduce benchmark noise." |

## Closure Checklist

- Collection window, source families, and label evidence are documented.
- Time-based split is used or the limitation is stated.
- Near-duplicate and campaign-cluster handling is documented.
- Feature leakage probes are complete.
- False positives and false negatives are grouped by operational pattern.
- Drift or evasion findings are connected to threshold decisions.
- Safe storage rules prevent accidental execution of suspicious content.
- Report language avoids overstating production readiness.
