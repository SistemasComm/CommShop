<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Affiliate for Magento 2
 */

namespace Amasty\Affiliate\Model;

use Laminas\Validator\ValidatorChainFactory;
use Laminas\Validator\ValidatorInterface;

class EntityValidatorsProvider
{
    /**
     * @var ValidatorChainFactory
     */
    private $validatorChainFactory;

    /**
     * @var array
     */
    private $validators;

    public function __construct(
        ValidatorChainFactory $validatorChainFactory,
        array $validators = []
    ) {
        $this->validatorChainFactory = $validatorChainFactory;
        $this->validators = $validators;
    }

    /**
     * Retrieve validators for entity
     */
    public function get(string $entity): ValidatorInterface
    {
        $entityValidators = $this->validatorChainFactory->create();

        if (isset($this->validators[$entity])) {
            foreach ($this->validators[$entity] as $validator) {
                if (!$validator instanceof ValidatorInterface) {
                    throw new \InvalidArgumentException(
                        sprintf('Entity validator mus implement %s', ValidatorInterface::class)
                    );
                }
                $entityValidators->attach($validator);
            }

        }

        return $entityValidators;
    }
}
