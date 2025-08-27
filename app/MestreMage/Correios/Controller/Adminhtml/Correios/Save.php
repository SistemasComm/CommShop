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

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \MestreMage\Correios\Model\CorreiosFactory
     */
    var $correiosFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \MestreMage\Correios\Model\CorreiosFactory $correiosFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \MestreMage\Correios\Model\CorreiosFactory $correiosFactory
    ) {
        parent::__construct($context);
        $this->correiosFactory = $correiosFactory;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('correios/correios/addrow');
            return;
        }
        try {
            $rowData = $this->correiosFactory->create();
            $rowData->setData($data);
            if (isset($data['id'])) {
                $rowData->setEntityId($data['id']);
            }
            $rowData->save();
            $this->messageManager->addSuccess(__('Row data has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('correios/correios/index');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MestreMage_Correios::save');
    }
}
