<?php

declare(strict_types=1);

namespace Amasty\Oaction\Plugin\Ui\Model;

use Magento\Ui\Config\Reader as ConfigReader;

class Reader extends AbstractReader
{
    /**
     * @param ConfigReader $subject
     * @param array        $result
     *
     * @return array
     */
    public function afterRead(ConfigReader $subject, array $result): array
    {
        return $this->updateMassactions($result);
    }
}
