<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Affiliate for Magento 2
 */

namespace Amasty\Affiliate\Block\Account;

use Magento\Store\Model\ScopeInterface;

class Traffic extends \Amasty\Affiliate\Block\Account\Social
{
    /**
     * @var string
     */
    protected $_template = 'account/traffic.phtml';

    public function getText()
    {
        return $this->_scopeConfig->getValue('amasty_affiliate/friends/text_traffic', ScopeInterface::SCOPE_STORE);
    }
}
