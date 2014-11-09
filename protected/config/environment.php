<?php

/**
 * This class helps to config Yii application environment.
 * @name Environment
 * @author Syed Asfaquz Zaman | <szaman@souq.com>
 */
class Environment {

    const DEVELOPMENT = 1;
    const STAGING = 2;
    const PRODUCTION = 3;

    private $_mode = 0;
    private $_env;
    private $_debug;
    private $_trace_level;
    private $_config;

    /**
     * Returns the debug mode
     * @return Bool
     */
    public function getDebug() {
        return $this->_debug;
    }

    /**
     * Returns the trace level for YII_TRACE_LEVEL
     * @return int
     */
    public function getTraceLevel() {
        return $this->_trace_level;
    }

    /**
     * Returns the configuration array depending on the mode
     * you choose
     * @return array
     */
    public function getConfig() {
        return $this->_config;
    }

    /**
     * Initilizes the Environment class with the given mode
     * @param constant $mode
     * @param bool $cron
     */
    public function __construct($mode) {
        $this->_mode = $mode;
        switch ($this->_mode):
            case self::DEVELOPMENT:
                $this->_env = "development";
                break;
            case self::STAGING:
                $this->_env = "staging";
                break;
            case self::PRODUCTION:
                $this->_env = "production";
                break;
        endswitch;
        $this->setConfig();
    }

    /**
     * Sets the configuration for the choosen environment
     */
    private function setConfig() {
        switch ($this->_mode) {
            case self::DEVELOPMENT:
                $this->_debug = TRUE;
                $this->_trace_level = 3;
                $this->_config = array_merge_recursive($this->_main(), $this->_dev());
                break;
            case self::STAGING:
                $this->_debug = TRUE;
                $this->_trace_level = 0;
                $this->_config = array_merge_recursive($this->_main(), $this->_stg());
                break;
            case self::PRODUCTION:
                $this->_debug = FALSE;
                $this->_trace_level = 0;
                $this->_config = array_merge_recursive($this->_main(), $this->_production());
                break;
            default:
                die('[1] No application environment selected!');
        }
    }

    /**
     * Main configuration
     * This is the general configuration that uses all environments
     * @return array
     */
    private function _main() {
        return $configMain = include dirname(__FILE__) . '/main.php';
    }

    /**
     * Development configuration
     * Usage:
     * - Show all details on each error.
     * - Gii module enabled
     * @return array
     */
    private function _dev() {
        return $configDevelopment = include dirname(__FILE__) . '/environment/' . strtolower($this->_env) . '.php';
    }

    /**
     * Integration configuration
     * Usage:
     * - Show all details on each error.
     * @return array
     */
    private function _stg() {
        return $configStaging = include dirname(__FILE__) . '/environment/' . strtolower($this->_env) . '.php';
    }

    /**
     * Production configuration
     * Usage:
     * - online website
     * - Production DB
     * @return array
     */
    private function _production() {
        return $configProduction = include dirname(__FILE__) . '/environment/' . strtolower($this->_env) . '.php';
    }

}