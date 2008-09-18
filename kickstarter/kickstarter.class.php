<?php

/**
 * OOP kickstarter module for Drupal
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
class kickstarter extends Oop_Drupal_ModuleBase
{
    /**
     * 
     */
    protected $_files             = array();
    
    /**
     * 
     */
    protected $_formValues        = array();
    
    /**
     * 
     */
    protected $_moduleName        = '';
    
    /**
     * 
     */
    protected $_moduleDir         = '';
    
    /**
     * 
     */
    protected $_moduleLangDir     = '';
    
    /**
     * 
     */
    protected $_moduleSettingsDir = '';
    
    /**
     * 
     */
    protected function _createDirs()
    {
        // Tries to create the module directory
        if( !mkdir( $this->_moduleDir ) ) {
            
            // Error- Cannot create the module directory
            drupal_set_message( sprintf( $this->_lang->cannotCreateDir, $this->_moduleDir ) );
            
            return false;
        }
        
        // Tries to create the lang directory
        if( !mkdir( $this->_moduleLangDir ) ) {
            
            // Error- Cannot create the lang directory
            drupal_set_message( sprintf( $this->_lang->cannotCreateDir, $this->_moduleLangDir ) );
            
            return false;
        }
        
        // Checks if the settings directory is needed
        if( $this->_formValues[ 'kickstarter_block_add_config' ]
            || $this->_formValues[ 'kickstarter_node_add' ]
            || $this->_formValues[ 'kickstarter_admin_add' ]
        ) {
            
            // Tries to create the settings directory
            if( !mkdir( $this->_moduleSettingsDir ) ) {
                
                // Error- Cannot create the settings directory
                drupal_set_message( sprintf( $this->_lang->cannotCreateDir, $this->_moduleSettingsDir ) );
                
                return false;
            }
        }
        
        // All directories have been created successfully
        return true;
    }
    
    /**
     * 
     */
    protected function _createInfoFile()
    {
        // Path to the .info file
        $path                    = $this->_moduleDir
                                 . DIRECTORY_SEPARATOR
                                 . $this->_moduleName
                                 . '.info';
        
        // Storage array
        $this->_files[ $path ]   = array();
        
        // Gets the dependencies, if any
        $deps                    = explode(
            ',',
            str_replace(
                ' ',
                '',
                $this->_formValues[ 'kickstarter_dependencies_dependencies' ]
            )
        );
        
        // Checks the dependencies array
        if( $deps[ 0 ] === '' ) {
            
            // Removes the empty dependecy
            array_pop( $deps );
        }
        
        // Adds a dependency to the OOP module
        array_unshift( $deps, 'oop' );
        
        // Creates the required lines
        $this->_files[ $path ][] = 'name = ' . $this->_formValues[ 'kickstarter_infos_title' ];
        $this->_files[ $path ][] = 'description = ' . $this->_formValues[ 'kickstarter_infos_description' ];
        $this->_files[ $path ][] = 'core = ' . $this->_formValues[ 'kickstarter_dependencies_version_core' ];
        $this->_files[ $path ][] = 'php = ' . $this->_formValues[ 'kickstarter_dependencies_version_php' ];
        
        // Checks for a package
        if( $this->_formValues[ 'kickstarter_infos_package' ] ) {
            
            // Adds the package informations
            $this->_files[ $path ][] = 'package = ' . $this->_formValues[ 'kickstarter_infos_package' ];
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
    protected function _createInstallFile()
    {
        // Path to the .install file
        $path                    = $this->_moduleDir
                                 . DIRECTORY_SEPARATOR
                                 . $this->_moduleName
                                 . '.install';
        
        // Storage array
        $this->_files[ $path ]   = array();
        
        // Creates the required lines
        $this->_files[ $path ][] = '<?php';
        $this->_files[ $path ][] = '';
        $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_install()';
        $this->_files[ $path ][] = '{';
        $this->_files[ $path ][] = '    $oopWeight = (int)db_result( db_query( "SELECT weight FROM {system} WHERE name = \'oop\'" ) );';
        $this->_files[ $path ][] = '    db_query( "UPDATE {system} SET weight = %d WHERE name = \'' . $this->_moduleName . '\'", $oopWeight + 1 );';
        $this->_files[ $path ][] = '}';
        $this->_files[ $path ][] = '';
        $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_uninstall()';
        $this->_files[ $path ][] = '{';
        $this->_files[ $path ][] = '    Oop_Drupal_Utils::getInstance()->deleteModuleVariables( \'' . $this->_moduleName . '\' );';
        $this->_files[ $path ][] = '}';
        $this->_files[ $path ][] = '';
    }
    
    /**
     * 
     */
    protected function _createModuleFile()
    {
        // Path to the .module file
        $path                    = $this->_moduleDir
                                 . DIRECTORY_SEPARATOR
                                 . $this->_moduleName
                                 . '.module';
        
        // Storage array
        $this->_files[ $path ]   = array();
        
        // Starts the file
        $this->_files[ $path ][] = '<?php';
        $this->_files[ $path ][] = '';
        
        // Creates the help hook
        $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_help( $path, $arg )';
        $this->_files[ $path ][] = '{';
        $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->help( $path, $arg );';
        $this->_files[ $path ][] = '}';
        $this->_files[ $path ][] = '';
        
        // Checks if a block content must be added
        if( $this->_formValues[ 'kickstarter_block_add' ] ) {
            
            // Creates the perm hook
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_perm()';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->perm();';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
            
            // Creates the block hook
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_block( $op = \'list\', $delta = 0, array $edit = array() ) )';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->block( $op, $delta, $edit );';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
        }
        
        // Checks if a node content must be added
        if( $this->_formValues[ 'kickstarter_node_add' ] ) {
            
            // Checks if a block has already been added
            if( !$this->_formValues[ 'kickstarter_block_add' ] ) {
                
                // Creates the perm hook
                $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_perm()';
                $this->_files[ $path ][] = '{';
                $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->perm();';
                $this->_files[ $path ][] = '}';
                $this->_files[ $path ][] = '';
            }
            
            // Adds the access hook
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_access( $op, $node )';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->access( $op, $node );';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
            
            // Adds the form hook
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_form( stdClass $node )';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->form( $node );';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
            
            // Adds the node_info hook
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_node_info()';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->node_info();';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
            
            // Adds the view hook
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_view( stdClass $node, $teaser = false, $page = false )';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->view( $node, $teaser, $page );';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
            
            // Adds the insert hook
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_insert( stdClass $node )';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->insert( $node );';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
            
            // Adds the update hook
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_update( stdClass $node )';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->update( $node );';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
        }
        
        // Checks if an administration and/or a menu item page must be added
        if( $this->_formValues[ 'kickstarter_admin_add' ] && $this->_formValues[ 'kickstarter_menu_add' ] ) {
            
            // Adds the menu hook
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_menu()';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    $module = Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' );';
            $this->_files[ $path ][] = '    return $module->addMenuItems( $module->addAdminSettingsMenu() );';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
            
            // Adds the adminForm function
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_adminForm()';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->getAdminForm();';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
            
            // Adds the validate hook
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_adminForm_validate()';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->validateAdminForm( $form, $formState );';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
            
            
            // Adds the show() function
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_show()';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->createModuleContent( \'show\' );';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
            
        } elseif( $this->_formValues[ 'kickstarter_admin_add' ] ) {
            
            // Adds the menu hook
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_menu()';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->addAdminSettingsMenu();';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
            
            // Adds the adminForm() function
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_adminForm()';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->getAdminForm();';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
            
            // Adds the validate hook
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_adminForm_validate()';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->validateAdminForm( $form, $formState );';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
            
        } elseif( $this->_formValues[ 'kickstarter_menu_add' ] ) {
            
            // Adds the menu hook
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_menu()';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->addMenuItems();';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
            
            // Adds the show() function
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_show()';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->createModuleContent( \'show\' );';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
        }
    }
    
    /**
     * 
     */
    protected function _createClassFile()
    {
        // Path to the class file
        $path                    = $this->_moduleDir
                                 . DIRECTORY_SEPARATOR
                                 . $this->_moduleName
                                 . '.class.php';
        
        // Storage array
        $this->_files[ $path ]   = array();
        
        // Starts the file
        $this->_files[ $path ][] = '<?php';
        $this->_files[ $path ][] = '';
        
        // Starts the class comments
        $this->_files[ $path ][] = '/**';
        $this->_files[ $path ][] = ' * ' . $this->_formValues[ 'kickstarter_infos_title' ] . ' module for Drupal';
        $this->_files[ $path ][] = ' * ';
        $this->_files[ $path ][] = ' * @author          ' . $this->_formValues[ 'kickstarter_author_name' ] . ' <' . $this->_formValues[ 'kickstarter_author_email' ] . '>';
        $this->_files[ $path ][] = ' * @copyright       Copyright &copy; ' . date( 'Y' );
        $this->_files[ $path ][] = ' * @version         0.1';
        $this->_files[ $path ][] = ' */';
        
        // Starts the class
        $this->_files[ $path ][] = 'class ' . $this->_moduleName . ' extends Oop_Drupal_ModuleBase';
        $this->_files[ $path ][] = '{';
        
        // Checks if the perm hook is implemented
        if( $this->_formValues[ 'kickstarter_block_add' ] || $this->_formValues[ 'kickstarter_node_add' ] ) {
            
            // Adds the permissions array
            $this->_files[ $path ][] = '    /**';
            $this->_files[ $path ][] = '     * An array with the Drupal permission for the module';
            $this->_files[ $path ][] = '     */';
            $this->_files[ $path ][] = '    protected $_perms = array();';
            $this->_files[ $path ][] = '    ';
        }
        
        // Checks if the block hook is implemented
        if( $this->_formValues[ 'kickstarter_block_add' ] ) {
            
            // Adds the getBlock() method
            $this->_files[ $path ][] = '    /**';
            $this->_files[ $path ][] = '     * Gets the block view';
            $this->_files[ $path ][] = '     *';
            $this->_files[ $path ][] = '     * @param   Oop_Xhtml_Tag   The placeholder for the module content';
            $this->_files[ $path ][] = '     * @param   int             The delta offset, used to generate different contents for different blocks';
            $this->_files[ $path ][] = '     * @return  NULL';
            $this->_files[ $path ][] = '     */';
            $this->_files[ $path ][] = '    public function getBlock( Oop_Xhtml_Tag $content, $delta )';
            $this->_files[ $path ][] = '    {';
            
            // Checks if the CSS file has to be included
            if( $this->_formValues[ 'kickstarter_misc_css' ] ) {
                
                // Adds the CSS inclusion
                $this->_files[ $path ][] = '        // Includes the CSS file';
                $this->_files[ $path ][] = '        $this->_includeModuleCss();';
                $this->_files[ $path ][] = '        ';
            }
            
            // Checks if the JS file has to be included
            if( $this->_formValues[ 'kickstarter_misc_js' ] ) {
                
                // Adds the JS inclusion
                $this->_files[ $path ][] = '        // Includes the JS file';
                $this->_files[ $path ][] = '        $this->_includeModuleScript();';
                $this->_files[ $path ][] = '        ';
            }
            
            // Ends the getBlock() method
            $this->_files[ $path ][] = '        // Adds some content';
            $this->_files[ $path ][] = '        $content->span = \'Block content for the module \' . __CLASS__;';
            $this->_files[ $path ][] = '    }';
        }
        
        // Checks if the node hook is implemented
        if( $this->_formValues[ 'kickstarter_node_add' ] ) {
            
            // Checks if a method has already been added
            if( $this->_formValues[ 'kickstarter_block_add' ] ) {
                
                // Adds a blank line
                $this->_files[ $path ][] = '    ';
            }
            
            // Adds the getNode() method
            $this->_files[ $path ][] = '    /**';
            $this->_files[ $path ][] = '     * Gets the node view';
            $this->_files[ $path ][] = '     *';
            $this->_files[ $path ][] = '     * @param   Oop_Xhtml_Tag   The placeholder for the module content';
            $this->_files[ $path ][] = '     * @param   stdClass        The node object';
            $this->_files[ $path ][] = '     * @param   boolean         Wheter a teaser must be generated instead of the full content';
            $this->_files[ $path ][] = '     * @param   boolean         Whether the node is being displayed as a standalone page';
            $this->_files[ $path ][] = '     * @return  NULL';
            $this->_files[ $path ][] = '     */';
            $this->_files[ $path ][] = '    public function getNode( Oop_Xhtml_Tag $content, stdClass $node, $teaser, $page )';
            $this->_files[ $path ][] = '    {';
            
            // Checks if the CSS file has to be included
            if( $this->_formValues[ 'kickstarter_misc_css' ] ) {
                
                // Adds the CSS inclusion
                $this->_files[ $path ][] = '        // Includes the CSS file';
                $this->_files[ $path ][] = '        $this->_includeModuleCss();';
                $this->_files[ $path ][] = '        ';
            }
            
            // Checks if the JS file has to be included
            if( $this->_formValues[ 'kickstarter_misc_js' ] ) {
                
                // Adds the JS inclusion
                $this->_files[ $path ][] = '        // Includes the JS file';
                $this->_files[ $path ][] = '        $this->_includeModuleScript();';
                $this->_files[ $path ][] = '        ';
            }
            
            // Ends the getNode() method
            $this->_files[ $path ][] = '        // Adds some content';
            $this->_files[ $path ][] = '        $content->span = \'Node content for the module \' . __CLASS__;';
            $this->_files[ $path ][] = '    }';
        }
        
        // Checks if the admin form has been added
        if( $this->_formValues[ 'kickstarter_admin_add' ] ) {
            
            // Checks if a method has already been added
            if( $this->_formValues[ 'kickstarter_block_add' ]
                || $this->_formValues[ 'kickstarter_node_add' ]
            ) {
                
                // Adds a blank line
                $this->_files[ $path ][] = '    ';
            }
            
            // Adds the validateAdminForm() method
            $this->_files[ $path ][] = '    /**';
            $this->_files[ $path ][] = '     * Validates the administration settings form';
            $this->_files[ $path ][] = '     * ';
            $this->_files[ $path ][] = '     * @param   array   The form configuration';
            $this->_files[ $path ][] = '     * @param   array   The form values (passed by reference)';
            $this->_files[ $path ][] = '     * @return  NULL';
            $this->_files[ $path ][] = '     */';
            $this->_files[ $path ][] = '    public function validateAdminForm( $form, &$formState )';
            $this->_files[ $path ][] = '    {}';
        }
        
        // Checks if a menu item has been added
        if( $this->_formValues[ 'kickstarter_menu_add' ] ) {
            
            // Checks if a method has already been added
            if( $this->_formValues[ 'kickstarter_block_add' ]
                || $this->_formValues[ 'kickstarter_node_add' ]
                || $this->_formValues[ 'kickstarter_admin_add' ]
            ) {
                
                // Adds a blank line
                $this->_files[ $path ][] = '    ';
            }
            
            // Adds the addMenuItems() method
            $this->_files[ $path ][] = '    /**';
            $this->_files[ $path ][] = '     * Adds items to the Drupal menu';
            $this->_files[ $path ][] = '     * ';
            $this->_files[ $path ][] = '     * @param   array   An array in which to place the menu items (may have existing items, depending on the call context)';
            $this->_files[ $path ][] = '     * @return  array   The modified items array';
            $this->_files[ $path ][] = '     */';
            $this->_files[ $path ][] = '    public function addMenuItems( array $items = array() )';
            $this->_files[ $path ][] = '    {';
            $this->_files[ $path ][] = '        $items[ \'' . $this->_formValues[ 'kickstarter_menu_path' ] . '\' ] = array(';
            $this->_files[ $path ][] = '            \'title\'            => $this->_lang->menuTitle,';
            $this->_files[ $path ][] = '            \'page callback\'    => \'' . $this->_moduleName . '_show\',';
            $this->_files[ $path ][] = '            \'access arguments\' => array( \'access administration pages\' ),';
            $this->_files[ $path ][] = '        );';
            $this->_files[ $path ][] = '        ';
            $this->_files[ $path ][] = '        return $items';
            $this->_files[ $path ][] = '    }';
            
            // Adds a blank line
            $this->_files[ $path ][] = '    ';
            
            // Adds the show() method
            $this->_files[ $path ][] = '    /**';
            $this->_files[ $path ][] = '     * Shows a menu item';
            $this->_files[ $path ][] = '     * ';
            $this->_files[ $path ][] = '     * @param   Oop_Xhtml_Tag   The placeholder for the module content';
            $this->_files[ $path ][] = '     * @return  NULL';
            $this->_files[ $path ][] = '     */';
            $this->_files[ $path ][] = '    public function show( Oop_Xhtml_Tag $content )';
            $this->_files[ $path ][] = '    {';
            
            // Checks if the CSS file has to be included
            if( $this->_formValues[ 'kickstarter_misc_css' ] ) {
                
                // Adds the CSS inclusion
                $this->_files[ $path ][] = '        // Includes the CSS file';
                $this->_files[ $path ][] = '        $this->_includeModuleCss();';
                $this->_files[ $path ][] = '        ';
            }
            
            // Checks if the JS file has to be included
            if( $this->_formValues[ 'kickstarter_misc_js' ] ) {
                
                // Adds the JS inclusion
                $this->_files[ $path ][] = '        // Includes the JS file';
                $this->_files[ $path ][] = '        $this->_includeModuleScript();';
                $this->_files[ $path ][] = '        ';
            }
            
            // Ends the getNode() method
            $this->_files[ $path ][] = '        // Adds some content';
            $this->_files[ $path ][] = '        $content->span = \'Menu item content for the module \' . __CLASS__;';
            $this->_files[ $path ][] = '    }';
        }
        // Ends the class
        $this->_files[ $path ][] = '}';
        $this->_files[ $path ][] = '';
    }

    /**
     * 
     */
    protected function _createLangFile()
    {
        // Path to the lang file
        $path                    = $this->_moduleLangDir
                                 . DIRECTORY_SEPARATOR
                                 . Oop_Lang_Getter::getDefaultLanguage()
                                 . '.xml';
        
        // Storage array
        $this->_files[ $path ]   = array();
        
        // Starts the file
        $this->_files[ $path ][] = '<?xml version="1.0" encoding="utf-8"?' . '>';
        $this->_files[ $path ][] = '<labels>';
        
        // Starts the system section
        $this->_files[ $path ][] = '    <system>';
        
        // Adds the help label
        $this->_files[ $path ][] = '        <help>' . $this->_formValues[ 'kickstarter_infos_description' ] . '</help>';
        
        // Ends the system section
        $this->_files[ $path ][] = '    </system>';
        
        // Starts the settings section
        $this->_files[ $path ][] = '    <settings>';
        
        // Ends the settings section
        $this->_files[ $path ][] = '    </settings>';
        
        // Starts the module section
        $this->_files[ $path ][] = '    <module>';
        
        // Ends the module section
        $this->_files[ $path ][] = '    </module>';
        
        // Ends the file
        $this->_files[ $path ][] = '</labels>';
        $this->_files[ $path ][] = '';
    }
    
    /**
     * 
     */
    protected function _createSettingsFiles()
    {}
    
    /**
     * 
     */
    protected function _createCssFile()
    {
        // Checks if the CSS file must be written
        if( $this->_formValues[ 'kickstarter_misc_css' ] ) {
            
            // Path to the CSS file
            $path                    = $this->_moduleDir
                                     . DIRECTORY_SEPARATOR
                                     . $this->_moduleName
                                     . '.css';
            
            // Storage array
            $this->_files[ $path ]   = array();
            
            // Starts the file
            $this->_files[ $path ][] = '/* <![CDATA[ */';
            $this->_files[ $path ][] = '';
            $this->_files[ $path ][] = '';
            $this->_files[ $path ][] = '';
            $this->_files[ $path ][] = '/* // ]]> */';
            $this->_files[ $path ][] = '';
        }
    }
    
    /**
     * 
     */
    protected function _createJsFile()
    {
        // Checks if the JS file must be written
        if( $this->_formValues[ 'kickstarter_misc_js' ] ) {
            
            // Path to the CSS file
            $path                    = $this->_moduleDir
                                     . DIRECTORY_SEPARATOR
                                     . $this->_moduleName
                                     . '.js';
            
            // Storage array
            $this->_files[ $path ]   = array();
            
            // Starts the file
            $this->_files[ $path ][] = '// <![CDATA[';
            $this->_files[ $path ][] = '';
            
            // Starts the class comments
            $this->_files[ $path ][] = '/**';
            $this->_files[ $path ][] = ' * JavaScript class for the Drupal \'' . $this->_moduleName . '\' module';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * @author          ' . $this->_formValues[ 'kickstarter_author_name' ] . ' <' . $this->_formValues[ 'kickstarter_author_email' ] . '>';
            $this->_files[ $path ][] = ' * @copyright       Copyright &copy; ' . date( 'Y' );
            $this->_files[ $path ][] = ' * @version         0.1';
            $this->_files[ $path ][] = ' */';
            
            // Starts the class
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '()';
            $this->_files[ $path ][] = '{}';
            
            // Starts the class instanciation
            $this->_files[ $path ][] = '';
            $this->_files[ $path ][] = '// Creates a new instance of the module class';
            $this->_files[ $path ][] = $this->_moduleName . ' = new ' . $this->_moduleName . '();';
            
            
            // Ends the file
            $this->_files[ $path ][] = '';
            $this->_files[ $path ][] = '// ]]>';
            $this->_files[ $path ][] = '';
        }
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
            $content->div = drupal_get_form( 'kickstarter_form' );
        }
    }
    
    /**
     * 
     */
    public function getForm()
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
    public function validateForm( $form, &$formState )
    {
        // Path to the module directory
        $this->_moduleDir         = self::$_classManager->getDrupalPath()
                                  . 'sites'
                                  . DIRECTORY_SEPARATOR
                                  . 'all'
                                  . DIRECTORY_SEPARATOR
                                  . 'modules'
                                  . DIRECTORY_SEPARATOR
                                  . $formState[ 'values' ][ 'kickstarter_infos_name' ];
        
        // Checks if the module directory already exists
        if( file_exists( $this->_moduleDir ) ) {
            
            // Error - The directory already exists
            drupal_set_message( sprintf( $this->_lang->dirExists, $path ) );
        }
        
        // Checks if the author email is valid
        if( !valid_email_address( $formState[ 'values' ][ 'kickstarter_author_email' ] ) ) {
            
            // Error - Invalid email address
            form_set_error( 'kickstarter_author_email', $this->_lang->invalidEmail );
        }
        
        // Checks the module name
        if( !preg_match( '/^[a-z_]+$/', $formState[ 'values' ][ 'kickstarter_infos_name' ] ) ) {
            
            // Error - Invalid module name
            form_set_error( 'kickstarter_infos_name', $this->_lang->invalidModuleName );
        }
        
        // Checks the PHP version
        if( ( int )$formState[ 'values' ][ 'kickstarter_dependencies_version_php' ] < 5 ) {
            
            // Error - PHP version cannot be under 5
            form_set_error( 'kickstarter_dependencies_version_php', $this->_lang->phpVersionTooOld );
        }
    }
    
    /**
     * 
     */
    public function submitForm( $formId, $formValues )
    {
        // Stores the submitted values
        $this->_formValues        =& $formValues[ 'values' ];
        
        // Path to the module directory
        $this->_moduleDir         = self::$_classManager->getDrupalPath()
                                  . 'sites'
                                  . DIRECTORY_SEPARATOR
                                  . 'all'
                                  . DIRECTORY_SEPARATOR
                                  . 'modules'
                                  . DIRECTORY_SEPARATOR
                                  . $this->_formValues[ 'kickstarter_infos_name' ];
        
        // Path to the lang directory
        $this->_moduleLangDir     = $this->_moduleDir
                                  . DIRECTORY_SEPARATOR
                                  . 'lang';
        
        // Path to the settings directory
        $this->_moduleSettingsDir = $this->_moduleDir
                                  . DIRECTORY_SEPARATOR
                                  . 'settings';
        
        // Name of the module to write
        $this->_moduleName        = $this->_formValues[ 'kickstarter_infos_name' ];
        
        // Checks if the directories can be created
        if( file_exists( $this->_moduleDir ) ) {
            
            // Error - The directory already exists
            drupal_set_message( sprintf( $this->_lang->dirExists, $path ) );
            
        } elseif( $this->_createDirs() ) {
            
            // Creates the .info file
            $this->_createInfoFile();
            
            // Creates the .install file
            $this->_createInstallFile();
            
            // Creates the .module file
            $this->_createModuleFile();
            
            // Creates the class file
            $this->_createClassFile();
            
            // Creates the lang file
            $this->_createLangFile();
            
            // Creates the settings file(s)
            $this->_createSettingsFiles();
            
            // Creates the CSS file
            $this->_createCssFile();
            
            // Creates the JS file
            $this->_createJsFile();
            
            // Process each file
            foreach( $this->_files as $path => &$lines ) {
                
                // Tries to write the file
                if( !file_put_contents( $path, implode( self::$_NL, $lines ) ) ) {
                    
                    // Error - impossible to write the current file
                    drupal_set_message( sprintf( $this->_lang->cannotCreateFile, $path ) );
                    break;
                }
            }
        }
    }
    
    /**
     * 
     */
    public function addMenuItems( array $items = array() )
    {
        // Adds the menu item for the kickstarter in the admin pages
        $items[ 'admin/build/oopkickstarter' ] = array(
            'title'            => $this->_lang->getLabel( 'admin_build_oopkickstarter_title', 'system' ),
            'description'      => $this->_lang->getLabel( 'admin_build_oopkickstarter_description', 'system' ),
            'page callback'    => 'kickstarter_show',
            'access arguments' => array('access administration pages'),
        );
        
        // Returns the menu items
        return $items;
    }
}
