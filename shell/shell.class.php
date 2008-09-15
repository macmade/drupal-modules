<?php

/**
 * Terminal module for Drupal
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
class shell extends Oop_Drupal_ModuleBase
{
    /**
     * Class version constants.
     * Holds the version, the developpment state
     * and the PHP lower compatible version.
     */
    const CLASS_VERSION  = '0.1';
    const DEVEL_STATE    = 'alpha';
    const PHP_COMPATIBLE = '5.2.0';
    
    /**
     * 
     */
    public function show( Oop_Xhtml_Tag $content )
    {
        $this->_includeModuleScript();
        $this->_includeModuleCss();
        $content->strong = __METHOD__;
    }
    
    /**
     * 
     */
    public function addMenuItems( array $items )
    {
        $items[ 'admin/shell' ] = array(
            'title'            => $this->_lang->menuTitle,
            'page callback'    => 'shell_show',
            'access arguments' => array('access administration pages'),
        );
        
        return $items;
    }
    
    /**
     * 
     */
    public function validateAdminForm( $form, &$formState )
    {
        $fontSize = $form[ '#post' ][ 'shell_font_size' ];
        
        if( !is_numeric( $fontSize ) ) {
            
            form_set_error( 'shell_font_size', $this->_lang->notNumeric );
        }
    }
}
