<?php

declare(strict_types=1);

namespace Theuargb\Steroids\Model\ResourceModel\HomepageSnapshot;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Theuargb\Steroids\Model\HomepageSnapshot as Model;
use Theuargb\Steroids\Model\ResourceModel\HomepageSnapshot as Resource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    protected function _construct(): void
    {
        $this->_init(Model::class, Resource::class);
    }
}
