<?php
namespace Custom\LinkPagamento\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Magento\Quote\Model\QuoteRepository;

class SaveSlugToOrder implements ObserverInterface
{
    protected $logger;
    protected $quoteRepository;

    public function __construct(
        LoggerInterface $logger,
        QuoteRepository $quoteRepository
    ) {
        $this->logger = $logger;
        $this->quoteRepository = $quoteRepository;
    }

    public function execute(Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();
            $quoteId = $order->getQuoteId();

            if ($quoteId) {
                $quote = $this->quoteRepository->get($quoteId);
                $slug = $quote->getData('slug');
                $order->setData('slug', $slug);
                $order->save();
            } else {
                throw new \Exception('Ocorreu um erro ao gerar o pedido.');
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
