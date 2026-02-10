<?php

declare(strict_types=1);

namespace Theuargb\Steroids\Controller\Adminhtml\HealingLog;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    public const ADMIN_RESOURCE = 'Theuargb_Steroids::healing_log';

    private PageFactory $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Theuargb_Steroids::healing_log');
        $resultPage->getConfig()->getTitle()->prepend(__('AI Self-Healing Log'));
        return $resultPage;
    }
}
