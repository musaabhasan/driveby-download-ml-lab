<?php

declare(strict_types=1);

namespace DrivebyLab\Service;

final class HtmlFeatureExtractor
{
    public function extract(string $source): array
    {
        $html = trim($source);
        $lower = strtolower($html);
        $length = max(1, strlen($html));
        $tagCount = $this->count('/<\s*\/?\s*[a-z][^>]*>/i', $html);
        $openingTags = $this->count('/<\s*[a-z][^>]*>/i', $html);
        $links = $this->count('/\b(?:href|src)\s*=/i', $html);
        $variables = $this->count('/\b(?:var|let|const)\s+[a-z_$][\w$]*/i', $html);
        $loops = $this->count('/\b(?:for|while)\s*\(|\bdo\s*\{/i', $html);
        $iframes = $this->count('/<\s*iframe\b/i', $html);
        $evals = $this->count('/\beval\s*\(/i', $html);
        $setTimeouts = $this->count('/\bsettimeout\s*\(/i', $lower);
        $redirects = $this->count('/http-equiv\s*=\s*["\']?refresh|window\.location|location\.href|location\.replace\s*\(/i', $html);
        $functionCalls = $this->count('/\b[a-zA-Z_$][\w$]*\s*\(/', $html);
        $forms = $this->count('/<\s*form\b/i', $html);
        $inputs = $this->count('/<\s*input\b/i', $html);
        $zeroPixel = $this->zeroPixelSignals($html);
        $onload = $this->count('/\bonload\s*=|addEventListener\s*\(\s*["\']load["\']/i', $html);
        $whiteSpaces = $this->count('/\s/', $html);

        return [
            'white_space_density' => min(10.0, round(($whiteSpaces / $length) * 28, 2)),
            'tag_density' => min(10.0, round($tagCount / max(1, $length / 260), 2)),
            'link_density' => min(10.0, round($links * 1.25, 2)),
            'variable_definitions' => min(10.0, round($variables * 2.5, 2)),
            'loop_constructs' => min(10.0, round($loops * 3.5, 2)),
            'iframe_count' => min(10.0, round($iframes * 4.0, 2)),
            'eval_calls' => min(10.0, round($evals * 7.0, 2)),
            'settimeout_calls' => min(10.0, round($setTimeouts * 4.5, 2)),
            'html_redirects' => min(10.0, round($redirects * 5.0, 2)),
            'opening_tags' => min(10.0, round($openingTags / max(1, $length / 320), 2)),
            'function_calls' => min(10.0, round($functionCalls / max(1, $length / 300), 2)),
            'form_count' => min(10.0, round($forms * 2.0, 2)),
            'input_fields' => min(10.0, round($inputs * 1.3, 2)),
            'zero_pixel_objects' => min(10.0, round($zeroPixel * 4.5, 2)),
            'onload_handlers' => min(10.0, round($onload * 5.0, 2)),
        ];
    }

    public function riskScore(array $features, array $definitions): float
    {
        $weighted = 0.0;
        $totalWeight = 0.0;

        foreach ($definitions as $definition) {
            $key = (string) ($definition['key'] ?? '');
            $weight = max(0.1, (float) ($definition['weight'] ?? 1));
            $value = max(0.0, min(10.0, (float) ($features[$key] ?? 0)));
            $weighted += ($value / 10.0) * 100.0 * $weight;
            $totalWeight += $weight;
        }

        return $totalWeight > 0 ? round($weighted / $totalWeight, 2) : 0.0;
    }

    public function topIndicators(array $features, int $limit = 5): array
    {
        arsort($features);
        return array_slice($features, 0, $limit, true);
    }

    private function zeroPixelSignals(string $html): int
    {
        $count = 0;
        $count += $this->count('/\bwidth\s*=\s*["\']?0(?:px)?["\']?/i', $html);
        $count += $this->count('/\bheight\s*=\s*["\']?0(?:px)?["\']?/i', $html);
        $count += $this->count('/width\s*:\s*0(?:px)?|height\s*:\s*0(?:px)?/i', $html);
        $count += $this->count('/display\s*:\s*none|visibility\s*:\s*hidden|opacity\s*:\s*0/i', $html);

        return $count;
    }

    private function count(string $pattern, string $subject): int
    {
        $matched = preg_match_all($pattern, $subject);
        return $matched === false ? 0 : $matched;
    }
}
