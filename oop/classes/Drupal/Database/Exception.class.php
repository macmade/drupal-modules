<?php

/**
 * Exception class for the Drupal_Database class
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Drupal/Exception
 * @version         0.1
 */
class Drupal_Database_Exception extends Exception_Base
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
    const EXCEPTION_NO_PDO        = 0x01;
    const EXCEPTION_NO_CONNECTION = 0x02;
    const EXCEPTION_BAD_METHOD    = 0x03;
}