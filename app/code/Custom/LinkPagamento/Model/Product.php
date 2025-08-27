<?php
namespace Custom\LinkPagamento\Model;

use Custom\LinkPagamento\Api\ProductInterface;
use Custom\LinkPagamento\Model\ResourceModel\Product as ResourceModel;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Webapi\Rest\Response;

class Product extends AbstractModel implements ProductInterface
{
    protected $random;
    protected $storeManager;
    protected $response;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        Random $random,
        StoreManagerInterface $storeManager,
        ResourceModel $resource = null,
        Response $response,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->random = $random;
        $this->storeManager = $storeManager;
        $this->response = $response;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * Post product data
     *
     * @param string $sku
     * @param int $quantity
     * @param float $price
     * @return string
     */
    public function postProduct($sku, $quantity, $price, $slug)
    {
        $return = [];
        try {
            $hashUrl = $this->random->getUniqueHash();
            $this->setData([
                'sku' => $sku,
                'quantity' => $quantity,
                'price' => $price,
                'hashurl' => $hashUrl,
                'slug' => $slug,
                'status' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $this->save();

            $baseUrl = $this->storeManager->getStore()->getBaseUrl();
            $completeUrl = $baseUrl . 'paymentlink/index/view/hash/' . $hashUrl;

            $return = [
                'code' => 200,
                'link' => $completeUrl
            ];
            return $this->response->setHeader('Content-Type', 'application/json', true)
                ->setBody(json_encode($return))
                ->sendResponse();
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }
}
