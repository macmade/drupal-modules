<?php

/**
 * Exception class for the Oop_Drupal_Page_Processor class
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Oop/Drupal/Exception
 * @version         0.1
 */
class Oop_Drupal_Page_Processor_Exception extends Oop_Core_Exception_Base
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
    const EXCEPTION_NO_PAGE   = 0x01;
    const EXCEPTION_NO_ROUTER = 0x02;
}
