<?php

namespace Clearsale\Total\Controller\Webhook;

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;

class Index extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    protected $_totalModel;

    public function __construct(
        \Clearsale\Total\Model\Total $total,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->_totalModel = $total;
    }
    public function execute()
    {
        $success = false;

        try {
            $obj = json_decode(utf8_encode(file_get_contents('php://input')));

            if ($obj) {
                $success = $this->_totalModel->sendOrders($obj->code);
            }

            if (!$success) {
                $this->getResponse()->setHttpResponseCode(400);
            }

            if ($success) {
                $this->getResponse()->setHttpResponseCode(200);
            }
        } catch (\Exception $e) {
            $this->getResponse()->setHttpResponseCode(400);
        }
    }
	
	
	/**
     * Create exception in case CSRF validation failed.
     * Return null if default exception will suffice.
     *
     * @param RequestInterface $request
     *
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * Perform custom request validation.
     * Return null if default validation is needed.
     *
     * @param RequestInterface $request
     *
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
