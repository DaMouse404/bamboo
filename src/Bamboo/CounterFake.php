<?php

namespace Bamboo;

/**
 * A class representing a Fake Counter.
 */
class CounterFake extends Counter
{
    private static $_countArray = array(
        'bambooErrorProxy400' => 0,
        'bambooErrorProxy404' => 0,
        'bambooErrorProxy500' => 0,
        'bambooErrorIbl500' => 0,
        'bambooErrorProxyCurl' => 0,
        'bambooErrorProxyOther' => 0
    );

    const BAMBOO_PROXY_BADREQUEST = 'bambooErrorProxy400';
    const BAMBOO_PROXY_NOTFOUND = 'bambooErrorProxy404';
    const BAMBOO_PROXY_OTHER = 'bambooErrorProxyOther';
    const BAMBOO_PROXY_SERVERERROR = 'bambooErrorProxy500';
    const BAMBOO_IBL_SERVERERROR = 'bambooErrorIbl500';
    const BAMBOO_PROXY_CURLERROR = 'bambooErrorProxyCurl';

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
