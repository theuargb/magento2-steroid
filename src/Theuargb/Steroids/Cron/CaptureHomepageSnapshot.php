<?php

declare(strict_types=1);

namespace Theuargb\Steroids\Cron;

use Psr\Log\LoggerInterface;
use Theuargb\Steroids\Helper\Config;
use Theuargb\Steroids\Model\HomepageSnapshotManager;

class CaptureHomepageSnapshot
{
    public function __construct(
        private readonly Config $config,
        private readonly HomepageSnapshotManager $snapshotManager,
        private readonly LoggerInterface $logger
    ) {}

    public function execute(): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $this->logger->info('[Steroids] Cron: capturing homepage snapshot');
        $this->snapshotManager->capture();
    }
}
