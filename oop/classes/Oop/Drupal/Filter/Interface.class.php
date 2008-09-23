<?php

# $Id$

/**
 * Interface for the Drupal filter modules
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Drupal
 * @version         0.1
 */
interface Oop_Drupal_Filter_Interface
{
    public function prepareFilter( $delta, $format, $text );
    public function processFilter( $delta, $format, $text );
}
