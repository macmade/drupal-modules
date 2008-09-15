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
class helloworld extends Oop_Drupal_ModuleBase
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
     * Gets the block view
     * 
     * @param   Oop_Xhtml_Tag   The placeholder for the module content
     * @param   int             The delta offset, used to generate different contents for different blocks
     * @return  NULL
     */
    public function getBlock( Oop_Xhtml_Tag $content, $delta )
    {
        // Adds the hello world message
        $content->div->strong = $this->_lang->hello;
        
        // Adds a divider
        $content->spacer( 20 );
        $content->hr;
        $content->spacer( 20 );
        
        // Adds the name of this method
        $content->div = sprintf( $this->_lang->method, $this->_modName . '::' . __FUNCTION__ );
        
        // Adds a divider
        $content->spacer( 20 );
        $content->hr;
        $content->spacer( 20 );
        
        // Crypts an email
        $content->div = sprintf(
            $this->_lang->cryptEmail,
            $this->_email( 'macmade@eosgarden.com' )
        );
        
        // Adds a divider
        $content->spacer( 20 );
        $content->hr;
        $content->spacer( 20 );
        
        // Modules container
        $content->comment( 'Start of the module list' );
        $modulesBlock                = $content->div;
        $content->comment( 'End of the module list' );
        
        // Backtrace containers
        $content->comment( 'Start of the backtrace' );
        $backTraceBlock              = $content->div;
        $content->comment( 'End of the backtrace' );
        
        // CSS class for the containers
        $this->_cssClass( $modulesBlock, 'modules' );
        $this->_cssClass( $backTraceBlock, 'backtrace' );
        
        // Adds the titles for the containers
        $modulesBlock->div->strong   = $this->_lang->modules;
        $backTraceBlock->div->strong = $this->_lang->backTrace;
        
        // Adds spacers
        $modulesBlock->spacer( 20 );
        $backTraceBlock->spacer( 20 );
        
        // Gets the backtrace
        $backTrace = debug_backtrace( $this );
        
        // Gets the info icon
        $infoIcon  = $this->_getIcon( 'information.png' );
        
        // Parameters for the PDO query
        $sqlParams = array(
            ':type'   => 'module',
            ':status' => 1
        );
        
        // SQL query
        $sql = 'SELECT *
                FROM {system}
                WHERE type = :type
                    AND status = :status
                ORDER BY name';
        
        // Prepares the PDO query
        $query       = self::$_db->prepare( $sql );
        
        // Executes the PDO query
        $query->execute( $sqlParams );
        
        // Includes the module script file
        $this->_includeModuleScript();
        
        // Includes the module CSS file
        $this->_includeModuleCSS();
        
        // Process each module
        while( $module = $query->fetchObject() ) {
            
            // Path of the INI file
            $iniFile              = self::$_classManager->getModulePath( $module->name )
                                  . $module->name
                                  . '.info';
            
            // Informations in the INI file
            $iniInfos             = parse_ini_file( $iniFile );
            
            // Create new divs
            $moduleDiv            = $modulesBlock->div;
            $infosDiv             = $modulesBlock->div;
            
            // Adds the attributes to the info div
            $infosDiv[ 'id' ]     = $this->_modName . '-' . $module->name;
            $infosDiv[ 'style' ]  = 'display: none;';
            
            // Adds the CSS class
            $this->_cssClass( $infosDiv, 'infos' );
            
            // Creates a new link
            $moduleLink           = $moduleDiv->strong->a;
            
            // Adds the href attribute
            $moduleLink[ 'title' ] = $iniInfos[ 'description' ];
            $moduleLink[ 'href' ]  = 'javascript:'
                                   . $this->_modName
                                   . '.display( \''
                                   . $this->_modName
                                   . '-'
                                   . $module->name
                                   . '\' );';
            
            // Adds the info icon
            $moduleLink->addChildNode( $infoIcon );
            
            // Adds the module name
            $moduleLink->addTextData( ' ' . $iniInfos[ 'name' ] . ' (' . $module->name . ')' );
            
            // Adds the description
            $infosDiv->div = sprintf( $this->_lang->description, $iniInfos[ 'description' ] );
            
            // Adds the module file name
            $infosDiv->div = sprintf( $this->_lang->fileName, $module->filename );
        }
        
        // Process the backtrace
        foreach( $backTrace as $key => $value ) {
            
            // Name of the function/method name
            $funcName            = ( isset( $value[ 'class' ] ) ) ? $value[ 'class' ] . '::' . $value[ 'function' ] . '()' : $value[ 'function' ] . '()';
            
            // Create new divs
            $funcDiv             = $backTraceBlock->div;
            $infosDiv            = $backTraceBlock->div;
            
            // Adds the attributes to the info div
            $infosDiv[ 'id' ]    = $this->_modName . '-backtrace-' . $key;
            $infosDiv[ 'style' ] = 'display: none;';
            
            // Adds the CSS class
            $this->_cssClass( $infosDiv, 'infos' );
            
            // Creates a new link
            $funcLink            = $funcDiv->strong->a;
            
            // Adds the href attribute
            $funcLink[ 'title' ] = $funcName;
            $funcLink[ 'href' ]  = 'javascript:'
                                   . $this->_modName
                                   . '.display( \''
                                   . $this->_modName
                                   . '-backtrace-'
                                   . $key
                                   . '\' );';
            
            // Adds the info icon
            $funcLink->addChildNode( $infoIcon );
            
            // Adds the module name
            $funcLink->addTextData( ' ' . $funcName );
            
            // Adds the file name
            $infosDiv->div = sprintf( $this->_lang->fileName, $value[ 'file' ] );
            
            // Adds the line number
            $infosDiv->div = sprintf( $this->_lang->line, $value[ 'line' ] );
        }
    }
}
