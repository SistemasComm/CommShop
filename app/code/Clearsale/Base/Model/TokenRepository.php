<?php


namespace Clearsale\Base\Model;

use Clearsale\Base\Api\TokenRepositoryInterface;
use Clearsale\Base\Api\Data\TokenInterface;
use Clearsale\Base\Model\ResourceModel\Token as TokenResource;
use Clearsale\Base\Model\ResourceModel\Token\CollectionFactory as TokenCollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;

class TokenRepository implements TokenRepositoryInterface
{
    protected $_resource;

    protected $_collection;

    protected $_tokenFactory;

    public function __construct(
        TokenResource $resource,
                                    TokenCollectionFactory $tokenCollectionFactory,
                                    \Clearsale\Base\Model\TokenFactory $tokenFactory
    ) {
        $this->_resource = $resource;
        $this->_collection = $tokenCollectionFactory;
        $this->_tokenFactory = $tokenFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(TokenInterface $token)
    {
        try {
            $this->_resource->save($token);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\LocalizedException(__(
                'Could not save the token: %1',
                $exception->getMessage()
            ));
        }

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($product_id)
    {
        $token = $this->_tokenFactory->create();
        $token->load($product_id, 'product_id');

        if (!$token->getId()) {
            return false;
        }

        return $token;
    }

    /**
    * {@inheritdoc}
    */
    public function delete(TokenInterface $token)
    {
        try {
            $this->_resource->delete($job);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the token: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($product_id)
    {
        return $this->delete($this->getById($product_id));
    }
}
