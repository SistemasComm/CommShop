<?php
namespace Custom\LinkPagamento\Controller\Index;

use Custom\LinkPagamento\Model\ProductFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Magento\Quote\Model\QuoteFactory;

class View extends Action
{
    protected $cart;
    protected $productRepository;
    protected $productFactory;
    protected $logger;
    protected $quoteFactory;

    public function __construct(
        Context $context,
        Cart $cart,
        ProductRepository $productRepository,
        ProductFactory $productFactory,
        LoggerInterface $logger,
        QuoteFactory $quoteFactory
    ) {
        $this->cart = $cart;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->logger = $logger;
        $this->quoteFactory = $quoteFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $hash = $this->getRequest()->getParam('hash');

        try {
            $productModel = $this->productFactory->create();
            $productData = $productModel->getCollection()
                ->addFieldToFilter('hashurl', $hash)
                ->getFirstItem();

            if (!$productData->getId()) {
                throw new NoSuchEntityException(__('URL inválida.'));
            }

            $sku = $productData->getSku();
            $quantity = $productData->getQuantity();
            $price = $productData->getPrice();
            $slug = $productData->getSlug();

            $product = $this->productRepository->get($sku);
            $params = [
                'product' => $product->getId(),
                'qty' => $quantity
            ];

            $cartItem = $this->cart->addProduct($product, $params)->getQuote()->getItemByProduct($product);
            if ($cartItem) {
                $cartItem->setCustomPrice($price);
                $cartItem->setOriginalCustomPrice($price);
                $cartItem->getProduct()->setIsSuperMode(true);
            }
            $this->cart->save();

            if ($slug) {
                $quoteId = $this->cart->getQuote()->getId();
                $quote = $this->quoteFactory->create()->load($quoteId);
                $quote->setData('slug', $slug);

                $quote->save();
            }

            $productData->setStatus(1);
            $productData->save();

            $this->messageManager->addSuccessMessage(__('Produto adicionado ao carrinho!'));

            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('checkout');

        } catch (NoSuchEntityException $e) {
            $this->logger->critical($e->getMessage());
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $this->messageManager->addErrorMessage(__('Não foi possível adicionar o produto no carrinho.'));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('/');
    }
}
