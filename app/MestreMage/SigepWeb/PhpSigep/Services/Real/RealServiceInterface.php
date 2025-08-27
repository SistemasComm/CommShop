<?php
/**
 * prestashop Project ${PROJECT_URL}
 *
 * @link      ${GITHUB_URL} Source code
 */
 
namespace MestreMage\SigepWeb\PhpSigep\Services\Real;

use MestreMage\SigepWeb\PhpSigep\Model\AbstractModel;
use MestreMage\SigepWeb\PhpSigep\Services\Result;

interface RealServiceInterface
{
    /**
     * @param AbstractModel $params
     * @return Result
     */
    public function execute(AbstractModel $params);
} 