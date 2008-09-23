<?php

# $Id$

/**
 * OOP Framework module for Drupal
 * 
 * The base framework for developping object oriented Drupal modules
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
class oop extends Oop_Drupal_ModuleBase implements Oop_Drupal_MenuItem_Interface
{
    /**
     * The permissions array
     */
    protected $_perms = array(
        'access oop admin'
    );
    
    /**
     * Adds items to the Drupal menu
     * 
     * @param   array   An array in which to place the menu items, passed by reference. It may contains existing menu items, for instance if an administration settings form exists
     * @return  NULL
     */
     public function addMenuItems( array &$items )
     {}
}
