<?php
declare(strict_types=1);

namespace MestreMage\Correios\Api;

interface ImportexportManagementInterface
{

    /**
     * POST for importexport api
     * @param string $req
     * @return string
     */
    public function postImportexport($req);
}