<?php

# $Id$

/**
 * Exception class for the Oop_Lang_Getter class
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Oop/Lang/Getter
 * @version         0.1
 */
class Oop_Lang_Getter_Exception extends Oop_Core_Exception_Base
{
    /**
     * Error codes for the exceptions
     */
    const EXCEPTION_NO_LANG_FILE = 0x01;
    const EXCEPTION_BAD_XML      = 0x02;
}
