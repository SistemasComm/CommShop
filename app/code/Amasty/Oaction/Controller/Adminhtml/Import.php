<?php

namespace Amasty\Oaction\Controller\Adminhtml;

abstract class Import extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Amasty_Oaction::tracking_import';
}
