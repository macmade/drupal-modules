<?php

# $Id$

/**
 * Exception class for the Oop_Aop_Advisor class
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Oop/Aop/Advisor
 * @version         0.1
 */
class Oop_Aop_Advisor_Exception extends Exception
{
    /**
     * Error codes for the exceptions
     */
    const EXCEPTION_NO_JOINPOINT              = 0x01;
    const EXCEPTION_NO_JOINPOINT_METHOD       = 0x02;
    const EXCEPTION_INVALID_ADVICE_CALLBACK   = 0x03;
    const EXCEPTION_INVALID_ADVICE_TYPE       = 0x04;
    const EXCEPTION_JOINPOINT_EXISTS          = 0x05;
    const EXCEPTION_ADVICE_TYPE_NOT_PERMITTED = 0x06;
}
