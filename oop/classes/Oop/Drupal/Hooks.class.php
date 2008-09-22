<?php

# $Id$

/**
 * Abstract for the Drupal modules which defines the hooks
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Drupal
 * @version         0.1
 */
abstract class Oop_Drupal_Hooks extends Oop_Drupal_Module
{
    /**
     * An array with the Drupal permission for the module
     */
    protected $_perms          = array();
    
    /**
     * The number of blocks available
     */
    protected $_numberOfBlocks = 1;
    
    /**
     * Wheter the blocks have identical properties (for the labels, configuration, etc)
     */
    protected $_sameBlocks     = false;
    
    /**
     * Checks if a method is defined in a module
     * 
     * @param   string                      The name of the method to check
     * @return  NULL
     * @throws  Oop_Drupal_Hook_Exceptions  If the method does not exist
     */
    private function _checkMethod( $name )
    {
        // Checks for the method
        if( !method_exists( $this, $name ) ) {
            
            // The method does not exist
            throw new Oop_Drupal_Hooks_Exception( 'The required method ' . $name . ' is not defined in the class of module ' . $this->_modName, Oop_Drupal_Hooks_Exception::EXCEPTION_NO_METHOD );
        }
    }
    
    /**
     * 
     */
    public function createModuleContent( $callbackMethod, array $args = array() )
    {
        // Checks if the current module is an override
        $override = self::$_classManager->isOverride( $this->_modName );
        
        // Name of the module, to support the overrides
        $modName  = ( $override ) ? $override : $this->_modName;
        
        // Checks the callback method
        $this->_checkMethod( $callbackMethod );
        
        // Checks the callback method to set the CSS class name
        if( $callbackMethod === 'getBlock' ) {
            
            // CSS class - Block content
            $cssClass = 'module-' . $modName . '-block';
            
        } elseif( $callbackMethod === 'getNode' ) {
            
            // CSS class - Node content
            $cssClass = 'module-' . $modName . '-node';
            
        } else {
            
            // CSS class - Custom content
            $cssClass = 'module-' . $modName;
        }
        
        // Creates the storage tag for the module
        $content            = new Oop_Xhtml_Tag( 'div' );
        
        // Adds the base CSS class
        $content[ 'class' ] = $cssClass;
        
        // Adds the content object to the arguments
        array_unshift( $args, $content );
        
        // Calls the callback method
        Oop_Callback_Helper::apply(
            array(
                $this,
                $callbackMethod
            ),
            $args
        );
        
        // Returns the full content
        return self::$_NL
             . self::$_NL
             . '<!-- Start of module \'' . $this->_modName . '\' -->'
             . self::$_NL
             . self::$_NL
             . $content
             . self::$_NL
             . self::$_NL
             . '<!-- End of module \'' . $this->_modName . '\' -->'
             . self::$_NL
             . self::$_NL;
    }
    
    /**
     * Sets the number of available blocks
     * 
     * @param   int     The desired number of blocks
     * @param   boolean Wheter the new blocks must be the same as the first one (for the labels, configuration, etc)
     * @return  NULL
     */
    public function setNumberOfBlocks( $number, $sameAsFirst = false )
    {
        $this->_numberOfBlocks   = ( int )$number;
        $this->_sameBlocks       = ( boolean )$sameAsFirst;
    }
    
    /**
     * Drupal 'help' hook
     * 
     * @param   string  The path for which to display help
     * @param   array   An array that holds the current path as would be returned from arg() function
     * @return  string  The help text for the Drupal module
     */
    public function help( $path, $arg )
    {
        // Checks if the current module is an override
        $override = self::$_classManager->isOverride( $this->_modName );
        
        // Name of the module, to support the overrides
        $modName  = ( $override ) ? $override : $this->_modName;
        
        // Checks the path
        switch( $path ) {
            
            // Admin help
            case 'admin/help#' . $modName:
                
                // Returns the localized help text
                return '<p>' . $this->_lang->getLabel( 'help', 'system' ) . '</p>';
                break;
        }
        
        return '';
    }
    
