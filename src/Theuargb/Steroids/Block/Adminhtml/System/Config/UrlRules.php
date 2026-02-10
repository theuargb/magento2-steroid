<?php

declare(strict_types=1);

namespace Theuargb\Steroids\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

/**
 * Dynamic rows for URL rules with per-URL prompt configuration.
 * Each row defines URL pattern with separate healer/fallback prompts and toggles.
 * First match wins — order matters.
 */
class UrlRules extends AbstractFieldArray
{
    protected function _prepareToRender(): void
    {
        $this->addColumn('pattern', [
            'label' => __('URL Pattern'),
            'class' => 'required-entry',
            'style' => 'width:150px',
        ]);

        $this->addColumn('healer_prompt', [
            'label' => __('Healer Instructions'),
            'style' => 'width:250px',
        ]);

        $this->addColumn('fallback_prompt', [
            'label' => __('Fallback Instructions'),
            'style' => 'width:250px',
        ]);

        $this->addColumn('allow_healing', [
            'label' => __('Try Healing'),
            'style' => 'width:60px',
        ]);

        $this->addColumn('allow_fallback', [
            'label' => __('Allow Fallback'),
            'style' => 'width:60px',
        ]);

        $this->addColumn('cache_fallback', [
            'label' => __('Cache Fallback'),
            'style' => 'width:60px',
        ]);

        $this->addColumn('enabled', [
            'label' => __('Enabled'),
            'style' => 'width:60px',
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add URL Rule');
    }

    protected function _prepareArrayRow(DataObject $row): void
    {
        // No custom option hash needed for simple text columns
    }
}
