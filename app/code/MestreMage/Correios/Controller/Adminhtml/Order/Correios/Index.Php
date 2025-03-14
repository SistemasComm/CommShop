<?php
/**
 * MestreMage Correios Controller
 *
 * @category    MestreMage
 * @package     MestreMage_Correios
 * @author      MestreMage Software Private Limited
 *
 */
namespace MestreMage\Correios\Controller\Adminhtml\Correios;
 
class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
 
    /**
     * @param \Magento\Backend\App\Action\Context        $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) 
    {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
    }
 
    /**
     * Correios List page.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('MestreMage_Correios::correios_list');
        $resultPage->getConfig()->getTitle()->prepend(__('Correios Offline'));
 
        return $resultPage;
    }
 
    /**
     * Check Correios List Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MestreMage_Correios::correios_list');
    }
}