    /**
     * Drupal 'node_info' hook
     * 
     * @return array    The information array for the Drupal node
     */
    public function node_info()
    {
        // Checks if the current module is an override
        $override = self::$_classManager->isOverride( $this->_modName );
        
        // Name of the module, to support the overrides
        $modName  = ( $override ) ? $override : $this->_modName;
        
        return array(
            $modName => array(
                'name'        => $this->_lang->getLabel( 'node_info_name', 'system' ),
                'module'      => $modName,
                'description' => $this->_lang->getLabel( 'node_info_description', 'system' )
            )
        );
    } 
    
    /**
     * Drupal 'perm' hook
     * 
     * @return array    The permissions array for the Drupal module
     */
    public function perm()
    {
        return $this->_perms;
    }
    
    /**
     * Drupal 'access' hook
     * 
     * @return boolean
     */
    public function access( $op, $node, $account )
    {
        // Checks if the current module is an override
        $override = self::$_classManager->isOverride( $this->_modName );
        
        // Name of the module, to support the overrides
        $modName  = ( $override ) ? $override : $this->_modName;
        
        // By default, access is granted for everything
        $access   = true;
        
        // Checks the operation
        if( $op === 'create' ) {
            
            // Checks for a node creation permission
            if( in_array( 'create ' . $modName . ' node', $this->_perms ) ) {
                
                // Checks the access for the node creation
                $access = user_access( 'create ' . $modName . ' node' );
            }
            
        } elseif( $op === 'update' || $op === 'delete' ) {
            
            // Checks if the current user has created the current node
            if( $GLOBALS[ 'user' ]->uid === $node->uid ) {
                
                // Checks for a own node edit permission
                if( in_array( 'edit own ' . $modName . ' node', $this->_perms ) ) {
                    
                    // Checks the access for the own node edition
                    $access = user_access( 'edit own ' . $modName . ' node' );
                }
                
            } else {
                
                // Checks for a node edit permission
                if( in_array( 'edit ' . $modName . ' node', $this->_perms ) ) {
                    
                    // Checks the access for the node edition
                    $access = user_access( 'edit ' . $modName . ' node' );
                }
            }
            
        } elseif( $op === 'view' ) {
            
            // Checks for a node view permission
            if( in_array( 'access ' . $modName . ' node', $this->_perms ) ) {
                
                // Checks the access for the node view
                $access = user_access( 'access ' . $modName . ' node' );
            }
        }
        
        // Returns the access
        return $access;
    }
    
    /**
     * Drupal 'form' hook
     * 
     * @param   stdClass    The node object
     * @param   boolean     Wheter to add the title field or not
     * @return  array       An array with the form configuration
     */
    public function form( stdClass $node, $addTitle = true )
    {
        // Checks if the current module is an override
        $override = self::$_classManager->isOverride( $this->_modName );
        
        // Name of the module, to support the overrides
        $modName  = ( $override ) ? $override : $this->_modName;
        
        // Gets the path of the configuration file
        $confPath  = self::$_classManager->getModulePath( $this->_modName )
                   . 'settings'
                   . DIRECTORY_SEPARATOR
                   . 'node.form.php';
        
        // Checks if we are in an override and if we have to take the original form
        if( $override && !file_exists( $confPath ) ) {
            
            // Original configuration file
            $confPath  = self::$_classManager->getModulePath( $override )
                       . 'settings'
                       . DIRECTORY_SEPARATOR
                       . 'node.form.php';
        }
        
        // Creates the form
        $form      = new Oop_Drupal_Form_Builder( $confPath, $modName, $this->_lang );
        
        // Gets the node type
        $type      = node_get_types( 'type', $node );
        
        // Gets the form configuration array
        $finalForm = $form->getConf();
        
        // Checks if the title field mus be added
        if( $addTitle ) {
            
            // Adds the title field
            $finalForm[ 'title' ] = array(
                '#type'          => 'textfield',
                '#title'         => check_plain( $type->title_label ),
                '#required'      => true,
                '#default_value' => $node->title,
                '#weight'        => -5
            );
        }
        
        // Returns the form
        return $finalForm;
    }
    
    /**
     * Drupal 'insert' hook
     * 
     * @param   stdClass    The node object
     * @return  NULL
     */
    public function insert( stdClass $node )
    {
        // Checks if the current module is an override
        $override = self::$_classManager->isOverride( $this->_modName );
        
        // Name of the module, to support the overrides
        $modName  = ( $override ) ? $override : $this->_modName;
        
        // Process the node properties
        foreach( $node as $key => $value ) {
            
            // Checks the property name
            if( substr( $key, 0, strlen( $modName ) + 1 ) === $modName . '_' ) {
                
                // Gets the variable short name
                $varName = substr( $key, strlen( $modName ) + 1 );
                
                // Sets the variable
                $this->_storeModuleVar( $varName, $value );
            }
        }
    }
    
