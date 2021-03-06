<?php

namespace Bamboo;

/**
 * A class representing a Counter
 */
class Counter
{

    protected static $_counter = null;

    /**
     * Set the counter to use
     */
    public static function setCounter($counter) {
        self::$_counter = $counter;
    }

    /**
     * Get the counter used
     */
    public static function getCounter() {
        return self::$_counter;
    }

    /*
     * A proxy method for the counter. Dynamically looks up real counter name.
     */
    public static function increment($counterName) {
        $counter = self::$_counter;
        $counter::increment(
            constant("$counter::$counterName")
        );
    }
}
