<?php

declare(strict_types = 1);

namespace Garanaw\SeedableMigrations\Enum;

enum SeedAt: string
{
    case EACH = 'each';
    case AFTER = 'after';
    case END = 'end';
    case NEVER = 'never';
    case NONE = 'none';

    /**
     * Checks if the current value is each.
     *
     * @return bool
     */
    public function each(): bool
    {
        return $this === self::EACH;
    }

    /**
     * Checks if the current value is end.
     *
     * @return bool
     */
    public function end(): bool
    {
        return $this === self::END;
    }

    /**
     * Checks if the current value is after.
     *
     * @return bool
     */
    public function after(): bool
    {
        return $this === self::AFTER;
    }

    /**
     * Checks if the current value is never.
     *
     * @return bool
     */
    public function never(): bool
    {
        return $this === self::NEVER;
    }

    /**
     * Checks if the current value is none.
     *
     * @return bool
     */
    public function none(): bool
    {
        return $this === self::NONE;
    }
}
