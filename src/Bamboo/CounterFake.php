<?php

namespace Bamboo;

/**
 * A class representing a Fake Counter
 */
class CounterFake
{
    private static $_countArray = array(
        'bambooError500' => 0,
        'bambooError404' => 0
    );

    const BAMBOO_BADREQUEST = 'bambooError400';
    const BAMBOO_UNAUTHORISED = 'bamboError403';
    const BAMBOO_NOTFOUND = 'bambooError404';
    const BAMBOO_METHODNOTALLOWED = 'bamboError405';
    const BAMBOO_SERVERERROR = 'bambooError500';
    const BAMBOO_CURLERROR = 'bambooErrorCurl';
    const BAMBOO_OTHER = 'bambooErrorOther';

    /* 
     * A proxy method for the counter
     */
    public static function increment($counterName) {
        self::$_countArray[$counterName] =+ 1;
    }

    public static function getCount($counterName) {
        $counter = constant("\Bamboo\CounterFake::$counterName");
        return self::$_countArray[$counter];
    }

    public static function resetCount($counterName) {
        $counter = constant("\Bamboo\CounterFake::$counterName");
        self::$_countArray[$counter] = 0;
    }
}