    /**
     * Drupal 'update' hook
     * 
     * @param   stdClass    The node object
     * @return  NULL
     */
    public function update( stdClass $node )
    {
        // Checks if the current module is an override
        $override = self::$_classManager->isOverride( $this->_modName );
        
        // Name of the module, to support the overrides
        $modName  = ( $override ) ? $override : $this->_modName;
        
        // Process the node properties
        foreach( $node as $key => $value ) {
            
            // Checks the property name
            if( substr( $key, 0, strlen( $modName ) + 1 ) === $modName . '_' ) {
                
                // Gets the variable short name
                $varName = substr( $key, strlen( $modName ) + 1 );
                
                // Sets the variable
                $this->_storeModuleVar( $varName, $value );
            }
        }
    }
    
    /**
     * Drupal 'view' hook
     * 
     * @param   stdClass    The node object
     * @param   boolean     Wheter a teaser must be generated instead of the full content
     * @param   boolean     Whether the node is being displayed as a standalone page
     * @return  stdClass    The node object
     * @throws  Oop_Drupal_Hooks_Exception  If the method getNode() is not defined in the module class
     */
    public function view( stdClass $node, $teaser, $page)
    {
        // Prepares the node
        $node = node_prepare( $node, $teaser );
        
        // Creates the module content
        $content            = $this->createModuleContent(
            'getNode',
            array(
                $node,
                $teaser,
                $page
            )
        );
        
        // Adds the title and the content, wrapped in HTML comments
        $node->content[ 'body' ][ '#value' ] = $content;
        
        // Returns the node
        return $node;
    }
    
    /**
     * Drupal 'block' hook
     * 
     * @param   string                      The kind of block to display
     * @param   int                         The delta offset, used to generate different contents for different blocks
     * @param   array                       The edited items (only if $op is 'save')
     * @return  array                       The Drupal block
     * @throws  Oop_Drupal_Hooks_Exception  If the method getBlock() is not defined in the module class
     * @see     _checkMethod
     */
    public function block( $op, $delta, $edit )
    {
        // Storage
        $block = array();
        
        // Checks the operation to perform
        if( $op === 'list' ) {
            
            // Repeat for each available block
            for( $i = 0; $i < $this->_numberOfBlocks; $i++ ) {
                
                // Index for the label
                $langIndex = ( $this->_sameBlocks ) ? 0 : $i;
                
                // Returns the help text
                $block[ $i ] = array(
                    'info' => $this->_lang->getLabel( 'block_' . $langIndex . '_info', 'system' )
                );
            }
        
        } elseif( $op === 'configure' ) {
            
            // Checks if the current module is an override
            $override = self::$_classManager->isOverride( $this->_modName );
            
            // Name of the module, to support the overrides
            $modName  = ( $override ) ? $override : $this->_modName;
            
            // By default, access is granted to the block
            $access   = true;
            
            // Checks for a block permission
            if( in_array( 'access ' . $modName . ' block config', $this->_perms ) ) {
                
                // Checks the access for the block
                $access = user_access( 'access ' . $modName . ' block config' );
            }
            
            // Checks the access
            if( $access ) {
                
                // Index for the forms
                $formIndex = ( $this->_sameBlocks ) ? 0 : $delta;
                
                // Gets the path of the configuration file
                $confPath = self::$_classManager->getModulePath( $this->_modName )
                          . 'settings'
                          . DIRECTORY_SEPARATOR
                          . 'block.' . $formIndex . '.form.php';
                
                // Checks if we are in an override and if we have to take the original form
                if( $override && !file_exists( $confPath ) ) {
                    
                    // Original configuration file
                    $confPath  = self::$_classManager->getModulePath( $override )
                               . 'settings'
                               . DIRECTORY_SEPARATOR
                               . 'node.form.php';
                }
                
                // Checks for a configuration file
                if( file_exists( $confPath ) ) {
                    
                    // Creates the form
                    $form  = new Oop_Drupal_Form_Builder( $confPath, $modName, $this->_lang, $delta );
                    
                    // Returns the form
                    $block = $form->getConf();
                }
            }
            
        } elseif( $op === 'save' ) {
            
            // Checks if the current module is an override
            $override = self::$_classManager->isOverride( $this->_modName );
            
            // Name of the module, to support the overrides
            $modName  = ( $override ) ? $override : $this->_modName;
            
            // Process each item
            foreach( $edit as $key => $value ) {
                
                // Checks the property name
                if( substr( $key, 0, strlen( $modName ) + 1 ) === $modName . '_' ) {
                    
                    // Gets the variable short name
                    $varName = substr( $key, strlen( $modName ) + 1 );
                    
                    // Sets the variable
                    $this->_storeModuleVar( $varName, $value );
                }
            }
            
        } elseif( $op === 'view' ) {
            
            // Checks if the current module is an override
            $override = self::$_classManager->isOverride( $this->_modName );
            
            // Name of the module, to support the overrides
            $modName  = ( $override ) ? $override : $this->_modName;
            
            // By default, access is granted to the block
            $access = true;
            
            // Checks for a block permission
            if( in_array( 'access ' . $modName . ' block', $this->_perms ) ) {
                
                // Checks the access for the block
                $access = user_access( 'access ' . $modName . ' block' );
            }
            
            // Checks the access
            if( $access ) {
                
                // Index for the label
                $langIndex = ( $this->_sameBlocks ) ? 0 : $delta;
                
                // Creates the module content
                $content   = $this->createModuleContent(
                    'getBlock',
                    array(
                        $delta
                    )
                );
                
                // Adds the title and the content, wrapped in HTML comments
                $block['subject'] = $this->_lang->getLabel( 'block_' . $langIndex . '_subject', 'system' ); 
                $block['content'] = $content;
            }
        }
        
        // Returns the block
        return $block;
    }
    
