<?php

# $Id$

/**
 * Exception class for the Oop_Drupal_Database class
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Oop/Drupal/Database
 * @version         0.1
 */
class Oop_Drupal_Database_Exception extends Oop_Core_Exception_Base
{
    /**
     * Error codes for the exceptions
     */
    const EXCEPTION_NO_PDO        = 0x01;
    const EXCEPTION_NO_PDO_DRIVER = 0x02;
    const EXCEPTION_NO_CONNECTION = 0x03;
    const EXCEPTION_BAD_METHOD    = 0x04;
}
