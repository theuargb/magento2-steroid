<?php

declare(strict_types=1);

namespace Theuargb\Steroids\Model;

use Theuargb\Steroids\Helper\Config;

/**
 * First-match URL rule matcher.
 * 
 * Iterates through configured URL rules top-to-bottom and returns
 * the first enabled matching rule. Pattern '*' matches everything.
 */
class UrlFilterMatcher
{
    public function __construct(
        private readonly Config $config
    ) {}

    /**
     * Returns true if the URL matches any enabled rule.
     */
    public function shouldIntercept(string $url): bool
    {
        return $this->getMatchingRule($url) !== null;
    }

    /**
     * Get the first enabled rule that matches the URL.
     * Returns full rule array or null if no match.
     * 
     * Rule array contains:
     * - pattern: URL pattern (glob-style, * = wildcard)
     * - healer_prompt: admin instructions for healer agent
     * - fallback_prompt: admin instructions for fallback agent
     * - allow_healing: 1/0
     * - allow_fallback: 1/0
     * - enabled: 1/0
     */
    public function getMatchingRule(string $url): ?array
    {
        foreach ($this->config->getUrlRules() as $rule) {
            if (empty($rule['enabled'])) {
                continue;
            }
            
            if (empty($rule['pattern'])) {
                continue;
            }

            if ($this->matchesPattern($url, $rule['pattern'])) {
                return $rule;
            }
        }

        return null;
    }

    /**
     * Match URL against a glob-style pattern.
     * Supports * (any chars) and ? (single char).
     */
    private function matchesPattern(string $url, string $pattern): bool
    {
        // Escape regex special chars, then convert glob wildcards to regex
        $regex = preg_quote($pattern, '#');
        $regex = str_replace(['\*', '\?'], ['.*', '.'], $regex);
        $regex = '#^' . $regex . '#';
        return (bool) preg_match($regex, $url);
    }
}
