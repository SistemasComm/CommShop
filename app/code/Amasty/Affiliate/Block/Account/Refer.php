<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Affiliate for Magento 2
 */

namespace Amasty\Affiliate\Block\Account;

use Magento\Store\Model\ScopeInterface;

class Refer extends \Amasty\Affiliate\Block\Account\Social
{
    /**
     * @var string
     */
    protected $_template = 'account/refer.phtml';

    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Refer Friends'));
    }

    public function getText()
    {
        return $this->_scopeConfig->getValue('amasty_affiliate/friends/text', ScopeInterface::SCOPE_STORE);
    }
}
