<?php

# $Id$

/**
 * Exception class for the Oop_Soap_Wsdl_Generator class
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Oop/Lang/Getter
 * @version         0.1
 */
class Oop_Soap_Wsdl_Generator_Exception extends Oop_Core_Exception_Base
{
    /**
     * Error codes for the exceptions
     */
    const EXCEPTION_NO_REFLECTION = 0x01;
    const EXCEPTION_NO_XML_WRITER = 0x02;
}
