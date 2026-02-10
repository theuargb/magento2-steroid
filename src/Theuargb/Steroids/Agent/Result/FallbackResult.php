<?php

declare(strict_types=1);

namespace Theuargb\Steroids\Agent\Result;

/**
 * Structured result from the fallback HTML generation agent.
 * Supports full HTTP response: status code, headers, and body.
 */
class FallbackResult
{
    public function __construct(
        private readonly bool $hasHtml,
        private readonly string $html,
        private readonly int $statusCode = 200,
        private readonly array $headers = [],
        private readonly ?string $customerMessage = null
    ) {}

    public function hasHtml(): bool
    {
        return $this->hasHtml;
    }

    /**
     * Returns true if this is a usable response — either has HTML body
     * or is a redirect (3xx with Location header).
     */
    public function hasResponse(): bool
    {
        if ($this->hasHtml) {
            return true;
        }

        // 3xx redirects are valid even without a body
        if ($this->statusCode >= 300 && $this->statusCode < 400 && isset($this->headers['Location'])) {
            return true;
        }

        return false;
    }

    public function getHtml(): string
    {
        return $this->html;
    }

    public function getBody(): string
    {
        return $this->html;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getCustomerMessage(): ?string
    {
        return $this->customerMessage;
    }

    public function hasCustomerMessage(): bool
    {
        return !empty($this->customerMessage);
    }
}
