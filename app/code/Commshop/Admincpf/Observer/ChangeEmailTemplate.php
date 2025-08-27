<?php

namespace Commshop\Admincpf\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Email\Model\TemplateFactory;
use Psr\Log\LoggerInterface;

class ChangeEmailTemplate implements ObserverInterface
{
    protected $scopeConfig;
    protected $orderSender;

    public function __construct(
        TemplateFactory $templateFactory,
        LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
    ) {
        $this->templateFactory = $templateFactory;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->orderSender = $orderSender;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();
            $paymentMethod = $order->getPayment()->getMethod();
        
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/yuri.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info($paymentMethod);

            if ($paymentMethod == 'getnet_paymentmagento_getpay') {
                $templateId = 3; // Substitua pelo ID do template que você anotou no painel.

                $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/yuri.log');
                $logger = new \Zend_Log();
                $logger->addWriter($writer);
                $logger->info('entrou no if');
        
                $template = $this->templateFactory->create();
                $template->load($templateId);
                
                if ($template->getId()) {
                    $logger->addWriter($writer);
                    $logger->info('entrou no if do id');
                    
                    $order->setEmailSent(null)
                          ->setEmailTemplate($templateId);
                }
                
                $newSubject = "Seu novo assunto aqui";

                // Modifica o assunto do e-mail
                $order->setEmailSubject($newSubject);

                
            }
        } catch (\Exception $e) {
            // Logar a exceção para diagnóstico posterior
            $this->logger->critical($e->getMessage());
        }
        
    }


    
}

