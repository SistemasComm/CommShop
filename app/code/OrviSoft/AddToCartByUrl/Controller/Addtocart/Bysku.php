<?php

namespace OrviSoft\AddToCartByUrl\Controller\Addtocart;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Model\Product;


class Bysku extends \Magento\Framework\App\Action\Action
{

    protected $formKey;   
    protected $cart;
    protected $product;
    private $productRepository;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        Context $context,
        FormKey $formKey,
        Cart $cart,
        Product $product,
        \Magento\Catalog\Model\ProductRepository $productRepository) {
            $this->formKey = $formKey;
            $this->cart = $cart;
            $this->product = $product;    
            $this->productRepository = $productRepository;  
            parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        //echo "Here we go...!";
        $skuParam = $this->getRequest()->getParam('sku');
        
        // Verifica se há múltiplos SKUs (separados por vírgula)
        $skuList = explode(',', $skuParam);
        
        $addedProducts = 0;
        $errorProducts = 0;
        $formKey = $this->formKey->getFormKey();
        
        foreach ($skuList as $sku) {
            // Remove espaços em branco
            $sku = trim($sku);
            
            if (empty($sku)) {
                continue;
            }
            
            try {
                $product = $this->productRepository->get($sku);
                $productId = $product->getId();
                
                $params = array(
                    'form_key' => $formKey,
                    'product' => $productId, 
                    'qty' => 1
                );
                
                $this->cart->addProduct($product, $params);
                $addedProducts++;
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                // Produto não encontrado
                $errorProducts++;
                continue;
            } catch (\Exception $e) {
                // Outros erros
                $errorProducts++;
                continue;
            }
        }
        
        if ($addedProducts > 0) {
            $this->cart->save();
            
            // Adiciona mensagem de sucesso
            $this->messageManager->addSuccessMessage(
                __('%1 produto(s) adicionado(s) ao carrinho.', $addedProducts)
            );
            
            if ($errorProducts > 0) {
                // Informa sobre produtos que não puderam ser adicionados
                $this->messageManager->addNoticeMessage(
                    __('%1 produto(s) não puderam ser adicionados ao carrinho.', $errorProducts)
                );
            }
        } else {
            // Adiciona mensagem de erro se nenhum produto pôde ser adicionado
            $this->messageManager->addErrorMessage(
                __('Nenhum produto pôde ser adicionado ao carrinho. Verifique os SKUs fornecidos.')
            );
        }
        
        return $this->_redirect('checkout/cart');
    }
}