    /**
     * Drupal 'filter' hook
     * 
     * @param   string                      Which filtering operation to perform
     * @param   int                         Which of the module's filters to use
     * @param   int                         Which input format the filter is being used
     * @param   string                      The content to filter
     * @return  mixed                       Depends on $op
     * @throws  Oop_Drupal_Hooks_Exception  If the method prepareFilter() is not defined in the module class
     * @throws  Oop_Drupal_Hooks_Exception  If the method processFilter() is not defined in the module class
     * @see     _checkMethod
     */
    public function filter( $op, $delta, $format, $text )
    {
        // Checks the operation to perform
        if( $op === 'list' ) {
            
            // Returns the filter title
            return array(
                $this->_lang->getLabel( 'filter_title', 'system' )
            );
            
        } elseif( $op === 'description' ) {
            
            // Returns the filter description
            return $this->_lang->getLabel( 'filter_description', 'system' );
            
        } elseif( $op === 'prepare' ) {
            
            // Checks the prepare method
            $this->_checkMethod( 'prepareFilter' );
            
            // Prepares the filter
            return Oop_Callback_Helper::apply(
                array(
                    $this,
                    'prepareFilter'
                ),
                array(
                    $delta,
                    $format,
                    $text
                )
            );
            
        } elseif( $op === 'process' ) {
            
            // Process the filter
            $this->_checkMethod( 'processFilter' );
            
            // Prepares the filter
            return Oop_Callback_Helper::apply(
                array(
                    $this,
                    'processFilter'
                ),
                array(
                    $delta,
                    $format,
                    $text
                 )
             );
        }
        
        // Returns the text
        return $text;
    }
    
    /**
     * Adds a section in admin/settings pages for the current module
     * 
     * @return  array   The menu items array
     */
    public function addAdminSettingsMenu()
    {
        // Checks if the current module is an override
        $override   = self::$_classManager->isOverride( $this->_modName );
        
        // Name of the module, to support the overrides
        $modName    = ( $override ) ? $override : $this->_modName;
        
        // Access arguments
        $accessArgs = ( in_array( 'access ' . $modName . ' admin', $this->_perms ) ) ? 'access ' . $modName . ' admin' : 'access administration pages';
        
        // Storage
        $items = array();
        
        // Creates the item array
        $items[ 'admin/settings/' . $modName ] = array(
            'title'            => $this->_lang->getLabel( 'menu_admin_title', 'system' ),
            'description'      => $this->_lang->getLabel( 'menu_admin_description', 'system' ),
            'page callback'    => 'drupal_get_form',
            'page arguments'   => array( $modName . '_adminForm' ),
            'access arguments' => array( $accessArgs ),
            'type'             => MENU_NORMAL_ITEM
        );
        
        // Returns the items array
        return $items;
    }
    
