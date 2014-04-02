<?php

namespace Bamboo\Feeds;

/**
 * A class representing a logger
 */
class Log
{
    const EMERG   = 0;  // Emergency: system is unusable
    const ALERT   = 1;  // Alert: action must be taken immediately
    const CRIT    = 2;  // Critical: critical conditions
    const ERR     = 3;  // Error: error conditions
    const WARN    = 4;  // Warning: warning conditions
    const NOTICE  = 5;  // Notice: normal but significant condition
    const INFO    = 6;  // Informational: informational messages
    const DEBUG   = 7;  // Debug: debug messages
    const DEFAULT_LOG_LEVEL = self::DEBUG; 

    protected static $_logger = null;

    /**
     * Set the logger to use
     */
    public static function setLogger($logger) {
        self::$_logger = $logger;
    }

    /**
     * Return the instance of the logger currently used
     */
    public static function getLogger() {
        return self::$_logger;
    }

    /**
     * Combine function arguments to form a message, sprintf-style
     *
     * @param  array $args Arguments in the form of an array
     * @return string      The sprintf'd result
     */
    public static function _getMessage($args) {
        $var = array_shift($args);
        return vsprintf($var, $args);
    }

    /**
     * Log a given message with a given level
     */
    protected static function _log($message, $level) {
        $logger = self::getLogger();
        if ($logger) {
            self::getLogger()->log($message, $level);
        }
    }

   /**
     * Log a given message, wrapping around sprintf
     * for readability
     */
    public static function log() {
        $message = self::_getMessage(func_get_args());
        self::_log($message, self::DEFAULT_LOG_LEVEL);
    }

    /**
     * Send an emergency message
     */
    public static function emerg() {
        $message = self::_getMessage(func_get_args());
        self::_log($message, self::EMERG);
    }

    /**
     * Send an alert message
     */
    public static function alert() {
        $message = self::_getMessage(func_get_args());
        self::_log($message, self::ALERT);
    }

    /**
     * Send an critical message
     */
    public static function crit() {
        $message = self::_getMessage(func_get_args());
        self::_log($message, self::CRIT);
    }

    /**
     * Send an error message
     */
    public static function err() {
        $message = self::_getMessage(func_get_args());
        self::_log($message, self::ERR);
    }

    /**
     * Send an warning message
     */
    public static function warn() {
        $message = self::_getMessage(func_get_args());
        self::_log($message, self::WARN);
    }

    /**
     * Send an notice message
     */
    public static function notice() {
        $message = self::_getMessage(func_get_args());
        self::_log($message, self::NOTICE);
    }

    /**
     * Send an informational message
     */
    public static function info() {
        $message = self::_getMessage(func_get_args());
        self::_log($message, self::INFO);
    }

    /**
     * Send an debug message
     */
    public static function debug() {
        $message = self::_getMessage(func_get_args());
        self::_log($message, self::DEBUG);
    }
}
