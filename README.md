# Drive-by Download ML Lab

A PHP 8.x and MySQL 8.0 research portal based on the paper **"Detection of Drive-by Download Attacks Using Machine Learning Approach"** by Monther Aldwairi, Musaab Hasan, and Zayed Balbahaith.

The project turns the paper's static webpage analysis workflow into a practical platform for safe URL/source inspection, HTML feature extraction, classifier profile comparison, experiment tracking, and research extension.

## Paper Reference

Aldwairi, M., Hasan, M., & Balbahaith, Z. (2017). **Detection of Drive-by Download Attacks Using Machine Learning Approach**. *International Journal of Information Security and Privacy*, 11(4), 16-28. IGI Global. https://doi.org/10.4018/IJISP.2017100102

The paper evaluated 23 machine-learning classifiers on 5,435 webpages using static features extracted from HTML source code. Bagged Trees was reported as the strongest classifier, with 90.1% accuracy, 96.24% true positive rate, and 26.07% false positive rate in the article abstract.

## What This Repository Provides

- Safe URL/source inspection workflow that reads HTML as text and does not execute browser code.
- Static feature extraction aligned with the paper's 15-feature approach.
- Adversarial dataset shift review for temporal drift, campaign clustering, feature leakage, evasion pressure, and operational threshold decisions.
- Research dashboard with dataset metrics, classifier model cards, experiments, and recent inspections.
- Inspector interface for either a live URL or pasted HTML source.
- JSON API for integration with other security dashboards or research notebooks.
- MySQL schema for feature definitions, classifier profiles, inspected sources, experiments, and audit events.
- Security-conscious PHP implementation with CSRF validation, security headers, PDO prepared statements, URL validation, private-network blocking, timeout controls, and source size limits.
- Docker-based local development setup.
- Lint and functional tests for the extractor, detector, citation metadata, and safe-fetch policy.

## Research Alignment

The implementation follows the paper's core workflow:

1. Collect benign and malicious webpage sources.
2. Parse HTML source without executing page scripts.
3. Extract static webpage features.
4. Compare candidate classifier profiles.
5. Present a practical inspection interface before a user visits a suspicious URL.

This repository does not include the original MATLAB-trained classifier artifacts. It provides a professional PHP/MySQL research lab scaffold with transparent heuristic scoring and model-card references, ready for later integration with exported trained models or an external scoring service.

## Quick Start

```bash
cp .env.example .env
docker compose up --build
```

Then open:

- Application: `http://localhost:8081`
- Inspector: `http://localhost:8081/inspector`
- Paper alignment: `http://localhost:8081/paper`
- Health endpoint: `http://localhost:8081/health`
- JSON summary: `http://localhost:8081/api/summary`

## Local Checks

```bash
php bin/lint.php
php bin/test.php
```

## Repository Structure

```text
public/              Web entry point and assets
src/                 PHP services, repository, security, and support classes
config/              Paper metadata, features, and classifier profiles
database/            MySQL schema and seed data
docs/                Architecture, paper alignment, security, testing, and extension notes
bin/                 Lint and test scripts
```

## Documentation

- [Architecture](docs/architecture.md)
- [Paper Alignment](docs/paper-alignment.md)
- [Security](docs/security.md)
- [Testing](docs/testing.md)
- [Model Evaluation Metrics Worksheet](docs/model-evaluation-metrics-worksheet.md)
- [Adversarial Dataset Shift Review](docs/adversarial-dataset-shift-review.md)
- [Extension Guide](docs/extension-guide.md)

## Production Notes

- Add authentication and role-based access before operational use.
- Treat inspected URLs and HTML as security evidence.
- Store secrets outside source control.
- Enforce HTTPS and centralized logging.
- Keep outbound fetches behind egress controls and malware-safe infrastructure.
- Use the platform as research support and decision aid, not as the only web security control.

## License

MIT License. See [LICENSE](LICENSE).

<!-- portfolio:start -->
## Portfolio and Professional Profile

