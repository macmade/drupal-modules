<?php

/**
 * Hello World module for Drupal
 * 
 * This module is a demonstration for the Drupal OOP framework.
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
class helloworld extends Drupal_ModuleBase
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
     * Gets the 'view' section of the module
     * 
     * @param   Html_Tag    The placeholder for the module content
     * @param   int         The delta offset, used to generate different contents for different blocks
     * @return  NULL
     */
    protected function _getView( Html_Tag $content, $delta )
    {
        // Adds the hello world message
        $firstBlock = $content->div->strong;
        $firstBlock->addTextData( $this->_lang->hello );
        
        // Adds a divider
        $content->spacer( 20 );
        $content->hr;
        $content->spacer( 20 );
        
        // Adds the name of this method
        $content->div = sprintf( $this->_lang->method, __METHOD__ );
        
        // Adds a divider
        $content->spacer( 20 );
        $content->hr;
        $content->spacer( 20 );
        
        // Adds the title for the module section
        $content->div->strong = $this->_lang->modules;
        
        // Adds a spacer
        $content->spacer( 20 );
        
        // Starts an HTML comment
        $content->comment( 'Start of the module list' );
        
        // Creates a new div
        $modulesBlock = $content->div;
        
        // Parameters for the PDO query
        $sqlParams = array(
            ':type' => 'module'
        );
        
        // Prepares the PDO query
        $sql       = self::$_db->prepare( 'SELECT * from system WHERE type = :type' );
        
        // Executes the PDO query
        $sql->execute( $sqlParams );
        
        // Fetches all the Drupal modules
        $modules = $sql->fetchAll();
        
        // Includes the Scriptaculous JS framework
        $this->_includeScriptaculous();
        
        // Includes the module script file
        $this->_includeModuleScript();
        
        // Includes the module CSS file
        $this->_includeModuleCSS();
        
        // Process each module
        foreach( $modules as $module ) {
            
            // Create new divs
            $moduleDiv            = $modulesBlock->div;
            $infosDiv             = $modulesBlock->div;
            $loadedDiv            = $infosDiv->div;
            $fileNameDiv          = $infosDiv->div;
            
            // Adds the attributes to the info div
            $infosDiv[ 'id' ]     = $this->_modName . '-' . $module[ 'name' ];
            $infosDiv[ 'style' ]  = 'display: none;';
            
            // Creates a new link
            $moduleLink           = $moduleDiv->strong->a;
            
            // Adds the href attribute
            $moduleLink[ 'href' ] = 'javascript:'
                                  . $this->_modName
                                  . '.display( \''
                                  . $this->_modName
                                  . '-'
                                  . $module[ 'name' ]
                                  . '\' );';
            
            // Adds the module name
            $moduleLink->addTextData( $module[ 'name' ] );
            
            // Gets the load state
            $loadState = ( $module[ 'status' ] == 1 ) ? $this->_lang->yes : $this->_lang->no;
            
            // Adds the loaded state
            $loadedDiv->addTextData( sprintf( $this->_lang->loaded, $loadState ) );
            
            // Adds the module file name
            $fileNameDiv->addTextData( sprintf( $this->_lang->fileName, $module[ 'filename' ] ) );
        }
        
        // Ends the HTML comment
        $content->comment( 'End of the module list' );
    }
}
