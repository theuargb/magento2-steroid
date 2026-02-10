<?php

declare(strict_types=1);

namespace Theuargb\Steroids\Model;

use Magento\Framework\App\Response\HttpFactory as ResponseFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Theuargb\Steroids\Agent\Result\FallbackResult;

/**
 * Builds a self-contained HTTP response from fallback result.
 * Supports custom status codes and headers (redirects, 404s, etc.).
 */
class FallbackResponseBuilder
{
    public function __construct(
        private readonly ResponseFactory $responseFactory,
        private readonly MessageManagerInterface $messageManager
    ) {}

    public function build(FallbackResult $result): ResponseInterface
    {
        // Add customer message to session before redirect if provided
        if ($result->hasCustomerMessage()) {
            $this->messageManager->addNoticeMessage($result->getCustomerMessage());
        }

        $response = $this->responseFactory->create();
        $response->setHttpResponseCode($result->getStatusCode());
        
        // Set headers from result
        $headers = $result->getHeaders();
        
        // Set Content-Type default if not specified
        if (!isset($headers['Content-Type']) && !empty($result->getBody())) {
            $headers['Content-Type'] = 'text/html; charset=UTF-8';
        }
        
        foreach ($headers as $name => $value) {
            $response->setHeader($name, $value, true);
        }
        
        $response->setHeader('X-Steroids', 'fallback', true);
        $response->setBody($result->getBody());
        
        return $response;
    }
}
