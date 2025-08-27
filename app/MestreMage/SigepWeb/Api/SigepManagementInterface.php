<?php


namespace MestreMage\SigepWeb\Api;

interface SigepManagementInterface
{

    /**
     * GET for sigep api
     * @param string $param
     * @return string
     */
    public function getSigep($param);
}
