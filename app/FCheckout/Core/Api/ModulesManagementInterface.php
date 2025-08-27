<?php

/**
 * @author Mestre Magento
 * @copyright Copyright (c) Mestre Magento (https://www.modulomagento.com.br/)
 * @license https://www.modulomagento.com.br/termos-condicoes/
 * @support contato@modulomagento.com.br
 */

declare(strict_types=1);

namespace FCheckout\Core\Api;

interface ModulesManagementInterface
{

    /**
     * GET for modules api
     * @param string $param
     * @return string
     */
    public function getModules($param);
}

