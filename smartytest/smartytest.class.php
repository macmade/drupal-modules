<?php

# $Id$

/**
 * Smarty test module for Drupal
 * 
 * A test module to demonstrate the use of Smarty in an OOP Drupal module
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
class smartytest extends Oop_Drupal_ModuleBase implements Oop_Drupal_Block_Interface
{
    /**
     * An array with the Drupal permission for the module
     */
    protected $_perms = array(
        'access smartytest block',
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
        $tmpl = $this->_getTemplate();
        $tmpl->assign( 'moduleName', __CLASS__ );
        $content->addTextData( $tmpl->fetch( 'main.tpl' ) );
    }
}
