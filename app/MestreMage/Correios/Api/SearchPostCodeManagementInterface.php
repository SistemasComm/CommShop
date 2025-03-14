<?php
declare(strict_types=1);

namespace MestreMage\Correios\Api;

interface SearchPostCodeManagementInterface
{

    /**
     * GET for searchPostCode api
     * @param string $param
     * @return string
     */
    public function getSearchPostCode($param);
}

