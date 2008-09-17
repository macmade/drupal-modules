<?php

/**
 * OOP Framework module for Drupal
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
class oop extends Oop_Drupal_ModuleBase
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
        $this->_includeModuleCss();
        
        $modulesDir = self::$_classManager->getDrupalPath()
                    . 'sites'
                    . DIRECTORY_SEPARATOR
                    . 'all'
                    . DIRECTORY_SEPARATOR
                    . 'modules';
        
        if( !file_exists( $modulesDir ) && !is_dir( $modulesDir ) ) {
            
            $error = $content->strong;
            $error->addTextData( sprintf( $this->_lang->errorNoDir, $modulesDir ) );
            $this->_cssClass( $error, 'error' );
            
        } elseif( !is_writeable( $modulesDir ) ) {
            
            $error = $content->strong;
            $error->addTextData( sprintf( $this->_lang->errorDirNotWriteable, $modulesDir ) );
            $this->_cssClass( $error, 'error' );
            
        } else {
            
            $content->div = drupal_get_form( 'oop_getModuleFormConf' );
        }
    }
    
    /**
     * 
     */
    public function getModuleFormConf()
    {
        $confPath = self::$_classManager->getModulePath( $this->_modName )
                  . 'settings'
                  . DIRECTORY_SEPARATOR
                  . 'kickstarter.form.php';
                      
        $form = new Oop_Drupal_Form_Builder( $confPath, $this->_modName, $this->_lang );
        
        return $form->getConf();
    }
    
    /**
     * 
     */
    public function addMenuItems( array $items )
    {
        $items[ 'admin/build/oopmodule' ] = array(
            'title'            => $this->_lang->getLabel( 'admin_build_oopmodule_title', 'system' ),
            'description'      => $this->_lang->getLabel( 'admin_build_oopmodule_description', 'system' ),
            'page callback'    => 'oop_show',
            'access arguments' => array('access administration pages'),
        );
        
        return $items;
    }
}