    /**
     * 
     */
    public function getAdminForm()
    {
        // Checks if the current module is an override
        $override = self::$_classManager->isOverride( $this->_modName );
        
        // Name of the module, to support the overrides
        $modName  = ( $override ) ? $override : $this->_modName;
        
        // Gets the path of the configuration file
        $confPath  = self::$_classManager->getModulePath( $this->_modName )
                   . 'settings'
                   . DIRECTORY_SEPARATOR
                   . 'admin.form.php';
        
        // Checks if we are in an override and if we have to take the original form
        if( $override && !file_exists( $confPath ) ) {
            
            // Original configuration file
            $confPath  = self::$_classManager->getModulePath( $override )
                       . 'settings'
                       . DIRECTORY_SEPARATOR
                       . 'admin.form.php';
        }
        
        // Creates the form
        $form                          = new Oop_Drupal_Form_Builder(
            $confPath,
            $modName,
            $this->_lang
        );
        
        // Gets the final form configuration
        $conf = $form->getConf();
        
        // Adds the submit button
        $conf[ 'buttons' ][ 'submit' ] = array(
            '#type'  => 'submit',
            '#value' => t('Save configuration')
        );
        
        // Adds the reset button
        $conf[ 'buttons' ][ 'reset' ]  = array(
            '#type'  => 'submit',
            '#value' => t('Reset to defaults')
        );
        
        // Checks the POST data and the form errors
        if( !empty($_POST) && form_get_errors() ) {
            
            // Displays the error message
            drupal_set_message(
                t( 'The settings have not been saved because of the errors.' ),
                'error'
            );
        }
        
        // Adds the submit callback
        $conf[ '#submit' ][] = 'oop_submitAdminForm';
        
        // Adds the theming support
        $conf[ '#theme' ]    = 'system_settings_form';
        
        // Returns the form
        return $conf;
    }
    
    /**
     * Submission callback for the administration settings form
     * 
     * @param   array   The form configuration
     * @param   array   The submitted form values
     * @return  NULL
     */
    public function submitAdminForm( array $form, array &$formState )
    {
        // Gets the operation
        $op = isset( $formState[ 'values' ][ 'op' ] ) ? $formState[ 'values' ][ 'op' ] : '';

        // Excludes all unnecessary elements from the array
        unset(
            $formState[ 'values' ][ 'submit' ],
            $formState[ 'values' ][ 'reset' ],
            $formState[ 'values' ][ 'form_id' ],
            $formState[ 'values' ][ 'op' ],
            $formState[ 'values' ][ 'form_token' ],
            $formState[ 'values' ][ 'form_build_id' ]
        );
        
        // Process the form values
        foreach( $formState[ 'values' ] as $key => $value ) {
            
            // Gets the module name
            $modName = substr( $key, 0, strpos( $key, '_' ) );
            
            // Checks the operation
            if( $op === t( 'Reset to defaults' ) ) {
                
                // Deletes all variables from this module
                self::$_utils->deleteModuleVariables( $modName );
                
            } else {
                
                // Checks if we have to filter the array
                if( is_array( $value ) && isset( $formState[ 'values' ][ 'array_filter' ] ) ) {
                    
                    // Filters the array
                    $value = array_keys( array_filter( $value ) );
                }
                
                // Gets the short variable name
                $varName = substr( $key, strpos( $key, '_' ) + 1 );
                
                // Stores the current variable
                self::$_utils->storeModuleVariable( $modName, $varName, $value );
            }
        }
    
        // Checks the operation
        if( $op === t( 'Reset to defaults' ) ) {
            
            // Sets the confirmation message
            drupal_set_message( t( 'The configuration options have been reset to their default values.' ) );
            
        } else {
            
            // Sets the confirmation message
            drupal_set_message(t('The configuration options have been saved.'));
        }
        
        // Clears the Drupal cache
        cache_clear_all();
        
        // Rebuilds the Drupal theme registry
        drupal_rebuild_theme_registry();
    }
}
