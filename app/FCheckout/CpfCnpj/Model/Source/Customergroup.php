<?php
namespace FCheckout\CpfCnpj\Model\Source;


class Customergroup implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $groups = $objectManager->get('\Magento\Customer\Model\ResourceModel\Group\Collection');
        $groupsArr = [];
        $groupsArr[] = ['value' => '', 'label' => 'Use Default Group'];

        foreach ($groups as $group) {
            if($group->getCode()!="NOT LOGGED IN"){
                $groupsArr[] = ['value' => $group->getId(), 'label' => $group->getCode()];
            }
        }

        return $groupsArr;
     }
}
