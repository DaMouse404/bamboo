<?php

namespace Bamboo;

use Bamboo\Log;
use Bamboo\Counter;
use Guzzle\Plugin\Cache\CachePlugin;

/*
 * Configuration Responsibility:
 * - Holds values which are specific to the client that are used by Bamboo objects
 */
class Configuration
{
    /**
     * URL prepended to all feeds for the service. Contains provider name and version.
     * @var string
     */
    private static $_baseUrl = '';
    /**
     *
     * @var CacheInterface
     */
    private static $_cache = false;
    /**
     * Used to set api_key and any other important feed info which is later merged over $_defaultParams.
     * @var array
     */
    private static $_config = array();
    /**
     * The HTTP Client to use to to do all the above except for error and fail states/responses.
     * @var object
     */
    private static $_failHttpClient;
    /**
     * The HTTP Client to use for normal unit tests, cukes and reading fixtures.
     * @var object
     */
    private static $_fakeHttpClient;
    /**
     * Hostname used for the request.
     * @var string
     */
    private static $_host = '';
    /**
     * Used to set custom image host
     * @var string
     */
    private static $_imageHost = false;
    /**
     * The URL of the image to be used when no images are returned by iBL.
     * @var string
     */
    private static $_placeholderImageUrl = '';
    /**
     * Tells the HTTP Client what proxy it must route traffic through if necessary.
     * @var string
     */
    private static $_networkProxy = '';
    /**
    * Store list of partial feed names to fake with the respective fixture file name
    * @var array
    */
    private static $_fakeRequests = array();
    /**
    * Store list of partial feed names to fail with the respective fixture file name
    * @var array
    */
    private static $_failRequests = array();


    public static function setBaseUrl($baseUrl) {
        self::$_baseUrl = $baseUrl;
    }

    /**
     *  Binds the cacheadapter to the cache as a plugin to Guzzle
     */
    public static function setCache($cache) {
        if (!!$cache) {
            self::$_cache = new CachePlugin(
                array(
                    'storage' => new CacheStorage($cache)
                )
            );
        } else {
            self::$_cache = false;
        }
    }

    public static function setConfig($config) {
        self::$_config = $config;
    }

    public static function setCounter($counter) {
       Counter::setCounter($counter);
    }

    public static function setHost($host) {
        self::$_host = $host;
    }

    public static function setImageHost($imageHost) {
        self::$_imageHost = $imageHost;
    }

    public static function setFakeHttpClient($fakeHttpClient) {
        self::$_fakeHttpClient = $fakeHttpClient;
    }

    public static function setFailHttpClient($failHttpClient) {
        self::$_failHttpClient = $failHttpClient;
    }

    public static function setLogger($logger) {
       Log::setLogger($logger);
    }

    public static function setPlaceholderImageUrl($placeholderImageUrl) {
        self::$_placeholderImageUrl = $placeholderImageUrl;
    }

    public static function setNetworkProxy($proxy) {
        self::$_networkProxy = $proxy;
    }

    public static function getBaseUrl() {
        return self::$_baseUrl;
    }

    public static function getCache() {
        return self::$_cache;
    }

    public static function getCounter() {
        return Counter::getCounter();
    }

    public static function getConfig() {
        return self::$_config;
    }

    public static function getHost() {
        return self::$_host;
    }

    public static function getImageHost() {
        return self::$_imageHost;
    }

    public static function getFakeHttpClient() {
        return self::$_fakeHttpClient;
    }

    public static function getFailHttpClient() {
        return self::$_failHttpClient;
    }

    public static function getPlaceholderImageUrl() {
        return self::$_placeholderImageUrl;
    }

    public static function getNetworkProxy() {
        return self::$_networkProxy;
    }

    public static function addFakeRequest($feedMatcher, $fileName) {
        self::$_fakeRequests[$feedMatcher] = $fileName;
    }

    public static function clearFakeRequests() {
        self::$_fakeRequests = array();
    }

    public static function getFakeRequests() {
        return self::$_fakeRequests;
    }

    public static function addFailRequest($feedMatcher, $fileName = '') {
        self::$_failRequests[$feedMatcher] = $fileName;
    }

    public static function clearFailRequests() {
        self::$_failRequests = array();
    }

    public static function getFailRequests() {
        return self::$_failRequests;
    }
}
