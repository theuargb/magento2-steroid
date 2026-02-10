<?php

declare(strict_types=1);

namespace Theuargb\Steroids\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class HealingAttempt extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('steroids_attempt', 'entity_id');
    }
}
