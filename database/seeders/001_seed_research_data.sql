INSERT INTO feature_definitions (feature_key, label, description, weight) VALUES
('white_space_density', 'White Space Density', 'Measures unusual source spacing and padding concentration.', 0.450),
('tag_density', 'HTML Tag Density', 'Counts total HTML tags relative to source size.', 0.600),
('link_density', 'Link Density', 'Counts anchors and link-like references.', 0.550),
('variable_definitions', 'Variable Definitions', 'Detects JavaScript variable declarations in source text.', 0.850),
('loop_constructs', 'Loop Constructs', 'Detects JavaScript looping constructs often present in scripted payloads.', 0.800),
('iframe_count', 'IFrame Count', 'Counts iframe elements used in hidden loading and redirection chains.', 1.200),
('eval_calls', 'Eval Calls', 'Detects eval usage, a common indicator for interpreted script payloads.', 1.350),
('settimeout_calls', 'setTimeout Calls', 'Detects delayed script execution patterns.', 0.900),
('html_redirects', 'HTML Redirection', 'Detects meta refresh, location changes, and scripted navigation.', 1.150),
('opening_tags', 'Opening Tags', 'Counts opening tags as a structural complexity signal.', 0.450),
('function_calls', 'Function Calls', 'Counts JavaScript-like function invocation patterns.', 0.700),
('form_count', 'Forms', 'Counts form elements present in the source.', 0.550),
('input_fields', 'Input Fields', 'Counts input elements and form collection points.', 0.550),
('zero_pixel_objects', 'Zero Pixel Objects', 'Detects zero-dimension or hidden HTML objects and frames.', 1.350),
('onload_handlers', 'Onload Handlers', 'Detects automatic execution handlers triggered when the page loads.', 1.250)
ON DUPLICATE KEY UPDATE label = VALUES(label), description = VALUES(description), weight = VALUES(weight);

INSERT INTO classifier_profiles (rank_order, name, tp_rate, fp_rate, accuracy, threshold_score, sensitivity, source_note) VALUES
(1, 'Bagged Trees', 96.00, 33.00, 90.10, 34.00, 1.080, 'Highest reported accuracy in Aldwairi, Hasan, and Balbahaith, IJISP 2017.'),
(2, 'Weighted KNN, k=10', 97.00, 36.00, 89.80, 35.00, 1.040, 'Top-five classifier reported in Aldwairi, Hasan, and Balbahaith, IJISP 2017.'),
(3, 'Boosted Trees', 97.00, 45.00, 87.70, 36.00, 1.020, 'Top-five classifier reported in Aldwairi, Hasan, and Balbahaith, IJISP 2017.'),
(4, 'Medium Tree', 96.00, 44.00, 87.30, 37.00, 0.980, 'Top-five classifier reported in Aldwairi, Hasan, and Balbahaith, IJISP 2017.'),
(5, 'Cubic KNN, k=10', 96.00, 44.00, 87.20, 37.00, 0.960, 'Top-five classifier reported in Aldwairi, Hasan, and Balbahaith, IJISP 2017.')
ON DUPLICATE KEY UPDATE rank_order = VALUES(rank_order), tp_rate = VALUES(tp_rate), fp_rate = VALUES(fp_rate), accuracy = VALUES(accuracy), threshold_score = VALUES(threshold_score), sensitivity = VALUES(sensitivity), source_note = VALUES(source_note);

INSERT INTO sample_sources (label, title, source_html, source_note) VALUES
('benign', 'Benign documentation page', '<!doctype html><html><head><title>Guide</title></head><body><main><h1>Security Guide</h1><p>Simple documentation page.</p><a href="/policy">Policy</a></main></body></html>', 'Safe static page example.'),
('benign', 'Benign registration page', '<html><body><form method="post" action="/register"><input name="name"><input name="email"><button>Register</button></form></body></html>', 'Simple form example.'),
('malicious', 'Hidden iframe redirect', '<html onload="launch()"><body><iframe src="http://example.invalid/kit" width="0" height="0" style="display:none"></iframe><script>function launch(){setTimeout(function(){location.href="http://example.invalid/next"},100);}</script></body></html>', 'Synthetic source demonstrating hidden iframe and onload patterns.'),
('malicious', 'Obfuscated eval payload', '<html><body><script>var a="location"; var b="href"; for(var i=0;i<1;i++){ eval("window["+a+"]["+b+"]=" + "http://example.invalid"); }</script></body></html>', 'Synthetic source demonstrating eval, variables, and loop patterns.')
ON DUPLICATE KEY UPDATE source_html = VALUES(source_html), source_note = VALUES(source_note);

INSERT INTO experiments (title, objective, dataset_size, feature_count, classifier_count, status) VALUES
('IJISP 2017 research baseline', 'Represent the paper workflow: 5,435 webpages, 15 selected HTML features, 23 evaluated classifiers, and five top model cards.', 5435, 15, 23, 'completed'),
('Expanded modern exploit-kit source corpus', 'Collect updated benign and malicious page sources for feature drift analysis and browser-exploit pattern comparison.', 7500, 15, 5, 'planned'),
('External trained model integration', 'Connect the PHP portal to exported model artifacts or a dedicated model scoring service.', 5435, 15, 5, 'planned')
ON DUPLICATE KEY UPDATE objective = VALUES(objective), dataset_size = VALUES(dataset_size), feature_count = VALUES(feature_count), classifier_count = VALUES(classifier_count), status = VALUES(status);
