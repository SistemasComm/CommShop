<?php

namespace Commshop\Admincpf\Plugin\Model;

class User
{
    public function beforeSave(\Magento\User\Model\User $subject)
    {
        $cpf = $subject->getData('cpf');
        \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug('CPF PLUGIN: ' . $cpf);

        // Verifica se o CPF está definido e se não é o mesmo que o original (para evitar regravações desnecessárias)
        if ($cpf && $cpf != $subject->getOrigData('cpf')) {
            $subject->setData('cpf', $cpf);
        }
    }
}