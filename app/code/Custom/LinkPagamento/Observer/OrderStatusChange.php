<?php
namespace Custom\LinkPagamento\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\HTTP\Client\Curl;

class OrderStatusChange implements ObserverInterface
{
    protected $logger;
    protected $curl;

    public function __construct(
        LoggerInterface $logger,
        Curl $curl
    ) {
        $this->logger = $logger;
        $this->curl = $curl;
    }

    public function execute(Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();
            $oldStatus = $order->getOrigData('status');
            $newStatus = $order->getStatus();

            // Verifica se o status mudou para "complete"
            if ($oldStatus !== $newStatus && $newStatus === 'complete') {
                $slug = $order->getData('slug');
                
                if ($slug) {
                    $this->curl->addHeader('Content-Type', 'application/json');
                    $this->curl->addHeader('x-integration-key', '344fe3a5-aa9e-4f1b-8428-37c203027ca2');
                    
                    $data = [
                        'sale' => [
                            'statusdesc' => 'ganha',
                            'substatusdesc' => 'emitida'
                        ]
                    ];

                    $url = 'https://api.vendapp.com.br/v1/integrations/commcenter/sales/' . $slug;
                    $this->curl->put($url, json_encode($data));

                    $response = $this->curl->getBody();
                    $this->logger->info('API Response for order ' . $order->getIncrementId() . ': ' . $response);
                }
            }
        } catch (\Exception $e) {
            $this->logger->error('Erro ao processar mudança de status do pedido: ' . $e->getMessage());
        }
    }
} 