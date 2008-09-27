<?php

# $Id$

/**
 * Exception class for the Oop_Soap_Server class
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Oop/Lang/Getter
 * @version         0.1
 */
class Oop_Soap_Server_Exception extends Oop_Core_Exception_Base
{
    /**
     * Error codes for the exceptions
     */
    const EXCEPTION_NO_SOAP    = 0x01;
    const EXCEPTION_NO_INI_SET = 0x02;
}
