<?php
/**
 * prestashop Project ${PROJECT_URL}
 *
 * @link      ${GITHUB_URL} Source code
 */

namespace MestreMage\SigepWeb\PhpSigep\Cache;

use MestreMage\SigepWeb\PhpSigep\Cache\Storage\Adapter\AdapterOptions;
use MestreMage\SigepWeb\PhpSigep\Cache\Storage\Adapter\Exception\InvalidAdapterNameException;
use MestreMage\SigepWeb\PhpSigep\Cache\Storage\Adapter\FileSystem;
use MestreMage\SigepWeb\PhpSigep\Config;
use MestreMage\SigepWeb\PhpSigep\FactoryInterface;

class Factory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param Config $config
     * @return mixed
     */
    public function createService(Config $config)
    {
        $options = $config->getCacheOptions();

        $storage = new FileSystem($options->getStorageOptions());

        return $storage;
    }

    /**
     * @param Options $options
     * @return \PhpSigep\Cache\Storage\Adapter\AdapterOptions
     */
    public function getStorageOptions(Options $options)
    {
        $storageOptions = $options->getStorageOptions();

        if (!($storageOptions instanceof AdapterOptions)) {
            $storageOptions = new AdapterOptions((array)$storageOptions);
        }

        return $storageOptions;
    }
} 