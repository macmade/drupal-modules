<?php

# $Id$

/**
 * Interface for the Drupal modules that add menu items
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Drupal
 * @version         0.1
 */
interface Oop_Drupal_MenuItem_Interface
{
    public function addMenuItems( array &$items );
}
