<?php

declare(strict_types=1);

namespace Theuargb\Steroids\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\HttpFactory as ResponseFactory;
use Magento\Framework\App\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * After a successful healing that modifies files on disk, we cannot
 * re-dispatch in the same PHP process — PHP has already loaded the old
 * class definitions and Magento's area code is already set.
 *
 * Instead we send a 302 redirect to the same URL, which spawns a fresh
 * PHP-FPM worker that reads the patched files from disk.
 *
 * Before redirecting we invalidate opcache for any files the agent wrote
 * so the new process doesn't serve stale bytecode.
 */
class RequestRedispatcher
{
    public function __construct(
        private readonly ResponseFactory $responseFactory,
        private readonly RequestInterface $request,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * Build a 302 redirect response to the same URL.
     * Invalidates opcache for written files to ensure the new process
     * picks up disk changes immediately.
     *
     * @param array $writtenFiles Absolute paths of files changed by the agent
     */
    public function buildRedirect(array $writtenFiles = []): ResponseInterface
    {
        // Invalidate opcache for every file the agent touched
        if (function_exists('opcache_invalidate')) {
            foreach ($writtenFiles as $file) {
                opcache_invalidate($file, true);
                $this->logger->info('[Steroids] Invalidated opcache for: ' . $file);
            }
        }

        $redirectUrl = $this->request->getRequestUri();
        $this->logger->info('[Steroids] Redirecting after healing to: ' . $redirectUrl);

        $response = $this->responseFactory->create();
        $response->setHttpResponseCode(302);
        $response->setHeader('Location', $redirectUrl, true);
        $response->setHeader('X-Steroids', 'healed-redirect', true);
        $response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate', true);
        $response->setBody('');

        return $response;
    }
}
