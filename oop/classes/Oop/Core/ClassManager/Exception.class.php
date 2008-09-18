<?php

/**
 * Exception class for the Oop_Core_ClassManager class
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Oop/Core/ClassManager
 * @version         0.1
 */
class Oop_Core_ClassManager_Exception extends Oop_Core_Exception_Base
{
    /**
     * Error codes for the exceptions
     */
    const EXCEPTION_MODULE_NOT_LOADED    = 0x01;
    const EXCEPTION_NO_MODULE_CLASS_FILE = 0x02;
    const EXCEPTION_NO_MODULE_CLASS      = 0x03;
}
