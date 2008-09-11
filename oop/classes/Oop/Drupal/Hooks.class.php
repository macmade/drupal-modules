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
    protected static $_perms = array();
    
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
                return '<p>' . $this->_lang->getSystemLabel( 'block_help' ) . '</p>';
                break;
        }
        
        return '';
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
     * Drupal 'block' hook
     * 
     * @param   string                      The kind of block to display
     * @param   int                         The delta offset, used to generate different contents for different blocks
     * @return  array                       The Drupal block
     * @throws  Oop_Drupal_Hooks_Exception  If the method _getView() is not defined in the module class
     * @see     _checkMethod
     */
    public function block( $op = 'list', $delta = 0 )
    {
        // Storage
        $block = array();
        
        // Checks the operation to perform
        if( $op === 'list' ) {
            
            // Returns the help text
            $block[0] = array(
                'info' => $this->_lang->getSystemLabel( 'block_help' )
            );
            
        } elseif( $op === 'view' ) {
            
            // Checks the view method
            $this->_checkMethod( '_getView' );
            
            // Creates the storage tag for the module
            $content            = new Oop_Xhtml_Tag( 'div' );
            
            // Adds the base CSS class
            $content[ 'class' ] = 'module-' . $this->_modName;
            
            // Gets the 'view' section from the child class
            $this->_getView( $content, $delta );
            
            // Adds the title and the content, wrapped in HTML comments
            $block['subject'] = $this->_lang->getSystemLabel( 'block_subject' ); 
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
    public function filter( $op, $delta = 0, $format = -1, $text = '' )
    {
        // Checks the operation to perform
        if( $op === 'list' ) {
            
            // Returns the filter title
            return array(
                $this->_lang->getSystemLabel( 'filter_title' )
            );
            
        } elseif( $op === 'description' ) {
            
            // Returns the filter description
            return $this->_lang->getSystemLabel( 'filter_description' );
            
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
     * Drupal 'menu' hook
     * 
     * @return  array                       The menu items array
     * @throws  Oop_Drupal_Hooks_Exception  If the method _admin() is not defined in the module class
     * @see     _checkMethod
     */
    public function menu()
    {
        // Storage
        $items = array();
        
        // Checks the admin method
        $this->_checkMethod( '_admin' );
        
        // Creates the item array
        $items[ 'admin/settings/' . $this->_modName ] = array(
            'title'            => $this->_lang->getSystemLabel( 'menu_title' ),
            'description'      => $this->_lang->getSystemLabel( 'menu_description' ),
            'page callback'    => 'drupal_get_form',
            'page arguments'   => array( $this, '_admin' ),
            'access arguments' => array( 'access administration pages' ),
            'type'             => MENU_NORMAL_ITEM,
        );
        
        // Returns the items array
        return $items;
    }
}
