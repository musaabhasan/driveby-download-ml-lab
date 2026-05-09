# Browser Version Skew Evaluation Protocol

Drive-by download detection changes when browser versions, operating systems, plugins, sandbox modes, and exploit delivery infrastructure change. A model that looks strong on one collection window can fail when attackers target newer APIs, legacy plugins, mobile browsers, or different parser behavior. Use this protocol to evaluate whether static HTML features and classifier thresholds remain reliable across browser and platform skew.

## Objectives

- Test whether detection performance changes across browser families and versions.
- Separate page-source features from runtime-only exploit behavior.
- Identify false positives caused by modern framework scripts, minification, obfuscation, or benign third-party tags.
- Identify false negatives caused by campaign adaptation, delayed payloads, browser fingerprinting, or version-specific exploit paths.
- Record limitations when the lab uses safe static source inspection rather than live exploit execution.

## Evaluation Matrix

| Dimension | Example Values | Evaluation Purpose |
| --- | --- | --- |
| Browser family | Chromium, Firefox, WebKit, legacy Internet Explorer, Android WebView | Detect family-specific delivery or parser assumptions |
| Browser version age | Current, n-1, n-6, long-term support, unsupported legacy | Measure drift between modern and vulnerable populations |
| Operating system | Windows, macOS, Linux, Android, iOS | Capture platform-specific redirection and payload targeting |
| Plugin or extension state | No plugin, PDF viewer, Java legacy, Flash legacy, browser extension | Identify exploit surface assumptions and false positives |
| Sandbox and isolation | Default sandbox, enterprise hardening, no sandbox, virtualized browser | Separate exploitability from static page suspicion |
| Network position | Direct, proxy, sinkhole, malware lab, safe crawler | Record infrastructure effects on retrieved source |
| Time window | Historical sample, recent sample, post-campaign sample | Detect temporal and campaign drift |

## Safe Collection Rules

- Collect HTML source as text and do not execute page scripts during routine lab inspection.
- Use isolated malware-safe infrastructure when fetching suspicious sources.
- Block private network targets and local metadata endpoints.
- Record user-agent, fetch timestamp, redirect chain, final URL, response status, MIME type, and source hash.
- Store retrieved source as evidence and avoid re-fetching from uncontrolled infrastructure for reproducibility.
- Do not visit suspicious URLs from a normal workstation or authenticated browser profile.

## Dataset Design

| Dataset Slice | Required Metadata | Purpose |
| --- | --- | --- |
| Benign modern web | Browser family, collection date, framework/library indicators, source hash | Controls false positives from modern JavaScript-heavy sites |
| Benign legacy web | Browser target, archived source, plugin references | Tests whether old benign pages resemble exploit-era features |
| Malicious historical | Campaign family, collection date, target browser, label source | Reproduces known drive-by patterns |
| Malicious recent | Campaign or detection source, redirect chain, target indicators | Tests current evasion and obfuscation |
| Fingerprinting pages | User-agent checks, navigator probes, plugin checks | Measures browser-targeting behavior visible in source |
| Delayed payload pages | Timers, event handlers, external script loaders | Measures static evidence for staged delivery |

Minimum metadata per sample:

- sample ID,
- label and label source,
- collection date,
- source URL or redacted evidence reference,
- final URL,
- response status and MIME type,
- user-agent used for collection,
- source hash,
- browser or target indicators visible in source,
- campaign or family tag when known,
- reviewer and confidence.

## Feature Drift Review

| Feature Theme | Drift Question | Evidence |
| --- | --- | --- |
| Obfuscation | Are benign frameworks producing obfuscation-like patterns? | Minified script ratio, entropy, packed code indicators |
| Redirection | Are modern consent, analytics, CDN, or SSO flows inflating redirect features? | Redirect chain and benign baseline comparison |
| External scripts | Are third-party tags changing script count and domain diversity? | Script host inventory |
| Suspicious functions | Are features tied to legacy exploit APIs still relevant? | Browser version and API references |
| Iframes and objects | Are benign embeds increasing iframe/object features? | Tag inventory and known benign services |
| Delayed execution | Are timers and event handlers common in benign pages? | Handler count by dataset slice |
| Browser fingerprinting | Are feature probes used by benign personalization or malicious targeting? | Fingerprinting pattern review |

## Evaluation Procedure

1. Define the target browser and platform population for the evaluation.
2. Build a balanced matrix across browser family, version age, OS, and time window.
3. Collect source safely with fixed user-agent strings and evidence hashes.
4. Extract static features with the same extractor version for all slices.
5. Train or score using temporal separation: older samples for training, newer samples for holdout.
6. Report metrics by browser/version slice, not only as a single aggregate.
7. Review false positives from modern benign pages and false negatives from targeted exploit pages.
8. Compare threshold behavior across slices.
9. Document whether feature changes require retraining, feature redesign, or separate thresholds.
10. Preserve dataset manifests and source hashes for peer review.

## Metrics to Report

| Metric | Why It Matters |
| --- | --- |
| True positive rate by browser/version slice | Shows whether targeted exploit families are detected |
| False positive rate by benign modern slice | Measures operational burden for current web traffic |
| False negative rate for fingerprinting or delayed payload pages | Tests evasion pressure against static features |
| Precision under realistic prevalence | Prevents accuracy from hiding operational alert volume |
| Threshold stability | Shows whether one threshold works across browser populations |
| Feature importance drift | Identifies stale or brittle static features |
| Label confidence distribution | Separates strong ground truth from weak or inherited labels |

## Finding Categories

| Finding | Meaning | Required Action |
| --- | --- | --- |
| Browser-family sensitivity | Performance differs materially across browser families | Consider family-aware thresholds or additional features |
| Legacy overfit | Model detects historical exploit pages but misses recent delivery | Refresh malicious corpus and evaluate new features |
| Modern benign false positives | Modern web frameworks inflate suspicious features | Add benign modern slice and adjust feature interpretation |
| Fingerprinting blind spot | Pages target browser versions without strong static indicators | Add detection notes and consider dynamic sandbox evidence |
| Temporal leakage | Train/test split includes same campaign or near-duplicate source | Re-split by campaign and collection time |
| Label uncertainty | Dataset labels depend on weak or stale reputation signals | Add reviewer confidence and exclude low-confidence rows from headline claims |

## Release Decision

| Decision | Criteria |
| --- | --- |
| Ready | Metrics are stable across target slices and limitations are documented |
| Ready with conditions | Known weak slice has compensating control or operational note |
| Retrain required | Current classifier fails target browser/version population |
| Feature redesign required | Static features no longer separate benign and malicious slices |
| Dynamic analysis required | Static source is insufficient for version-targeted or delayed payload behavior |

## Evidence Package

Retain:

- dataset manifest,
- source hashes,
- fetch configuration and user-agent strings,
- extractor version,
- browser/version matrix,
- metrics by slice,
- threshold analysis,
- false positive and false negative review,
- label confidence notes,
- release decision and reviewer.
