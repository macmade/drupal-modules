<?php

# $Id$

/**
 * Exception class for the Oop_Drupal_Template class
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Oop/Drupal/Template
 * @version         0.1
 */
class Oop_Drupal_Template_Exception extends Oop_Core_Exception_Base
{
    /**
     * Error codes for the exceptions
     */
    const EXCEPTION_NO_DIRECTORY            = 0x01;
    const EXCEPTION_DIRECTORY_NOT_WRITEABLE = 0x02;
    const EXCEPTION_NO_SMARTY_CLASS_FILE    = 0x03;
    const EXCEPTION_NO_SMARTY_CLASS         = 0x04;
    const EXCEPTION_BAD_METHOD              = 0x05;
}
