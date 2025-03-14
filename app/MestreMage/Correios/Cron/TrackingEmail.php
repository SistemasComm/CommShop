<?php

declare(strict_types=1);

namespace MestreMage\Correios\Cron;

use MestreMage\Correios\Model\Carrier\Correios;
use MestreMage\Correios\Model\WhatsAppTrackingShipping;

class TrackingEmail
{
    public function execute()
    {
        $from = date('Y-m-d h:i:s', strtotime('-45 day', strtotime(date("Y-m-d h:i:s"))));
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $orderDatamodel = $objectManager->get('Magento\Sales\Model\Order')->getCollection()
            ->setOrder('created_at','DESC')
            ->addFieldToFilter('created_at', array('from'=>$from));


            foreach($orderDatamodel as $order) {
                    $trackNumbers = '';
                    $tracksCollection = $order->getTracksCollection();
                    foreach ($tracksCollection->getItems() as $track) {
                        $trackNumbers = $track->getTrackNumber();
                    }
        
                if($trackNumbers){
                    $tracking = (array)Correios::getTrackingInfoByCode($trackNumbers);
                    if(isset($tracking['success'])){
                        $change_tracking = $tracking[0]['date'].'|'.$tracking[0]['hour'];
                        if($order->getPayment()->getAdditionalInformation('object_time_tracking') != $change_tracking){
                            $order->getPayment()->setAdditionalInformation('object_time_tracking',$change_tracking)->save();

                            $this->sendEmailTrackingShipping($order, $tracking);

                            $this->sendWhatsAppTrackingShipping($order, $tracking);

                        }

                    }
                }
            }

        }

        public function sendEmailTrackingShipping($order, $info_shipping){
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
            $transportBuilder = $objectManager->get('\Magento\Framework\Mail\Template\TransportBuilder');

                $sender = [
                    'email' => $this->getConfig('trans_email/ident_sales/email'),
                    'name' => $this->getConfig('trans_email/ident_sales/name')
                ];
            
                $transport= $transportBuilder->setTemplateIdentifier($this->getConfig('carriers/correios/shipping_tracking'))
                    ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND,'store' => $storeManager->getStore()->getId(),])
                    ->setTemplateVars([
                        'message' => $info_shipping[0]['message'],
                        'location' => $info_shipping[0]['location'],
                        'customer_name' => $order['customer_firstname'],
                        'url_sales_order_view' => $storeManager->getStore()->getBaseUrl().'sales/order/view/order_id/'.$order['entity_id'],
                        'order_id' => $order['increment_id'],
                        'subject' => 'Pedido '.$order['increment_id'].': Nova Atualização'
                      ])
                    ->setFrom($sender)
                    ->addTo($order['customer_email'], '')
                    ->getTransport();
                $transport->sendMessage();

                 $historyItem = $order->addStatusHistoryComment($info_shipping[0]['message'], \Magento\Sales\Model\Order::STATE_COMPLETE);
                 $historyItem->setIsVisibleOnFront(true);
                 $historyItem->setIsCustomerNotified(true);
                 $historyItem->save();
        }

        public function sendWhatsAppTrackingShipping($order, $info_shipping){
            $shippingAddress = $order->getShippingAddress();
            $to = $order->getPayment()->getAdditionalInformation('number_whatsapp_send');
            
            if($to){
                $message = 'Informações sobre o pedido '.$order['increment_id'].' - '.$info_shipping[0]['message'];
                if($this->getConfig('modulo_correios_tracking_whatsapp/settings_api_twilio/active')){
                    $from = $this->getConfig('modulo_correios_tracking_whatsapp/settings_api_twilio/tel_twilio');
                    $id = $this->getConfig('modulo_correios_tracking_whatsapp/settings_api_twilio/sid_twilio');
                    $token = $this->getConfig('modulo_correios_tracking_whatsapp/settings_api_twilio/auth_token_twilio');
                    WhatsAppTrackingShipping::twilio($id, $token, $to, $from, $message);
                }
            }
        }

        public function getConfig($valor){
            $scopeConfig = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Config\ScopeConfigInterface');
            return $scopeConfig->getValue($valor, \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
        }

    }

