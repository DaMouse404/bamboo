<?php

namespace Bamboo;

/**
 * A class representing a Counter
 */
class Counter
{

    protected static $_counter = null;
    
    /**
     * Set the logger to use
     */
    public static function setCounter($counter) {
        self::$_counter = $counter;
    }

    /* 
     * A proxy method for the counter
     */
    public static function increment($counterName) {
        $counter = self::$_counter;
        $counter::increment(
            constant("$counter::$counterName")
        ); 
    }
}