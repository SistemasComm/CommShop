<?php
namespace Clearsale\Fingerprint\Block;

class Fingerprint extends \Magento\Framework\View\Element\Template
{
    const APP_NAME = 'clearsale_fingerprint/settings/app_name';
    const CONFIG_TAG_MANAGER = 'clearsale_fingerprint/settings/google_tag_manager';

    private $_sessionManager;
    
	public function __construct( 
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Session\SessionManager $sessionManager,
        array $data = []
    )
	{
        $this->_sessionManager = $sessionManager;
        parent::__construct($context, $data);
	}

    public function getAppName()
    {
        return $this->_scopeConfig->getValue(self::APP_NAME);
    }

    public function getGoogleTagManagerStatus()
    {
        return $this->_scopeConfig->getValue(self::CONFIG_TAG_MANAGER);
    }

    public function getSessionId()
    {
        return $this->_sessionManager->getSessionId();
    }

}