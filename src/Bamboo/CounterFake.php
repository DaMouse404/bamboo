<?php

namespace Bamboo;

/**
 * A class representing a Fake Counter. 
 */
class CounterFake extends Counter
{
    private static $_countArray = array(
        'bambooErrorApigee500' => 0,
        'bambooErrorApigee404' => 0,
        'bambooErrorApigee500' => 0,
        'bambooErrorIbl500' => 0
    );

    const BAMBOO_APIGEE_BADREQUEST = 'bambooErrorApigee400';
    const BAMBOO_APIGEE_NOTFOUND = 'bambooErrorApigee404';
    const BAMBOO_APIGEE_SERVERERROR = 'bambooErrorApigee500';
    const BAMBOO_IBL_SERVERERROR = 'bambooErrorIbl500';

    /* 
     * A proxy method for the counter
     */
    public static function increment($counterName) {
        self::$_countArray[$counterName] =+ 1;
    }

    public static function getCount($counterName) {
        $realName = constant("\Bamboo\CounterFake::$counterName");
        return self::$_countArray[$realName];
    }

    public static function resetCount($counterName) {
        $realName = constant("\Bamboo\CounterFake::$counterName");
        self::$_countArray[$realName] = 0;
    }
}