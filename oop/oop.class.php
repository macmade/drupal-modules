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
    protected $_files = array();
    
    /**
     * 
     */
    protected function _createInfoFile( $path, $name, $title, $description, $package, $drupalVersion, $phpVersion, $dependencies )
    {
        // Path to the .info file
        $path                    = $path . DIRECTORY_SEPARATOR . $name . '.info';
        
        // Storage array
        $this->_files[ $path ]   = array();
        
        // Gets the dependencies, if any
        $deps                    = explode( ',', str_replace( ' ', '', $dependencies ) );
        
        // Adds a dependency to the OOP module
        array_unshift( $deps, 'oop' );
        
        // Creates the required lines
        $this->_files[ $path ][] = 'name = ' . $title;
        $this->_files[ $path ][] = 'description = ' . $description;
        $this->_files[ $path ][] = 'core = ' . $drupalVersion;
        $this->_files[ $path ][] = 'php = ' . $phpVersion;
        
        // Checks for a package
        if( $package ) {
            
            // Adds the package informations
            $this->_files[ $path ][] = 'package = ' . $package;
        }
        
        // Process each dependency
        foreach( $deps as $dep ) {
            
            // Adds the dependency line
            $this->_files[ $path ][] = 'dependencies[] = ' . $dep;
        }
        
        // Adds a last line for compatibility
        $this->_files[ $path ][] = '';
    }
    
    /**
     * 
     */
    protected function _createInstallFile( $path, $name )
    {
        // Path to the .install file
        $path                    = $path . DIRECTORY_SEPARATOR . $name . '.install';
        
        // Storage array
        $this->_files[ $path ]   = array();
        
        // Creates the required lines
        $this->_files[ $path ][] = '<?php';
        $this->_files[ $path ][] = '';
        $this->_files[ $path ][] = 'function ' . $name . '_install()';
        $this->_files[ $path ][] = '{';
        $this->_files[ $path ][] = '    $oopWeight = (int)db_result( db_query( "SELECT weight FROM {system} WHERE name = \'oop\'" ) );';
        $this->_files[ $path ][] = '    db_query( "UPDATE {system} SET weight = %d WHERE name = \'' . $name . '\'", $oopWeight + 1 );';
        $this->_files[ $path ][] = '}';
        $this->_files[ $path ][] = '';
        $this->_files[ $path ][] = 'function ' . $name . '_uninstall()';
        $this->_files[ $path ][] = '{';
        $this->_files[ $path ][] = '    Oop_Drupal_Utils::getInstance()->deleteModuleVariables( \'' . $name . '\' );';
        $this->_files[ $path ][] = '}';
        $this->_files[ $path ][] = '';
    }
    
    /**
     * 
     */
    public function show( Oop_Xhtml_Tag $content )
    {
        // Includes the CSS for the module
        $this->_includeModuleCss();
        
        // Path to the Drupal modules directory
        $modulesDir = self::$_classManager->getDrupalPath()
                    . 'sites'
                    . DIRECTORY_SEPARATOR
                    . 'all'
                    . DIRECTORY_SEPARATOR
                    . 'modules';
        
        // Checks if the modules directory exists and is writeables
        if( !file_exists( $modulesDir ) && !is_dir( $modulesDir ) ) {
            
            // Error - The modules directory does not exist
            $error = $content->strong;
            $error->addTextData( sprintf( $this->_lang->errorNoDir, $modulesDir ) );
            $this->_cssClass( $error, 'error' );
            
        } elseif( !is_writeable( $modulesDir ) ) {
            
            // Error - The modules directory is not writeable
            $error = $content->strong;
            $error->addTextData( sprintf( $this->_lang->errorDirNotWriteable, $modulesDir ) );
            $this->_cssClass( $error, 'error' );
            
        } else {
            
            // Creates the kickstarter form
            $content->div = drupal_get_form( 'oop_kickstarterForm' );
        }
    }
    
    /**
     * 
     */
    public function getKickstarterForm()
    {
        // Path to the form configuration file
        $confPath = self::$_classManager->getModulePath( $this->_modName )
                  . 'settings'
                  . DIRECTORY_SEPARATOR
                  . 'kickstarter.form.php';
        
        // Creates the final form configuration
        $form = new Oop_Drupal_Form_Builder( $confPath, $this->_modName, $this->_lang );
        
        // Returns the final form configuration array
        return $form->getConf();
    }
    
    /**
     * 
     */
    public function validateKickstarterForm( $form, &$formState )
    {
        // Checks the module name
        if( !preg_match( '/^[a-z_]+$/', $form[ '#post' ][ 'oop_infos_name' ] ) ) {
            
            // Error - Invalid module name
            form_set_error( 'oop_infos_name', $this->_lang->invalidModuleName );
        }
        
        // Checks the PHP version
        if( ( int )$form[ '#post' ][ 'oop_dependencies_version_php' ] < 5 ) {
            
            // Error - PHP version cannot be under 5
            form_set_error( 'oop_dependencies_version_php', $this->_lang->phpVersionTooOld );
        }
    }
    
    /**
     * 
     */
    public function submitKickstarterForm( $formId, $formValues )
    {
        // Gets the submitted values
        $values =& $formValues[ 'values' ];
        
        // Path to the module directory
        $moduleDir = self::$_classManager->getDrupalPath()
                   . 'sites'
                   . DIRECTORY_SEPARATOR
                   . 'all'
                   . DIRECTORY_SEPARATOR
                   . 'modules'
                   . DIRECTORY_SEPARATOR
                   . $values[ 'oop_infos_name' ];
        
        // Tries to create the directory
        if( mkdir( $moduleDir ) ) {
            
            // Creates the .info file
            $this->_createInfoFile(
                $moduleDir,
                $values[ 'oop_infos_name' ],
                $values[ 'oop_infos_title' ],
                $values[ 'oop_infos_description' ],
                $values[ 'oop_infos_package' ],
                $values[ 'oop_dependencies_version_core' ],
                $values[ 'oop_dependencies_version_php' ],
                $values[ 'oop_dependencies_dependencies' ]
            );
            
            // Creates the .install file
            $this->_createInstallFile(
                $moduleDir,
                $values[ 'oop_infos_name' ]
            );
            
            // Creates the .module file
            #$this->_createModuleFile(
            #    $moduleDir,
            #    $values[ 'oop_infos_name' ]
            #);
            
            // Creates the class file
            #$this->_createClassFile(
            #    $moduleDir,
            #    $values[ 'oop_infos_name' ]
            #);
            
            // Creates the lang file
            #$this->_createLangFile(
            #    $moduleDir,
            #    $values[ 'oop_infos_name' ]
            #);
            
            // Creates the settings file(s)
            #$this->_createSettingsFiles(
            #    $moduleDir,
            #    $values[ 'oop_infos_name' ]
            #);
            
            // Process each file
            foreach( $this->_files as $path => &$lines ) {
                
                // Tries to write the file
                if( !file_put_contents( $path, implode( self::$_NL, $lines ) ) ) {
                    
                    // Error - impossible to write the current file
                    drupal_set_message( sprintf( $this->_lang->cannotCreateFile, $path ) );
                    break;
                }
            }
            
        } else {
            
            // Error- Cannot create the module directory
            drupal_set_message( sprintf( $this->_lang->cannotCreateDir, $moduleDir ) );
        }
    }
    
    /**
     * 
     */
    public function addMenuItems( array $items )
    {
        // Adds the menu item for the kickstarter in the admin pages
        $items[ 'admin/build/oopmodule' ] = array(
            'title'            => $this->_lang->getLabel( 'admin_build_oopmodule_title', 'system' ),
            'description'      => $this->_lang->getLabel( 'admin_build_oopmodule_description', 'system' ),
            'page callback'    => 'oop_show',
            'access arguments' => array('access administration pages'),
        );
        
        // Returns the menu items
        return $items;
    }
}
