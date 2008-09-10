<?php

/**
 * Exception class for the ClassManager class
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         ClassManager
 * @version         0.1
 */
class ClassManager_Exception extends Exception_Base
{
    /**
     * Class version constants.
     * Holds the version, the developpment state
     * and the PHP lower compatible version.
     */
    const CLASS_VERSION  = '0.1';
    const DEVEL_STATE    = 'alpha';
    const PHP_COMPATIBLE = '5.2.0';
    
    /**
     * Error codes for the exceptions
     */
    const EXCEPTION_MODULE_NOT_LOADED    = 0x01;
    const EXCEPTION_NO_MODULE_CLASS_FILE = 0x02;
    const EXCEPTION_NO_MODULE_CLASS      = 0x03;
    const EXCEPTION_NO_PHP_VERSION       = 0x04;
    const EXCEPTION_PHP_VERSION_TOO_OLD  = 0x05;
}
