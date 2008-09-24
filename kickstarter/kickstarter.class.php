<?php

# $Id$

/**
 * OOP kickstarter module for Drupal
 * 
 * Creation wizard for the OOP modules that will create all the necessary
 * development files to build an OOP module
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
class kickstarter extends Oop_Drupal_ModuleBase implements Oop_Drupal_MenuItem_Interface
{
    /**
     * 
     */
    protected $_files              = array();
    
    /**
     * 
     */
    protected $_formValues         = array();
    
    /**
     * 
     */
    protected $_perms              = array(
        'access kickstarter admin/build/oopkickstarter'
    );
    
    /**
     * 
     */
    protected $_moduleName         = '';
    
    /**
     * 
     */
    protected $_moduleDir          = '';
    
    /**
     * 
     */
    protected $_moduleLangDir      = '';
    
    /**
     * 
     */
    protected $_moduleSettingsDir  = '';
    
    /**
     * 
     */
    protected $_moduleTemplatesDir = '';
    
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
        
        // Checks if the templates directory is needed
        if( $this->_formValues[ 'kickstarter_template_add' ] ) {
            
            // Tries to create the settings directory
            if( !mkdir( $this->_moduleTemplatesDir ) ) {
                
                // Error- Cannot create the settings directory
                drupal_set_message( sprintf( $this->_lang->cannotCreateDir, $this->_moduleTemplatesDir ) );
                
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
        
        // Checks if we want CVS support
        if( $this->_formValues[ 'kickstarter_infos_cvs' ] ) {
            
            // Adds the CVS ID variable
            $this->_files[ $path ][] = '; $' . 'Id' . '$';
            $this->_files[ $path ][] = '';
        }
        
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
        
        // Checks if we want CVS support
        if( $this->_formValues[ 'kickstarter_infos_cvs' ] ) {
            
            // Adds the CVS ID variable
            $this->_files[ $path ][] = '# $' . 'Id' . '$';
            $this->_files[ $path ][] = '';
        }
        
        // Starts the install hook
        $this->_files[ $path ][] = '/**';
        $this->_files[ $path ][] = ' * Drupal \'install\' hook';
        $this->_files[ $path ][] = ' * ';
        $this->_files[ $path ][] = ' * This function will be called when the module is installed.';
        $this->_files[ $path ][] = ' * It will automatically set the weight of the module to be sure that the \'oop\'';
        $this->_files[ $path ][] = ' * module will be loded first.';
        $this->_files[ $path ][] = ' * If you have custom database tables, you should also install them here,';
        $this->_files[ $path ][] = ' * using the drupal_install_schema() function.';
        $this->_files[ $path ][] = ' * ';
        $this->_files[ $path ][] = ' * @return  NULL';
        $this->_files[ $path ][] = ' */';
        $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_install()';
        $this->_files[ $path ][] = '{';
        $this->_files[ $path ][] = '    $oopWeight = (int)db_result( db_query( "SELECT weight FROM {system} WHERE name = \'oop\'" ) );';
        $this->_files[ $path ][] = '    db_query( "UPDATE {system} SET weight = %d WHERE name = \'' . $this->_moduleName . '\'", $oopWeight + 1 );';
        
        // Checks if we have to add a database table
        if( $this->_formValues[ 'kickstarter_table_add' ] ) {
            
            // Installs the schema
            $this->_files[ $path ][] = '    drupal_install_schema( \'' . $this->_moduleName . '\' );';
        }
        
        // Ends the install hook and starts the uninstall hook
        $this->_files[ $path ][] = '}';
        $this->_files[ $path ][] = '';
        $this->_files[ $path ][] = '/**';
        $this->_files[ $path ][] = ' * Drupal \'uninstall\' hook';
        $this->_files[ $path ][] = ' * ';
        $this->_files[ $path ][] = ' * This function will be called when the module is uninstalled.';
        $this->_files[ $path ][] = ' * It will automatically delete all the variables belonging to this module,';
        $this->_files[ $path ][] = ' * stored in the database.';
        $this->_files[ $path ][] = ' * If you have custom database tables, you should also uninstall them here,';
        $this->_files[ $path ][] = ' * using the drupal_uninstall_schema() function.';
        $this->_files[ $path ][] = ' * ';
        $this->_files[ $path ][] = ' * @return  NULL';
        $this->_files[ $path ][] = ' * @see     Oop_Drupal_Utils::deleteModuleVariables';
        $this->_files[ $path ][] = ' */';
        $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_uninstall()';
        $this->_files[ $path ][] = '{';
        $this->_files[ $path ][] = '    Oop_Drupal_Utils::getInstance()->deleteModuleVariables( \'' . $this->_moduleName . '\' );';
        
        // Checks if we have to add a database table
        if( $this->_formValues[ 'kickstarter_table_add' ] ) {
            
            // Uninstalls the schema
            $this->_files[ $path ][] = '    drupal_uninstall_schema( \'' . $this->_moduleName . '\' );';
        }
        
        // Ends the uninstall hook
        $this->_files[ $path ][] = '}';
        $this->_files[ $path ][] = '';
        
        // Checks if we have to add a database table
        if( $this->_formValues[ 'kickstarter_table_add' ] ) {
            
            // Adds the schema hook
            $this->_files[ $path ][] = '/**';
            $this->_files[ $path ][] = ' * Drupal \'schema\' hook';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * This function will install the database needed by the module.';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * @return  array   The database schema';
            $this->_files[ $path ][] = ' * @see     Oop_Drupal_Database::createSchema';
            $this->_files[ $path ][] = ' */';
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_schema()';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    $fields  = array();';
            $this->_files[ $path ][] = '    $indexes = array();';
            $this->_files[ $path ][] = '    $unique  = array();';
            $this->_files[ $path ][] = '    return Oop_Drupal_Database::createSchema( \'' . $this->_moduleName . '_' . $this->_formValues[ 'kickstarter_table_name' ] . '\', $fields, $indexes, $unique );';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
        }
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
        
        // Checks if we want CVS support
        if( $this->_formValues[ 'kickstarter_infos_cvs' ] ) {
            
            // Adds the CVS ID variable
            $this->_files[ $path ][] = '# $' . 'Id' . '$';
            $this->_files[ $path ][] = '';
        }
        
        // Checks if the number of blocks can be set
        if( $this->_formValues[ 'kickstarter_admin_add' ] && $this->_formValues[ 'kickstarter_admin_blocks_number' ] ) {
            
            // Sets the number of blocks
            $this->_files[ $path ][] = 'try {';
            $this->_files[ $path ][] = '    ';
            $this->_files[ $path ][] = '    // Sets the number of blocks available from this module';
            $this->_files[ $path ][] = '    Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->setNumberOfBlocks(';
            $this->_files[ $path ][] = '        Oop_Drupal_Utils::getInstance()->getModuleVariable(';
            $this->_files[ $path ][] = '            \'test\',';
            $this->_files[ $path ][] = '            \'number_of_blocks\',';
            $this->_files[ $path ][] = '            1';
            $this->_files[ $path ][] = '        ),';
            $this->_files[ $path ][] = '        true';
            $this->_files[ $path ][] = '    );';
            $this->_files[ $path ][] = '    ';
            $this->_files[ $path ][] = '} catch( Exception $e ) {';
            $this->_files[ $path ][] = '    ';
            $this->_files[ $path ][] = '    // Nothing, as we want to avoid an error that may occur during the installation process';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
        }
        
        // Creates the help hook
        $this->_files[ $path ][] = '/**';
        $this->_files[ $path ][] = ' * Drupal \'help\' hook';
        $this->_files[ $path ][] = ' * ';
        $this->_files[ $path ][] = ' * This function returns the help text for the admin/help# page. The label';
        $this->_files[ $path ][] = ' * returned must be placed in the module\'s lang file, in the \'lang/\'';
        $this->_files[ $path ][] = ' * directory. It\'s the \'help\' node of the \'system\' section.';
        $this->_files[ $path ][] = ' * ';
        $this->_files[ $path ][] = ' * @param   string  The path for which to display help';
        $this->_files[ $path ][] = ' * @param   array   An array that holds the current path as would be returned from the arg() function';
        $this->_files[ $path ][] = ' * @return  string  The help text';
        $this->_files[ $path ][] = ' * @see     Oop_Drupal_Hooks::help';
        $this->_files[ $path ][] = ' */';
        $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_help( $path, $arg )';
        $this->_files[ $path ][] = '{';
        $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->help( $path, $arg );';
        $this->_files[ $path ][] = '}';
        $this->_files[ $path ][] = '';
        
        // Checks if we have to add the perm hook
        if( $this->_formValues[ 'kickstarter_block_add' ]
            || $this->_formValues[ 'kickstarter_node_add' ]
            || $this->_formValues[ 'kickstarter_admin_add' ]
            || $this->_formValues[ 'kickstarter_menu_add' ]
        ) {
            
            // Creates the perm hook
            $this->_files[ $path ][] = '/**';
            $this->_files[ $path ][] = ' * Drupal \'perm\' hook';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * This function returns the available permissions for the module. They must';
            $this->_files[ $path ][] = ' * be declared in the $_perm property of the module class.';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * @return  array   The permissions array';
            $this->_files[ $path ][] = ' * @see     Oop_Drupal_Hooks::perm';
            $this->_files[ $path ][] = ' */';
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_perm()';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->perm();';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
        }
        
        // Checks if a block content must be added
        if( $this->_formValues[ 'kickstarter_block_add' ] ) {
            
            // Creates the block hook
            $this->_files[ $path ][] = '/**';
            $this->_files[ $path ][] = ' * Drupal \'block\' hook';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * This function lets Drupal know that the module has block content. The method';
            $this->_files[ $path ][] = ' * used to generate the block content is getBlock(), defined in the module';
            $this->_files[ $path ][] = ' * class.';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * @param   string  The kind of block to display';
            $this->_files[ $path ][] = ' * @param   int     The delta offset, used to generate different contents for different blocks';
            $this->_files[ $path ][] = ' * @param   array   The edited items (only if $op is \'save\')';
            $this->_files[ $path ][] = ' * @return  array   The Drupal block';
            $this->_files[ $path ][] = ' * @see     Oop_Drupal_Hooks::block';
            $this->_files[ $path ][] = ' * @see     ' . $this->_moduleName . '::getBlock';
            $this->_files[ $path ][] = ' */';
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_block( $op = \'list\', $delta = 0, array $edit = array() )';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->block( $op, $delta, $edit );';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
        }
        
        // Checks if a node content must be added
        if( $this->_formValues[ 'kickstarter_node_add' ] ) {
            
            // Adds the access hook
            $this->_files[ $path ][] = '/**';
            $this->_files[ $path ][] = ' * Drupal \'access\' hook';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * This function controls the access for the node content. Permissions must';
            $this->_files[ $path ][] = ' * be declared in the $_perm property of the module class.';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * @param   string  The requested operation';
            $this->_files[ $path ][] = ' * @param   mixed   The node object, if any';
            $this->_files[ $path ][] = ' * @param   mixed   The user account, if any';
            $this->_files[ $path ][] = ' * @return  boolean Wheter the access is granted or not for the given operation';
            $this->_files[ $path ][] = ' * @see     Oop_Drupal_Hooks::access';
            $this->_files[ $path ][] = ' * @see     Oop_Drupal_Hooks::perm';
            $this->_files[ $path ][] = ' */';
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_access( $op, $node, $account )';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->access( $op, $node, $account );';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
            
            // Adds the form hook
            $this->_files[ $path ][] = '/**';
            $this->_files[ $path ][] = ' * Drupal \'form\' hook';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * This function returns the form configuration for the node creation/ edition.';
            $this->_files[ $path ][] = ' * The form configuration must be defined in the \'settings/node.form.php\' file.';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * @param   stdClass    The node object';
            $this->_files[ $path ][] = ' * @return  array       An array with the form configuration';
            $this->_files[ $path ][] = ' * @see     Oop_Drupal_Hooks::form';
            $this->_files[ $path ][] = ' */';
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_form( stdClass $node )';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->form( $node );';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
            
            // Adds the node_info hook
            $this->_files[ $path ][] = '/**';
            $this->_files[ $path ][] = ' * Drupal \'node_info\' hook';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * This function returns informations about the node. The labels used must be';
            $this->_files[ $path ][] = ' * placed in the module\'s lang file, in the \'lang/\' directory. They are all';
            $this->_files[ $path ][] = ' * in the \'system\' section, and are called \'node_info_name\' and';
            $this->_files[ $path ][] = ' * \'node_info_description\'.';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * @return  array    The information array for the Drupal node';
            $this->_files[ $path ][] = ' * @see     Oop_Drupal_Hooks::node_info';
            $this->_files[ $path ][] = ' */';
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_node_info()';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->node_info();';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
            
            // Adds the view hook
            $this->_files[ $path ][] = '/**';
            $this->_files[ $path ][] = ' * Drupal \'view\' hook';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * This function lets Drupal know that the module has node content. The method';
            $this->_files[ $path ][] = ' * used to generate the node content is getNode(), defined in the module';
            $this->_files[ $path ][] = ' * class.';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * @param   stdClass    The node object';
            $this->_files[ $path ][] = ' * @param   boolean     Wheter a teaser must be generated instead of the full content';
            $this->_files[ $path ][] = ' * @param   boolean     Whether the node is being displayed as a standalone page';
            $this->_files[ $path ][] = ' * @return  stdClass    The node object';
            $this->_files[ $path ][] = ' * @see     Oop_Drupal_Hooks::view';
            $this->_files[ $path ][] = ' * @see     ' . $this->_moduleName . '::getNode';
            $this->_files[ $path ][] = ' */';
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_view( stdClass $node, $teaser = false, $page = false )';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->view( $node, $teaser, $page );';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
            
            // Adds the insert hook
            $this->_files[ $path ][] = '/**';
            $this->_files[ $path ][] = ' * Drupal \'insert\' hook';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * This function is called when a new node is created. All fields will be';
            $this->_files[ $path ][] = ' * automatically stored in the database and placed in the $_modVars property';
            $this->_files[ $path ][] = ' * of the module class.';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * @param   stdClass    The node object';
            $this->_files[ $path ][] = ' * @see     Oop_Drupal_Hooks::insert';
            $this->_files[ $path ][] = ' */';
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_insert( stdClass $node )';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->insert( $node );';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
            
            // Adds the update hook
            $this->_files[ $path ][] = '/**';
            $this->_files[ $path ][] = ' * Drupal \'update\' hook';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * This function is called when a node is updated. All fields will be';
            $this->_files[ $path ][] = ' * automatically stored in the database and placed in the $_modVars property';
            $this->_files[ $path ][] = ' * of the module class.';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * @param   stdClass    The node object';
            $this->_files[ $path ][] = ' * @see     Oop_Drupal_Hooks::insert';
            $this->_files[ $path ][] = ' */';
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_update( stdClass $node )';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->update( $node );';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
        }
        
        // Checks if an administration and/or a menu item page must be added
        if( $this->_formValues[ 'kickstarter_admin_add' ] || $this->_formValues[ 'kickstarter_menu_add' ] ) {
            
            // Adds the menu hook
            $this->_files[ $path ][] = '/**';
            $this->_files[ $path ][] = ' * Drupal \'menu\' hook';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * This function lets Drupal know that the module has custom menu items.';
            $this->_files[ $path ][] = ' * An administration settings page will be automatically added if the file';
            $this->_files[ $path ][] = ' * \'settings/admin.form.php\' exists.';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * @return  array   An array with the menu items';
            $this->_files[ $path ][] = ' * @see     Oop_Drupal_Hooks::addMenuItems';
            $this->_files[ $path ][] = ' * @see     Oop_Drupal_Hooks::addAdminSettingsMenu';
            $this->_files[ $path ][] = ' * @see     Oop_Drupal_Hooks::perm';
            $this->_files[ $path ][] = ' */';
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_menu()';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->menu();';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
        }
        
        // Checks if an administration settings page has been added
        if( $this->_formValues[ 'kickstarter_admin_add' ] ) {
            
            // Adds the adminForm() function
            $this->_files[ $path ][] = '/**';
            $this->_files[ $path ][] = ' * Gets the admin form';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * This function returns the form configuration for the administration settings';
            $this->_files[ $path ][] = ' * page. The form configuration must be defined in the \'settings/admin.form.php\'';
            $this->_files[ $path ][] = ' * file.';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * @return  array   An array with the form configuration';
            $this->_files[ $path ][] = ' * @see     Oop_Drupal_Hooks::getAdminForm';
            $this->_files[ $path ][] = ' */';
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_adminForm()';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->getAdminForm();';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
            
            // Adds the validate hook
            $this->_files[ $path ][] = '/**';
            $this->_files[ $path ][] = ' * Validates the admin form';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * This function will be called when the form in the administration settings';
            $this->_files[ $path ][] = ' * page is submitted.';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * @param   array   The form configuration';
            $this->_files[ $path ][] = ' * @param   array   The submitted values';
            $this->_files[ $path ][] = ' * @return  NULL';
            $this->_files[ $path ][] = ' * @see     ' . $this->_moduleName . '::validateAdminForm';
            $this->_files[ $path ][] = ' */';
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_adminForm_validate( array $form, array &$formState )';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->validateAdminForm( $form, $formState );';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
            
        }
        
        // Checks if a menu item has been added
        if( $this->_formValues[ 'kickstarter_menu_add' ] ) {
            
            // Adds the show() function
            $this->_files[ $path ][] = '/**';
            $this->_files[ $path ][] = ' * Displays the page for a menu item';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * This function generates the content for a page placed in a custom menu item';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * @return  string  The content of the page';
            $this->_files[ $path ][] = ' * @see     Oop_Drupal_Hooks::menu';
            $this->_files[ $path ][] = ' * @see     Oop_Drupal_Hooks::createModuleContent';
            $this->_files[ $path ][] = ' * @see     ' . $this->_moduleName . '::show';
            $this->_files[ $path ][] = ' */';
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_show()';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->createModuleContent( \'show\' );';
            $this->_files[ $path ][] = '}';
            $this->_files[ $path ][] = '';
        }
        
        // Checks if a filter must be added
        if( $this->_formValues[ 'kickstarter_filter_add' ] ) {
            
            // Adds the filter hook
            $this->_files[ $path ][] = '/**';
            $this->_files[ $path ][] = ' * Drupal \'filter\' hook';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * Process a filter. The methods used to prepare and process the filter must';
            $this->_files[ $path ][] = ' * be declared in the module class. They are prepareFilter() and processFilter().';
            $this->_files[ $path ][] = ' * ';
            $this->_files[ $path ][] = ' * @param   string  Which filtering operation to perform';
            $this->_files[ $path ][] = ' * @param   int     Which of the module\'s filters to use';
            $this->_files[ $path ][] = ' * @param   int     Which input format the filter is being used';
            $this->_files[ $path ][] = ' * @param   string  The content to filter';
            $this->_files[ $path ][] = ' * @return  mixed   Depends on $op';
            $this->_files[ $path ][] = ' * @see     Oop_Drupal_Hooks::filter';
            $this->_files[ $path ][] = ' * @see     ' . $this->_moduleName . '::prepareFilter';
            $this->_files[ $path ][] = ' * @see     ' . $this->_moduleName . '::processFilter';
            $this->_files[ $path ][] = ' */';
            $this->_files[ $path ][] = 'function ' . $this->_moduleName . '_filter( $op, $delta = 0, $format = -1, $text = \'\' )';
            $this->_files[ $path ][] = '{';
            $this->_files[ $path ][] = '    return Oop_Core_ClassManager::getInstance()->getModule( \'' . $this->_moduleName . '\' )->filter( $op, $delta, $format, $text );';
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
        
        // Checks if we want CVS support
        if( $this->_formValues[ 'kickstarter_infos_cvs' ] ) {
            
            // Adds the CVS ID variable
            $this->_files[ $path ][] = '# $' . 'Id' . '$';
            $this->_files[ $path ][] = '';
        }
        
        // Starts the class comments
        $this->_files[ $path ][] = '/**';
        $this->_files[ $path ][] = ' * ' . $this->_formValues[ 'kickstarter_infos_title' ] . ' module for Drupal';
        $this->_files[ $path ][] = ' * ';
        $this->_files[ $path ][] = ' * ' . $this->_formValues[ 'kickstarter_infos_description' ];
        $this->_files[ $path ][] = ' * ';
        $this->_files[ $path ][] = ' * @author          ' . $this->_formValues[ 'kickstarter_author_name' ] . ' <' . $this->_formValues[ 'kickstarter_author_email' ] . '>';
        $this->_files[ $path ][] = ' * @copyright       Copyright &copy; ' . date( 'Y' );
        $this->_files[ $path ][] = ' * @version         0.1';
        $this->_files[ $path ][] = ' */';
        
        // Storage for the interfaces
        $interfaces = array();
        
        // Checks if we have to implement the block interface
        if( $this->_formValues[ 'kickstarter_block_add' ] ) {
            
            // Adds the interface to the list
            $interfaces[] = 'Oop_Drupal_Block_Interface';
        }
        
        // Checks if we have to implement the node interface
        if( $this->_formValues[ 'kickstarter_node_add' ] ) {
            
            // Adds the interface to the list
            $interfaces[] = 'Oop_Drupal_Node_Interface';
        }
        
        // Checks if we have to implement the filter interface
        if( $this->_formValues[ 'kickstarter_filter_add' ] ) {
            
            // Adds the interface to the list
            $interfaces[] = 'Oop_Drupal_Filter_Interface';
        }
        
        // Checks if we have to implement the menu item interface
        if( $this->_formValues[ 'kickstarter_admin_add' ] || $this->_formValues[ 'kickstarter_menu_add' ] ) {
            
            // Adds the interface to the list
            $interfaces[] = 'Oop_Drupal_MenuItem_Interface';
        }
        
        // Builds the implements statement if interfaces should be implemented in the module class
        $implements = ( count( $interfaces ) ) ? ' implements ' . implode( ', ', $interfaces ) : '';
        
        // Starts the class
        $this->_files[ $path ][] = 'class ' . $this->_moduleName . ' extends Oop_Drupal_ModuleBase' . $implements;
        $this->_files[ $path ][] = '{';
        
        // Checks if we have Smarty templates
        if( $this->_formValues[ 'kickstarter_template_add' ] ) {
            
            // Adds the template object property
            $this->_files[ $path ][] = '    /**';
            $this->_files[ $path ][] = '     * The template object';
            $this->_files[ $path ][] = '     */';
            $this->_files[ $path ][] = '    protected $_tmpl  = NULL;';
            $this->_files[ $path ][] = '    ';
        }
        
        // Checks if the perm hook is implemented
        if( $this->_formValues[ 'kickstarter_block_add' ]
            || $this->_formValues[ 'kickstarter_node_add' ]
            || $this->_formValues[ 'kickstarter_admin_add' ]
            || $this->_formValues[ 'kickstarter_menu_add' ]
        ) {
            
            // Starts the permissions array
            $this->_files[ $path ][] = '    /**';
            $this->_files[ $path ][] = '     * An array with the Drupal permission for the module';
            $this->_files[ $path ][] = '     */';
            $this->_files[ $path ][] = '    protected $_perms = array(';
            
            // Checks if we have a block
            if( $this->_formValues[ 'kickstarter_block_add' ] ) {
                
                // Adds the permissions
                $this->_files[ $path ][] = '        \'access ' . $this->_moduleName . ' block\',';
                
                // Checks if we have a block configuration
                if( $this->_formValues[ 'block_add_config' ] ) {
                    
                    // Adds the permissions
                    $this->_files[ $path ][] = '        \'access ' . $this->_moduleName . ' block config\',';
                }
            }
            
            // Checks if we have a node
            if( $this->_formValues[ 'kickstarter_node_add' ] ) {
                
                // Adds the permissions
                $this->_files[ $path ][] = '        \'access ' . $this->_moduleName . ' node\',';
                $this->_files[ $path ][] = '        \'create ' . $this->_moduleName . ' node\',';
                $this->_files[ $path ][] = '        \'edit ' . $this->_moduleName . ' node\',';
                $this->_files[ $path ][] = '        \'edit own ' . $this->_moduleName . ' node\',';
            }
            
            // Checks if we have an administration settings page
            if( $this->_formValues[ 'kickstarter_admin_add' ] ) {
                
                // Adds the permissions
                $this->_files[ $path ][] = '        \'access ' . $this->_moduleName . ' admin\',';
            }
            
            // Checks if we have an custom menu item
            if( $this->_formValues[ 'kickstarter_menu_add' ] ) {
                
                // Adds the permissions
                $this->_files[ $path ][] = '        \'access ' . $this->_moduleName . ' ' . $this->_formValues[ 'kickstarter_menu_path' ] . '\',';
            }
            
            $this->_files[ $path ][] = '    );';
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
            
            // Checks if we have Smarty templates
            if( $this->_formValues[ 'kickstarter_template_add' ] ) {
                
                // Adds the template object
                $this->_files[ $path ][] = '        // Gets the template object';
                $this->_files[ $path ][] = '        $this->_tmpl   = $this->_getTemplate();';
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
            
            // Checks if we have Smarty templates
            if( $this->_formValues[ 'kickstarter_template_add' ] ) {
                
                // Adds the template object
                $this->_files[ $path ][] = '        // Gets the template object';
                $this->_files[ $path ][] = '        $this->_tmpl   = $this->_getTemplate();';
                $this->_files[ $path ][] = '        ';
            }
            
            // Ends the getNode() method
            $this->_files[ $path ][] = '        // Adds some content';
            $this->_files[ $path ][] = '        $content->span = \'Node content for the module \' . __CLASS__;';
            $this->_files[ $path ][] = '    }';
        }
        
        // Checks if we must declare the addMenuItems() method
        if( $this->_formValues[ 'kickstarter_admin_add' ] || $this->_formValues[ 'kickstarter_menu_add' ] ) {
            
            // Checks if a method has already been added
            if( $this->_formValues[ 'kickstarter_block_add' ]
                || $this->_formValues[ 'kickstarter_node_add' ]
            ) {
                
                // Adds a blank line
                $this->_files[ $path ][] = '    ';
            }
            
            // Starts the addMenuItems() method
            $this->_files[ $path ][] = '    /**';
            $this->_files[ $path ][] = '     * Adds items to the Drupal menu';
            $this->_files[ $path ][] = '     * ';
            $this->_files[ $path ][] = '     * @param   array   An array in which to place the menu items, passed by reference. It may contains existing menu items, for instance if an administration settings form exists';
            $this->_files[ $path ][] = '     * @return  NULL';
            $this->_files[ $path ][] = '     */';
            $this->_files[ $path ][] = '    public function addMenuItems( array &$items )';
            
            // Checks if a custom menu item has been defined
            if( $this->_formValues[ 'kickstarter_menu_add' ] ) {
                
                // Name of the menu in which to place the item
                $menuName                = ( $this->_formValues[ 'kickstarter_menu_name' ] === 'navigation' ) ? $this->_formValues[ 'kickstarter_menu_name' ] : $this->_formValues[ 'kickstarter_menu_name' ] . '-links';
                
                // Adds the custom menu item
                $this->_files[ $path ][] = '    {';
                $this->_files[ $path ][] = '        $items[ \'' . $this->_formValues[ 'kickstarter_menu_path' ] . '\' ] = array(';
                $this->_files[ $path ][] = '            \'title\'            => $this->_lang->getLabel( \'menu_item_title\', \'system\' ),';
                $this->_files[ $path ][] = '            \'description\'      => $this->_lang->getLabel( \'menu_item_description\', \'system\' ),';
                $this->_files[ $path ][] = '            \'page callback\'    => \'' . $this->_moduleName . '_show\',';
                $this->_files[ $path ][] = '            \'access arguments\' => array( \'access ' . $this->_moduleName . ' ' . $this->_formValues[ 'kickstarter_menu_path' ] . '\' ),';
                $this->_files[ $path ][] = '            \'menu_name\'        => \'' . $menuName . '\',';
                $this->_files[ $path ][] = '            \'type\'             => ' . strtoupper( $this->_formValues[ 'kickstarter_menu_type' ] );
                $this->_files[ $path ][] = '        );';
                $this->_files[ $path ][] = '    }';
                
            } else {
                
                // Ends the method
                $this->_files[ $path ][] = '    {}';
            }
        }
        
        // Checks if the admin form has been added
        if( $this->_formValues[ 'kickstarter_admin_add' ] ) {
            
            // Adds a blank line
            $this->_files[ $path ][] = '    ';
            
            // Adds the validateAdminForm() method
            $this->_files[ $path ][] = '    /**';
            $this->_files[ $path ][] = '     * Validates the administration settings form';
            $this->_files[ $path ][] = '     * ';
            $this->_files[ $path ][] = '     * @param   array   The form configuration';
            $this->_files[ $path ][] = '     * @param   array   The form values (passed by reference)';
            $this->_files[ $path ][] = '     * @return  NULL';
            $this->_files[ $path ][] = '     */';
            $this->_files[ $path ][] = '    public function validateAdminForm( array $form, array &$formState )';
            
            // Checks if the number of blocks can be settable
            if( $this->_formValues[ 'kickstarter_admin_blocks_number' ] ) {
                
                // Adds the check for the number of blocks
                $this->_files[ $path ][] = '    {';
                $this->_files[ $path ][] = '        // Gets the number of blocks';
                $this->_files[ $path ][] = '        $number = $formState[ \'values\' ][ \'' . $this->_moduleName . '_number_of_blocks\' ];';
                $this->_files[ $path ][] = '        ';
                $this->_files[ $path ][] = '        // Checks for a numeric value';
                $this->_files[ $path ][] = '        if( !is_numeric( $number ) ) {';
                $this->_files[ $path ][] = '            ';
                $this->_files[ $path ][] = '            // Error - Value is not numeric';
                $this->_files[ $path ][] = '            form_set_error( \'' . $this->_moduleName . '_number_of_blocks\', $this->_lang->blocksNumberNotNumeric );';
                $this->_files[ $path ][] = '        }';
                $this->_files[ $path ][] = '    }';
                
            } else {
                
                // Ends the method
                $this->_files[ $path ][] = '    {}';
            }
        }
        
        // Checks if a menu item has been added
        if( $this->_formValues[ 'kickstarter_menu_add' ] ) {
            
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
            
            // Checks if we have Smarty templates
            if( $this->_formValues[ 'kickstarter_template_add' ] ) {
                
                // Adds the template object
                $this->_files[ $path ][] = '        // Gets the template object';
                $this->_files[ $path ][] = '        $this->_tmpl   = $this->_getTemplate();';
                $this->_files[ $path ][] = '        ';
            }
            
            // Ends the show() method
            $this->_files[ $path ][] = '        // Adds some content';
            $this->_files[ $path ][] = '        $content->span = \'Menu item content for the module \' . __CLASS__;';
            $this->_files[ $path ][] = '    }';
        }
        
        // Checks if a filter has been added
        if( $this->_formValues[ 'kickstarter_filter_add' ] ) {
            
            // Checks if a method has already been added
            if( $this->_formValues[ 'kickstarter_block_add' ]
                || $this->_formValues[ 'kickstarter_node_add' ]
                || $this->_formValues[ 'kickstarter_admin_add' ]
                || $this->_formValues[ 'kickstarter_menu_add' ]
            ) {
                
                // Adds a blank line
                $this->_files[ $path ][] = '    ';
            }
            
            // Adds the prepareFilter() method
            $this->_files[ $path ][] = '    /**';
            $this->_files[ $path ][] = '     * Prepares the filter';
            $this->_files[ $path ][] = '     * ';
            $this->_files[ $path ][] = '     * @param   int     Which of the module\'s filters to use';
            $this->_files[ $path ][] = '     * @param   int     Which input format the filter is being used';
            $this->_files[ $path ][] = '     * @param   string  The content to filter';
            $this->_files[ $path ][] = '     * @return  string  The prepared text';
            $this->_files[ $path ][] = '     */';
            $this->_files[ $path ][] = '    public function prepareFilter( $delta, $format, $text )';
            $this->_files[ $path ][] = '    {';
            $this->_files[ $path ][] = '        return $text;';
            $this->_files[ $path ][] = '    }';
            
            // Adds a blank line
            $this->_files[ $path ][] = '    ';
            
            // Adds the prepareFilter() method
            $this->_files[ $path ][] = '    /**';
            $this->_files[ $path ][] = '     * Process the filter';
            $this->_files[ $path ][] = '     * ';
            $this->_files[ $path ][] = '     * @param   int     Which of the module\'s filters to use';
            $this->_files[ $path ][] = '     * @param   int     Which input format the filter is being used';
            $this->_files[ $path ][] = '     * @param   string  The content to filter';
            $this->_files[ $path ][] = '     * @return  string  The processed text';
            $this->_files[ $path ][] = '     */';
            $this->_files[ $path ][] = '    public function processFilter( $delta, $format, $text )';
            $this->_files[ $path ][] = '    {';
            $this->_files[ $path ][] = '        return $text;';
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
        
        // Checks if we want CVS support
        if( $this->_formValues[ 'kickstarter_infos_cvs' ] ) {
            
            // Adds the CVS ID variable
            $this->_files[ $path ][] = '';
            $this->_files[ $path ][] = '<!-- $' . 'Id' . '$ -->';
            $this->_files[ $path ][] = '';
        }
        
        // Starts the label container
        $this->_files[ $path ][] = '<labels>';
        
        // Starts the system section
        $this->_files[ $path ][] = '    <system>';
        
        // Adds the help label
        $this->_files[ $path ][] = '        <help>' . $this->_formValues[ 'kickstarter_infos_description' ] . '</help>';
        
        // Checks if a block has been added
        if( $this->_formValues[ 'kickstarter_block_add' ] ) {
            
            // Adds the block labels
            $this->_files[ $path ][] = '        <block_0_subject>' .  $this->_formValues[ 'kickstarter_block_title' ]  . '</block_0_subject>';
            $this->_files[ $path ][] = '        <block_0_info>' .  $this->_formValues[ 'kickstarter_block_description' ]  . '</block_0_info>';
        }
        
        // Checks if a node has been added
        if( $this->_formValues[ 'kickstarter_node_add' ] ) {
            
            // Adds the node labels
            $this->_files[ $path ][] = '        <node_info_name>' .  $this->_formValues[ 'kickstarter_node_title' ]  . '</node_info_name>';
            $this->_files[ $path ][] = '        <node_info_description>' .  $this->_formValues[ 'kickstarter_node_description' ]  . '</node_info_description>';
        }
        
        // Checks if an administration settings page has been added
        if( $this->_formValues[ 'kickstarter_admin_add' ] ) {
            
            // Adds the admin labels
            $this->_files[ $path ][] = '        <menu_admin_title>' .  $this->_formValues[ 'kickstarter_admin_title' ]  . '</menu_admin_title>';
            $this->_files[ $path ][] = '        <menu_admin_description>' .  $this->_formValues[ 'kickstarter_admin_description' ]  . '</menu_admin_description>';
        }
        
        // Checks if a menu item has been added
        if( $this->_formValues[ 'kickstarter_menu_add' ] ) {
            
            // Adds the menu labels
            $this->_files[ $path ][] = '        <menu_item_title>' .  $this->_formValues[ 'kickstarter_menu_title' ]  . '</menu_item_title>';
            $this->_files[ $path ][] = '        <menu_item_description>' .  $this->_formValues[ 'kickstarter_menu_description' ]  . '</menu_item_description>';
        }
        
        // Checks if a filter item has been added
        if( $this->_formValues[ 'kickstarter_filter_add' ] ) {
            
            // Adds the menu labels
            $this->_files[ $path ][] = '        <filter_title>' .  $this->_formValues[ 'kickstarter_filter_title' ]  . '</filter_title>';
            $this->_files[ $path ][] = '        <filter_description>' .  $this->_formValues[ 'kickstarter_filter_description' ]  . '</filter_description>';
        }
        
        // Ends the system section
        $this->_files[ $path ][] = '    </system>';
        
        // Starts the settings section
        $this->_files[ $path ][] = '    <settings>';
        
        // Checks if the number of blocks is settable through the admin page
        if( $this->_formValues[ 'kickstarter_admin_add' ]
            && $this->_formValues[ 'kickstarter_admin_blocks_number' ]
        ) {
            
            // Adds the label for the settable number of blocks
            $this->_files[ $path ][] = '        <number_of_blocks_title>Number of blocks</number_of_blocks_title>';
            $this->_files[ $path ][] = '        <number_of_blocks_description>The desired number of blocks. This will allow you to place this block more than once.</number_of_blocks_description>';
        }
        
        // Ends the settings section
        $this->_files[ $path ][] = '    </settings>';
        
        // Starts the module section
        $this->_files[ $path ][] = '    <module>';
        
        // Checks if the number of blocks is settable through the admin page
        if( $this->_formValues[ 'kickstarter_admin_add' ]
            && $this->_formValues[ 'kickstarter_admin_blocks_number' ]
        ) {
            
            // Adds the error message for a non-numeric value
            $this->_files[ $path ][] = '        <blocksNumberNotNumeric>The number of blocks must be a numeric value</blocksNumberNotNumeric>';
        }
        
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
    {
        // Files to create
        $files = array();
        
        // Checks if a block has been added
        if( $this->_formValues[ 'kickstarter_block_add' ] && $this->_formValues[ 'block_add_config' ] ) {
            
            // Config file path
            $files[] = $this->_moduleSettingsDir
                     . DIRECTORY_SEPARATOR
                     . 'block.0.form.php';
        }
        
        // Checks if a node has been added
        if( $this->_formValues[ 'kickstarter_node_add' ] ) {
            
            // Config file path
            $files[] = $this->_moduleSettingsDir
                     . DIRECTORY_SEPARATOR
                     . 'node.form.php';
        }
        
        // Process each file
        foreach( $files as $path ) {
            
            // Storage array
            $this->_files[ $path ] = array();
            
            // Starts the file
            $this->_files[ $path ][] = '<?php';
            $this->_files[ $path ][] = '';
            
            // Checks if we want CVS support
            if( $this->_formValues[ 'kickstarter_infos_cvs' ] ) {
                
                // Adds the CVS ID variable
                $this->_files[ $path ][] = '# $' . 'Id' . '$';
                $this->_files[ $path ][] = '';
            }
            
            // Adds the configuration array
            $this->_files[ $path ][] = '// Drupal form configuration array';
            $this->_files[ $path ][] = '$formConf = array();';
            $this->_files[ $path ][] = '';
        }
        
        // Checks if an administration settings page has been added
        if( $this->_formValues[ 'kickstarter_admin_add' ] ) {
            
            // Config file path
            $adminConfigPath = $this->_moduleSettingsDir
                            . DIRECTORY_SEPARATOR
                            . 'admin.form.php';
            
            // Checks if the blocks number is settable
            if( $this->_formValues[ 'kickstarter_admin_add' ]
                && $this->_formValues[ 'kickstarter_admin_blocks_number' ]
            ) {
                
                // Starts the file
                $this->_files[ $adminConfigPath ][] = '<?php';
                $this->_files[ $adminConfigPath ][] = '';
                
                // Checks if we want CVS support
                if( $this->_formValues[ 'kickstarter_infos_cvs' ] ) {
                    
                    // Adds the CVS ID variable
                    $this->_files[ $adminConfigPath ][] = '# $' . 'Id' . '$';
                    $this->_files[ $adminConfigPath ][] = '';
                }
                
                // Adds the number of blocks field
                $this->_files[ $adminConfigPath ][] = '// Drupal form configuration array';
                $this->_files[ $adminConfigPath ][] = '$formConf = array(';
                $this->_files[ $adminConfigPath ][] = '        \'number_of_blocks\' => array(';
                $this->_files[ $adminConfigPath ][] = '        \'#type\'          => \'textfield\',';
                $this->_files[ $adminConfigPath ][] = '        \'#default_value\' => \'1\',';
                $this->_files[ $adminConfigPath ][] = '        \'#size\'          => 5,';
                $this->_files[ $adminConfigPath ][] = '        \'#maxlength\'     => 5,';
                $this->_files[ $adminConfigPath ][] = '        \'#required\'      => true';
                $this->_files[ $adminConfigPath ][] = '    )';
                $this->_files[ $adminConfigPath ][] = ');';
                $this->_files[ $adminConfigPath ][] = '';
                
            } else {
                
                // Starts the file
                $this->_files[ $adminConfigPath ][] = '<?php';
                $this->_files[ $adminConfigPath ][] = '';
                
                // Checks if we want CVS support
                if( $this->_formValues[ 'kickstarter_infos_cvs' ] ) {
                    
                    // Adds the CVS ID variable
                    $this->_files[ $adminConfigPath ][] = '# $' . 'Id' . '$';
                    $this->_files[ $adminConfigPath ][] = '';
                }
                
                // Adds the configuration array
                $this->_files[ $adminConfigPath ][] = '// Drupal form configuration array';
                $this->_files[ $adminConfigPath ][] = '$formConf = array();';
                $this->_files[ $adminConfigPath ][] = '';
            }
        }
    }
    
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
            
            // Checks if we want CVS support
            if( $this->_formValues[ 'kickstarter_infos_cvs' ] ) {
                
                // Adds the CVS ID variable
                $this->_files[ $path ][] = '/* $' . 'Id' . '$ */';
                $this->_files[ $path ][] = '';
            }
            
            // Ends the file
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
            
            // Checks if we want CVS support
            if( $this->_formValues[ 'kickstarter_infos_cvs' ] ) {
                
                // Adds the CVS ID variable
                $this->_files[ $path ][] = '// $' . 'Id' . '$';
                $this->_files[ $path ][] = '';
            }
            
            // Registers the module class
            $this->_files[ $path ][] = '// Registers the class for the \'' . $this->_moduleName . '\' module';
            $this->_files[ $path ][] = 'oopManager.getInstance().registerModuleClass(';
            $this->_files[ $path ][] = '    \'' . $this->_moduleName . '\',';
            $this->_files[ $path ][] = '    function()';
            $this->_files[ $path ][] = '    {';
            $this->_files[ $path ][] = '        /**';
            $this->_files[ $path ][] = '         * Place the JavaScript methods for your module here.';
            $this->_files[ $path ][] = '         * For instance:';
            $this->_files[ $path ][] = '         * ';
            $this->_files[ $path ][] = '         * this.sayHello = function()';
            $this->_files[ $path ][] = '         * {';
            $this->_files[ $path ][] = '         *     alert( \'Hello world!\' );';
            $this->_files[ $path ][] = '         * }';
            $this->_files[ $path ][] = '         * ';
            $this->_files[ $path ][] = '         * You will then be able to access the module class by using the';
            $this->_files[ $path ][] = '         * OOP JavaScript class manager.';
            $this->_files[ $path ][] = '         * For instance:';
            $this->_files[ $path ][] = '         * ';
            $this->_files[ $path ][] = '         * oopManager.getInstance().getModule( \'' . $this->_moduleName . '\' ).sayHello();';
            $this->_files[ $path ][] = '         */';
            $this->_files[ $path ][] = '    }';
            $this->_files[ $path ][] = ');';
            
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
            
            // Adds the intro text
            $content->div->strong = $this->_lang->introTitle;
            $content->div         = $this->_lang->introText;
            
            // Adds a spacer
            $content->spacer( 10 );
            
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
    public function validateForm( array $form, array &$formState )
    {
        // Checks for a module name
        if( $formState[ 'values' ][ 'kickstarter_infos_name' ] ) {
            
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
                form_set_error( 'kickstarter_infos_name', sprintf( $this->_lang->dirExists, $path ) );
            }
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
        
        // Checks for a database table
        if( $formState[ 'values' ][ 'kickstarter_table_add' ] && !preg_match( '/^[a-zA-Z0-9_]+$/', $formState[ 'values' ][ 'kickstarter_table_name' ] ) ) {
            
            // Error - Invalid table name
            form_set_error( 'kickstarter_table_name', $this->_lang->invalidTableName );
        }
    }
    
    /**
     * 
     */
    public function submitForm( $formId, $formValues )
    {
        // Stores the submitted values
        $this->_formValues         =& $formValues[ 'values' ];
        
        // Path to the module directory
        $this->_moduleDir          = self::$_classManager->getDrupalPath()
                                   . 'sites'
                                   . DIRECTORY_SEPARATOR
                                   . 'all'
                                   . DIRECTORY_SEPARATOR
                                   . 'modules'
                                   . DIRECTORY_SEPARATOR
                                   . $this->_formValues[ 'kickstarter_infos_name' ];
        
        // Path to the lang directory
        $this->_moduleLangDir      = $this->_moduleDir
                                   . DIRECTORY_SEPARATOR
                                   . 'lang';
        
        // Path to the settings directory
        $this->_moduleSettingsDir  = $this->_moduleDir
                                   . DIRECTORY_SEPARATOR
                                   . 'settings';
        
        // Path to the templates directory
        $this->_moduleTemplatesDir = $this->_moduleDir
                                   . DIRECTORY_SEPARATOR
                                   . 'templates';
        
        // Name of the module to write
        $this->_moduleName         = $this->_formValues[ 'kickstarter_infos_name' ];
        
        // Checks if the directories can be created
        if( file_exists( $this->_moduleDir ) ) {
            
            // Error - The directory already exists
            drupal_set_message( sprintf( $this->_lang->dirExists, $this->_moduleDir ) );
            
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
            
            // Error state
            $error = false;
            
            // Process each file
            foreach( $this->_files as $path => &$lines ) {
                
                // Tries to write the file
                if( !file_put_contents( $path, implode( self::$_NL, $lines ) ) ) {
                    
                    // Error - impossible to write the current file
                    drupal_set_message( sprintf( $this->_lang->cannotCreateFile, $path ) );
                    $error = true;
                    break;
                }
            }
            
            // Checks for an error
            if( !$error ) {
                
                // Displays the success message
                drupal_set_message( sprintf( $this->_lang->moduleCreated, $this->_moduleDir ) );
            }
        }
    }
    
    /**
     * Adds items to the Drupal menu
     * 
     * @param   array   An array in which to place the menu items, passed by reference. It may contains existing menu items, for instance if an administration settings form exists
     * @return  NULL
     */
     public function addMenuItems( array &$items )
    {
        // Adds the menu item for the kickstarter in the admin pages
        $items[ 'admin/build/oopkickstarter' ] = array(
            'title'            => $this->_lang->getLabel( 'admin_build_oopkickstarter_title', 'system' ),
            'description'      => $this->_lang->getLabel( 'admin_build_oopkickstarter_description', 'system' ),
            'page callback'    => 'kickstarter_show',
            'access arguments' => array( 'access kickstarter admin/build/oopkickstarter' )
        );
        
        // Returns the menu items
        return $items;
    }
}