This repository is part of the professional portfolio of [Musaab Hasan](https://musaab.info), focused on cybersecurity, digital forensics, AI governance, EdTech, secure platforms, and research-driven digital transformation.

### Digital Forensics and Security Research Labs

- [Android Digital Forensics Lab](https://github.com/musaabhasan/android-forensics-lab) - Advanced Android forensics workbench for acquisition planning, anti-forensics evaluation, memory triage, evidence integrity, and case reconstruction.
- [Humanoid Robot Forensics Lab](https://github.com/musaabhasan/humanoid-robot-forensics-lab) - PHP/MySQL forensic casework platform for humanoid robot, companion app, and IoT evidence triage.
- [Smart Metering Security Lab](https://github.com/musaabhasan/smart-metering-security-lab) - Research portal based on smart metering security analysis for cyber-physical and smart-grid environments.
- [Drive-by Download ML Lab](https://github.com/musaabhasan/driveby-download-ml-lab) - Machine learning research portal for detecting drive-by download attacks and web-based malware delivery.
- [SQL Injection ML Detection Lab](https://github.com/musaabhasan/sqli-ml-detection-lab) - Research portal for SQL injection detection using machine learning and security telemetry.
- [IoT Board SSH Hardening Lab](https://github.com/musaabhasan/iot-board-ssh-hardening-lab) - SSH exposure assessment and hardening portal for IoT development boards and embedded Linux systems.
- [ZigBee WHAS Design Lab](https://github.com/musaabhasan/zigbee-whas-design-lab) - Research portal for designing and evaluating ZigBee wireless home automation systems.
- [Mammogram Fourier Analysis Lab](https://github.com/musaabhasan/mammogram-fourier-analysis-lab) - Medical image-processing research portal based on Fourier transform analysis for mammography.

### Security Culture and Transformation Platforms

- [Human Factors Risk Profiler](https://github.com/musaabhasan/human-factors-risk-profiler) - Human-centered security risk profiling portal for targeted interventions and behavior-aware controls.
- [Security Champion Network Portal](https://github.com/musaabhasan/security-champion-network-portal) - Platform for managing security champion networks, missions, recognition, and measurable impact.
- [Crisis Simulation Command Portal](https://github.com/musaabhasan/crisis-simulation-command-portal) - Cyber crisis simulation planning, scoring, and improvement platform for resilience exercises.
- [Behavioral Security Metrics Portal](https://github.com/musaabhasan/behavioral-security-metrics-portal) - Evidence-based security awareness metrics portal focused on behavior, culture, and intervention outcomes.
- [Security Culture Heatmap Portal](https://github.com/musaabhasan/security-culture-heatmap-portal) - Security culture maturity heatmap for norms, leadership signals, and organizational readiness.
- [Emerging Technology Security Culture Portal](https://github.com/musaabhasan/emerging-technology-security-culture-portal) - Adoption-readiness portal for emerging technology, governance, and security culture alignment.
- [AI Use Case Evaluation Portal](https://github.com/musaabhasan/ai-use-case-evaluation-portal) - Evaluation platform for AI use cases across value, feasibility, data readiness, privacy, ethics, and governance.
- [Transformation Roadmap Portal](https://github.com/musaabhasan/transformation-roadmap-portal) - Roadmap platform for moving security culture programs from compliance orientation to resilience and measurable change.

### Governance, Education, and Secure Enablement

- [Professional Development Registration System Framework](https://github.com/musaabhasan/pdrs-framework) - Secure registration and Moodle enrollment automation framework for professional development programs.
- [Multilingual Certificate Issuer](https://github.com/musaabhasan/multilingual-certificate-issuer) - Arabic/English certificate design, PDF generation, and throttled SMTP distribution platform.
- [AI Security Governance Toolkit](https://github.com/musaabhasan/ai-security-governance-toolkit) - Practical AI security governance controls, templates, evidence registers, playbooks, and policy-as-code examples.

Professional profile and research portfolio: [https://musaab.info](https://musaab.info)
<!-- portfolio:end -->
