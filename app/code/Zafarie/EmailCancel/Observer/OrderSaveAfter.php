<?php

namespace Zafarie\EmailCancel\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class OrderSaveAfter implements ObserverInterface
{
    protected $transportBuilder;
    protected $scopeConfig;
    protected $logger;

    public function __construct(
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();
        if ($order->getState() !== 'canceled') {
            return;
        }

        try {
            
            $templateId = $this->scopeConfig->getValue(
                'sales_email/order/cancel_template',
                ScopeInterface::SCOPE_STORE,
                $order->getStoreId()
            );
     $templateId = 18;
            
            $templateVars = [
                'order' => $order,
                'customer_name' => $order->getCustomerName(),
                'store' => $order->getStore()
            ];

            $sender = [
                'name' => $this->scopeConfig->getValue(
                    'trans_email/ident_sales/name',
                    ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                ),
                'email' => $this->scopeConfig->getValue(
                    'trans_email/ident_sales/email',
                    ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                )
            ];

            
            $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions([
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $order->getStoreId()
                ])
                ->setTemplateVars($templateVars)
                ->setFrom($sender)
                ->addTo($order->getCustomerEmail(), $order->getCustomerName());

            
            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();

        } catch (\Exception $e) {
            // Registra qualquer erro no log
            $this->logger->error('Erro ao enviar e-mail de cancelamento: ' . $e->getMessage());
        }
    }
}
