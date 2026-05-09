# Drive-by Download Model Evaluation Metrics Worksheet

Use this worksheet when evaluating or comparing drive-by download detection models, heuristic profiles, static HTML feature extractors, or external scoring services. The goal is to connect reported model quality to security operations: safe inspection, false-positive workload, missed malicious pages, and triage decisions.

## Evaluation Header

| Field | Value |
| --- | --- |
| Evaluation name |  |
| Dataset version |  |
| Source collection window |  |
| Feature extractor version |  |
| Model or detector |  |
| Threshold or score policy |  |
| Evaluator |  |
| Date |  |

## Dataset And Sampling

| Check | Evidence | Status |
| --- | --- | --- |
| Benign and malicious webpage counts are recorded | Dataset summary |  |
| Collection source is documented without encouraging unsafe browsing | Source note |  |
| Live URL fetching is separated from inert HTML-source analysis | Collection procedure |  |
| Duplicate pages, mirrors, and repeated campaign samples are handled | Deduplication note |  |
| Label source and reviewer process are documented | Labeling protocol |  |
| Time-based drift is considered for web threat changes | Collection window |  |
| Data-sharing restrictions are recorded | License or access note |  |

## Metrics Table

| Metric | Value | Why it matters |
| --- | --- | --- |
| Accuracy |  | Overall correctness; insufficient alone for security decisions |
| True positive rate |  | Ability to detect malicious or exploit-delivery pages |
| False positive rate |  | Expected noise against benign pages and analyst workload |
| Precision |  | Share of alerts that are truly malicious |
| Recall |  | Share of malicious pages detected |
| F1 score |  | Balance between precision and recall |
| ROC-AUC or PR-AUC |  | Threshold-independent comparison, especially under class imbalance |
| False negatives by feature pattern |  | Bypass or evasion patterns |
| False positives by site type |  | Operational friction for normal web content |

## Operational Threshold Review

| Decision | Notes |
| --- | --- |
| Alert threshold |  |
| Review threshold |  |
| Block threshold, if any |  |
| Human analyst review path |  |
| Expected alert volume |  |
| False-positive tolerance |  |
| High-risk false-negative scenarios |  |

Review questions:

- Does the selected threshold reflect analyst capacity, not only benchmark score?
- Are false positives concentrated in legitimate pages that use scripts, iframes, redirects, or obfuscation for benign reasons?
- Are false negatives concentrated in compressed, minimal, delayed, or externally loaded exploit content?
- Is the detector used as triage support rather than a sole blocking decision?
- Are examples stored as inert text rather than executed in a browser?

## Feature Validation

| Feature group | Validation question | Status |
| --- | --- | --- |
| Script indicators | Are inline and external scripts counted consistently? |  |
| Redirect indicators | Are meta refresh, script redirects, and hidden navigation captured? |  |
| Iframe indicators | Are hidden or suspicious iframes measured without rendering the page? |  |
| Obfuscation indicators | Are encoded, packed, or suspicious tokens handled consistently? |  |
| Link and form indicators | Are suspicious endpoints counted without contacting them? |  |
| Size and density indicators | Are page length and script density normalized? |  |

## Error Review

| Error type | Required evidence |
| --- | --- |
| False positive | Page type, triggered features, safe source excerpt, analyst decision, mitigation note |
| False negative | Missed malicious behavior, missing feature coverage, campaign or exploit pattern, improvement note |
| Uncertain label | Reviewer disagreement, source reliability, and decision to exclude or retain |
| Fetch policy denial | Private network, unsupported scheme, timeout, oversized source, or unsafe redirect |

## Reporting Commitments

Before publishing or presenting the evaluation:

- Report false-positive and false-negative rates alongside accuracy.
- Explain whether results come from the original paper, seeded examples, heuristic scoring, or new trained artifacts.
- Document threshold selection and analyst review assumptions.
- Avoid claiming production blocking readiness without operational validation.
- State that safe static source inspection does not replace browser isolation, patching, endpoint controls, or user-awareness measures.
- Preserve enough sanitized examples to support future comparison without storing active exploit material.
