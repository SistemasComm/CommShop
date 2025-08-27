<?php
 /**
 * Mestre Magento
 * www.modulomagento.com.br
 *
 * LICENÇA DE USO
 * Este arquivo está sujeito ao EULA (contrato de licença para usuário final).
 * @copyright Copyright (c) Mestre Magento. ( https://www.modulomagento.com.br/ )
 * @license   https://www.modulomagento.com.br/Mestre-Magento-Licenca.txt
 *
 * ##############################################################################
 * #																			#
 * #  Nós programadores nos dedicamos muito, da mesma forma que você, então		#
 * #  valorize sua profissão e não aceite usar esse módulo de forma ilícita.	#
 * #  Denuncie anônimamente a empresa que utilizar esse módulo de forma ilegal.	#
 * #  Estamos abertos a parcerias com desenvolvedores ;)						#
 * #																			#
 * ##############################################################################
 *
 */

namespace MestreMage\Correios\Controller\Adminhtml\Correios;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use MestreMage\Correios\Model\ResourceModel\Correios\CollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Massactions filter.
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Context           $context
     * @param Filter            $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {

        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $recordDeleted = 0;
        foreach ($collection->getItems() as $record) {
            $record->setId($record->getEntityId());
            $record->delete();
            $recordDeleted++;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $recordDeleted));

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }

    /**
     * Check Category Map recode delete Permission.
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MestreMage_Correios::row_data_delete');
    }
}
