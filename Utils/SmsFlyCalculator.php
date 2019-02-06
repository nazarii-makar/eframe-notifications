<?php

namespace EFrame\Notifications\Utils;

use InvalidArgumentException;
use LogicException;

/**
 * Class SmsFlyCalculator
 * @package EFrame\Notifications\Utils
 */
class SmsFlyCalculator
{
    const SINGLE_PART_LENGTH = 70;
    const MULTI_PART_LENGTH = 67;

    /**
     * @param string $content
     * @return float
     */
    public static function calculateCost(string $content): float
    {
        $cost = config('notification.channels.smsfly.cost');

        if (null === $cost) {
            throw new LogicException('SMS-fly cost option is mandatory');
        }

        if (!is_numeric($cost)) {
            throw new InvalidArgumentException('Invalid SMS-fly cost given. Numeric expected');
        }

        $partsCount = static::getPartsCount($content);

        return $partsCount * $cost;
    }

    /**
     * @param string $content
     * @return bool
     */
    protected static function isSinglePart(string $content): bool
    {
        return mb_strlen($content) <= static::SINGLE_PART_LENGTH;
    }

    /**
     * @param string $content
     * @return bool
     */
    protected static function isMultiPart(string $content): bool
    {
        return !static::isSinglePart($content);
    }

    /**
     * @param string $content
     * @return int
     */
    protected static function getPartsCount(string $content): int
    {
        $divider = static::isSinglePart($content) ? static::SINGLE_PART_LENGTH : static::MULTI_PART_LENGTH;

        return ceil(mb_strlen($content) / $divider);
    }
}