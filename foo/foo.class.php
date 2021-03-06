<?php

# $Id$

/**
 * Foo module for Drupal
 * 
 * A Drupal test module
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
class foo extends Oop_Drupal_ModuleBase implements Oop_Drupal_Block_Interface
{
    /**
     * Permissions array
     */
    protected $_perms    = array(
        'access foo block'
    );
    
    /**
     * Gets the block view
     * 
     * @param   Oop_Xhtml_Tag   The placeholder for the module content
     * @param   int             The delta offset, used to generate different contents for different blocks
     * @return  NULL
     */
    public function getBlock( Oop_Xhtml_Tag $content, $delta )
    {
        // Adds the hello world message
        $content->div = $this->_lang->hello;
    }
}
