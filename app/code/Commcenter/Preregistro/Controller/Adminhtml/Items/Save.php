<?php
/**
 * @category   Commcenter
 * @package    Commcenter_Preregistro
 * @author     yurirneves@gmail.com
 */

namespace Commcenter\Preregistro\Controller\Adminhtml\Items;

class Save extends \Commcenter\Preregistro\Controller\Adminhtml\Items
{
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                $model = $this->_objectManager->create('Commcenter\Preregistro\Model\Preregistro');
                $data = $this->getRequest()->getPostValue();

                // Remover lógica de imagem, pois não estamos lidando com arquivos
                $inputFilter = new \Zend_Filter_Input([], [], $data);
                $data = $inputFilter->getUnescaped();

                // Carrega o ID do item, se houver
                $id = $this->getRequest()->getParam('id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('O item especificado está incorreto.'));
                    }
                }

                // Configura os dados e salva o modelo
                $model->setData($data);
                $session = $this->_objectManager->get('Magento\Backend\Model\Session');
                $session->setPageData($model->getData());
                $model->save();

                // Mensagem de sucesso
                $this->messageManager->addSuccess(__('Você salvou o item.'));
                $session->setPageData(false);

                // Verifica se é necessário redirecionar para a página de edição novamente
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('commcenter_preregistro/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('commcenter_preregistro/*/');
                return;

            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int)$this->getRequest()->getParam('id');
                if (!empty($id)) {
                    $this->_redirect('commcenter_preregistro/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('commcenter_preregistro/*/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Algo deu errado ao salvar os dados do item. Por favor, revise o log de erros.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                $this->_redirect('commcenter_preregistro/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->_redirect('commcenter_preregistro/*/');
    }
}
