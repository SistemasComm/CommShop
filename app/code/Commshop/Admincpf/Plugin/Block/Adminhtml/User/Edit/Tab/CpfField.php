<?php 

namespace Commshop\Admincpf\Plugin\Block\Adminhtml\User\Edit\Tab;

class CpfField
{
    public function aroundGetFormHtml(
        \Magento\User\Block\User\Edit\Tab\Main $subject,
        \Closure $proceed
    ) {
        $form = $subject->getForm();

        if (is_object($form)) {
            // Carregar o usuário atualmente editado
            $userId = $subject->getRequest()->getParam('user_id');
            $userModel = \Magento\Framework\App\ObjectManager::getInstance()->create(\Magento\User\Model\User::class)->load($userId);
            $cpfValue = $userModel->getCpf();

            // Adicionar o campo CPF
            $fieldset = $form->addFieldset('admin_user_cpf', ['legend' => __('Additional Info')]);
            $fieldset->addField(
                'cpf',
                'text',
                [
                    'name' => 'cpf',
                    'label' => __('CPF'),
                    'id' => 'cpf',
                    'title' => __('CPF'),
                    'value' => $cpfValue,
                    'required' => true
                ]
            );

            $subject->setForm($form);
        }

        return $proceed();
    }
}
