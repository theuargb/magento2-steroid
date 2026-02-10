<?php

declare(strict_types=1);

namespace Theuargb\Steroids\Plugin;

use Magento\Framework\App\Http as MagentoHttp;
use Magento\Framework\App\ResponseInterface;
use Theuargb\Steroids\Model\HealingOrchestrator;
use Theuargb\Steroids\Helper\Config;
use Psr\Log\LoggerInterface;

class AppHttpPlugin
{
    public function __construct(
        private readonly HealingOrchestrator $orchestrator,
        private readonly Config $config,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * Around plugin on the main application launch.
     * Wraps Magento's entire request dispatch cycle including routing,
     * controller dispatch, layout rendering, and response sending.
     */
    public function aroundLaunch(
        MagentoHttp $subject,
        callable $proceed
    ): ResponseInterface {
        if (!$this->config->isEnabled()) {
            return $proceed();
        }

        try {
            return $proceed();
        } catch (\Throwable $e) {
            try {
                $result = $this->orchestrator->handle($e);
                if ($result !== null) {
                    return $result;
                }
            } catch (\Throwable $orchestratorException) {
                // The healer itself failed — never let it make things worse
                $this->logger->critical(
                    '[Steroids] Orchestrator failure, passing through original exception',
                    [
                        'original_exception' => $e->getMessage(),
                        'orchestrator_exception' => $orchestratorException->getMessage(),
                    ]
                );
            }

            // Could not heal — rethrow original so Magento's default
            // error handling takes over (error page, report, etc.)
            throw $e;
        }
    }
}
