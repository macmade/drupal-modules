<?php

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
     * Class version constants.
     * Holds the version, the developpment state
     * and the PHP lower compatible version.
     */
    const CLASS_VERSION  = '0.1';
    const DEVEL_STATE    = 'alpha';
    const PHP_COMPATIBLE = '5.2.0';
    
    /**
     * An array with the Drupal permission for the module
     */
    protected $_perms      = array();
    
    /**
     * The number of blocks available
     */
    protected $_blockNum   = 1;
    
    /**
     * Wheter the blocks have identical properties (for the labels, configuration, etc)
     */
    protected $_sameBlocks = false;
    
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
     * Sets the number of available blocks
     * 
     * @param   int     The desired number of blocks
     * @param   boolean Wheter the new blocks must be the same as the first one (for the labels, configuration, etc)
     * @return  NULL
     */
    public function setBlocksNumber( $number, $sameAsFirst = false )
    {
        $this->_blockNum   = ( int )$number;
        $this->_sameBlocks = ( boolean )$sameAsFirst;
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
        // Checks the path
        switch( $path ) {
            
            // Admin help
            case 'admin/help#' . $this->_modName:
                
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
        return array(
            $this->_modName => array(
                'name'        => $this->_lang->getLabel( 'node_info_name', 'system' ),
                'module'      => $this->_modName,
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
    public function access( $op, $node )
    {
        // Checks the operation
        if( $op === 'create' ) {
            
            // Only users with permission to do so may create this node type
            return user_access( 'create ' . $this->_modName );
            
        } elseif( $op === 'update' || $op === 'delete' ) {
            
            // Users who create a node may edit or delete it later, assuming they have the necessary permissions
            if( user_access( 'edit own ' . $this->_modName ) && ( $GLOBALS[ 'user' ]->uid === $node->uid ) ) {
                
                // Access granted
                return true;
            }
        }
        
        // No access
        return false;
    }
    
    /**
     * Drupal 'form' hook
     * 
     * @return array    An array with the form configuration
     */
    public function form()
    {
        // Gets the path of the configuration file
        $confPath = self::$_classManager->getModulePath( $this->_modName )
                  . 'settings'
                  . DIRECTORY_SEPARATOR
                  . 'node.form.php';
        
        // Creates the form
        $form = new Oop_Drupal_Form_Builder( $confPath, $this->_modName, $this->_lang );
        
        // Returns the form
        return $form->getConf();
    }
    
    /**
     * Drupal 'view' hook
     * 
     * @param   stdClass    The node object
     * @param   
     * @param   
     * @return  stdClass    The node object
     */
    public function view( stdClass $node, $teaser = false, $page = false )
    {
        // Prepares the node
        $node = node_prepare( $node, $teaser );
        
        // Checks the view method
        $this->_checkMethod( '_getNode' );
        
        // Calls the node view method
        $this->_getNode( $node, $teaser, $page );
        
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
     * @throws  Oop_Drupal_Hooks_Exception  If the method _getView() is not defined in the module class
     * @see     _checkMethod
     */
    public function block( $op, $delta, $edit )
    {
        // Storage
        $block = array();
        
        // Checks the operation to perform
        if( $op === 'list' ) {
            
            // Repeat for each available block
            for( $i = 0; $i < $this->_blockNum; $i++ ) {
                
                // Index for the label
                $langIndex = ( $this->_sameBlocks ) ? 0 : $i;
                
                // Returns the help text
                $block[ $i ] = array(
                    'info' => $this->_lang->getLabel( 'block_' . $langIndex . '_info', 'system' )
                );
            }
        
        } elseif( $op === 'configure' ) {
            
            // Index for the forms
            $formIndex = ( $this->_sameBlocks ) ? 0 : $delta;
            
            // Gets the path of the configuration file
            $confPath = self::$_classManager->getModulePath( $this->_modName )
                      . 'settings'
                      . DIRECTORY_SEPARATOR
                      . 'block.' . $formIndex . '.form.php';
            
            // Checks for a configuration file
            if( file_exists( $confPath ) ) {
                
                // Creates the form
                $form  = new Oop_Drupal_Form_Builder( $confPath, $this->_modName, $this->_lang, $delta );
                
                // Returns the form
                $block = $form->getConf();
            }
            
        } elseif( $op === 'save' ) {
            
            // Process each item
            foreach( $edit as $key => $value ) {
                
                // Sets the variable
                variable_set( $key, $value );
            }
            
        } elseif( $op === 'view' ) {
            
            // Index for the label
            $langIndex = ( $this->_sameBlocks ) ? 0 : $delta;
            
            // Checks the view method
            $this->_checkMethod( '_getBlock' );
            
            // Creates the storage tag for the module
            $content            = new Oop_Xhtml_Tag( 'div' );
            
            // Adds the base CSS class
            $content[ 'class' ] = 'module-' . $this->_modName;
            
            // Gets the 'view' section from the child class
            $this->_getBlock( $content, $delta );
            
            // Adds the title and the content, wrapped in HTML comments
            $block['subject'] = $this->_lang->getLabel( 'block_' . $langIndex . '_subject', 'system' ); 
            $block['content'] = self::$_NL
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
     * @throws  Oop_Drupal_Hooks_Exception  If the method _prepareFilter() is not defined in the module class
     * @throws  Oop_Drupal_Hooks_Exception  If the method _processFilter() is not defined in the module class
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
            $this->_checkMethod( '_prepareFilter' );
            
            // Prepares the filter
            return $this->_prepareFilter( $delta, $format, $text );
            
        } elseif( $op === 'process' ) {
            
            // Process the filter
            $this->_checkMethod( '_processFilter' );
            
            // Prepares the filter
            return $this->_processFilter( $delta, $format, $text );
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
        // Storage
        $items = array();
        
        // Creates the item array
        $items[ 'admin/settings/' . $this->_modName ] = array(
            'title'            => $this->_lang->getLabel( 'menu_admin_title', 'system' ),
            'description'      => $this->_lang->getLabel( 'menu_admin_description', 'system' ),
            'page callback'    => 'drupal_get_form',
            'page arguments'   => array( $this->_modName . '_adminForm' ),
            'access arguments' => array( 'access administration pages' ),
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
        // Gets the path of the configuration file
        $confPath = self::$_classManager->getModulePath( $this->_modName )
                  . 'settings'
                  . DIRECTORY_SEPARATOR
                  . 'admin.form.php';
        
        // Creates the form
        $form = new Oop_Drupal_Form_Builder( $confPath, $this->_modName, $this->_lang );
        
        // Returns the form
        return system_settings_form( $form->getConf() );
    }
}
