<?php
declare(strict_types=1);

namespace Amasty\Oaction\Model\Action\Modifier;

interface ActionModifierInterface
{
    public function modify(array &$item): void;
}
