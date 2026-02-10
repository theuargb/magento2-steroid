<?php

declare(strict_types=1);

namespace Theuargb\Steroids\Model;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Serialize\Serializer\Json;
use Theuargb\Steroids\Agent\Result\FallbackResult;
use Theuargb\Steroids\Helper\Config;
use Psr\Log\LoggerInterface;

/**
 * Caches fallback responses keyed by URL + error fingerprint + Magento HTTP context vary string.
 * Uses Magento's built-in cache framework, flushable via standard cache management.
 */
class FallbackCache
{
    private const CACHE_PREFIX = 'steroids_fb_';
    private const CACHE_TAG = 'STEROIDS_FALLBACK';

    public function __construct(
        private readonly CacheInterface $cache,
        private readonly HttpContext $httpContext,
        private readonly Json $json,
        private readonly Config $config,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * Look up a cached fallback response.
     */
    public function load(string $url, string $fingerprint): ?FallbackResult
    {
        $key = $this->buildCacheKey($url, $fingerprint);

        try {
            $data = $this->cache->load($key);
            if ($data === false) {
                return null;
            }

            $decoded = $this->json->unserialize($data);
            if (!is_array($decoded) || !isset($decoded['status_code'])) {
                return null;
            }

            $this->logger->info("[Steroids] Fallback cache HIT for {$url} (key: {$key})");

            return new FallbackResult(
                hasHtml: !empty($decoded['body']),
                html: $decoded['body'] ?? '',
                statusCode: (int) ($decoded['status_code'] ?? 200),
                headers: $decoded['headers'] ?? [],
                customerMessage: $decoded['customer_message'] ?? null
            );
        } catch (\Throwable $e) {
            $this->logger->error('[Steroids] Fallback cache load error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Store a fallback response in cache.
     */
    public function save(string $url, string $fingerprint, FallbackResult $result): void
    {
        $key = $this->buildCacheKey($url, $fingerprint);

        try {
            $data = $this->json->serialize([
                'body' => $result->getBody(),
                'status_code' => $result->getStatusCode(),
                'headers' => $result->getHeaders(),
                'customer_message' => $result->getCustomerMessage(),
            ]);

            $ttl = $this->config->getFallbackCacheTtl();

            $this->cache->save($data, $key, [self::CACHE_TAG], $ttl);
            $this->logger->info("[Steroids] Fallback cached for {$url} (key: {$key}, ttl: {$ttl}s)");
        } catch (\Throwable $e) {
            $this->logger->error('[Steroids] Fallback cache save error: ' . $e->getMessage());
        }
    }

    /**
     * Build a unique cache key from URL, fingerprint, and Magento HTTP context vary string.
     * The vary string ensures different customer groups, currencies, store views, etc.
     * get separate cached responses.
     */
    private function buildCacheKey(string $url, string $fingerprint): string
    {
        $varyString = $this->httpContext->getVaryString();
        $raw = $url . '|' . $fingerprint . '|' . ($varyString ?? '');
        return self::CACHE_PREFIX . hash('sha256', $raw);
    }
}
