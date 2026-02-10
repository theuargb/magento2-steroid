<?php

declare(strict_types=1);

namespace Theuargb\Steroids\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class HomepageSnapshot extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('steroids_homepage_snapshot', 'entity_id');
    }
}
