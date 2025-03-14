<?php

/**

 * @copyright   2018 FCheckout (https://mestremage.com.br)
 * @license     https://mestremage.com.br Copyright
 * @author Mestre Magento <contato@modulomagento.com.br>

 */

namespace FCheckout\CpfCnpj\Controller\Index;

use Magento\Framework\App\Action\Action;

use Magento\Framework\App\ResponseInterface;

use Magento\Framework\Controller\ResultFactory;

class Index extends Action

{

    /**

     * Dispatch request

     *

     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface

     * @throws \Magento\Framework\Exception\NotFoundException

     */

    public function execute()

    {

        return $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);

    }

}