<?php
declare(strict_types=1);

namespace Amasty\Oaction\Model\Action;

use Amasty\Oaction\Helper\Data;

class ActionChecker
{
    public const ACTION_DELIMITER = 'amasty_oaction_delemiter';
    public const ACTION_SIGN = 'amasty_oaction';

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var array|null
     */
    private $availableActions = null;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param string $actionName
     * @return bool
     */
    public function isActionAvailable(string $actionName): bool
    {
        if ($actionName == self::ACTION_DELIMITER
            || strpos($actionName, self::ACTION_SIGN) === false
        ) {
            return true;
        }

        return in_array($actionName, $this->getAvailableActions());
    }

    private function getAvailableActions(): array
    {
        if ($this->availableActions === null) {
            $this->availableActions = explode(
                ',',
                (string)$this->helper->getModuleConfig('general/commands')
            );
        }

        return $this->availableActions;
    }
}
