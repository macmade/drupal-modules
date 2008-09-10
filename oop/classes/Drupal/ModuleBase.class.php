<?php

/**
 * Abstract for the Drupal modules
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
abstract class Drupal_ModuleBase
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
    abstract protected function _getView( Html_Tag $content, $delta );
    
    /**
     * The instance of the database class
     */
    protected static $_db      = NULL;
    
    /**
     * The language object for the module
     */
    protected static $_lang    = NULL;
    
    /**
     * 
     */
    protected static $_perms   = array();
    
    /**
     * The new line character
     */
    protected static $_NL      = '';
    
    /**
     * 
     */
    protected static $_modPath = '';
    
    /**
     * 
     */
    protected static $_modName = '';
    
    /**
     * 
     */
    public function __construct( $modPath )
    {
        // Checks if the static variables are set
        if( !self::$_modPath ) {
            
            // Sets the module path
            self::$_modPath = $modPath;
            
            // Sets the module name
            self::$_modName = get_class( $this );
            
            // Gets the instance of the database class
            self::$_db      = Drupal_Database::getInstance();
            
            // Gets the instance of the database class
            self::$_lang    = Lang::getInstance( self::$_modName );
            
            // Sets the new line character
            self::$_NL      = chr( 10 );
        }
    }
    
    /**
     * 
     */
    public function help( $path, $arg )
    {
        switch( $path ) {
            
            case 'admin/help#' . self::$_modName:
                
                return '<p>' . self::$_lang->help . '</p>';
                break;
        }
    }
    
    /**
     * 
     */
    public function perm()
    {
        return $this->_perms;
    }
    
    /**
     * 
     */
    public function block( $op = 'list', $delta = 0 )
    {
        $block = array();
        
        if( $op === 'list' ) {
            
            $block[0] = array(
                'info' => self::$_lang->help
            );
            
        } elseif( $op === 'view' ) {
            
            $content            = new Html_Tag( 'div' );
            $content[ 'class' ] = 'module-' . self::$_modName;
            
            $this->_getView( $content, $delta );
            
            $block['subject'] = self::$_lang->blockSubject; 
            $block['content'] = self::$_NL
                              . self::$_NL
                              . '<!-- Start of module \'' . self::$_modName . '\' -->'
                              . self::$_NL
                              . self::$_NL
                              . $content
                              . self::$_NL
                              . self::$_NL
                              . '<!-- End of module \'' . self::$_modName . '\' -->'
                              . self::$_NL
                              . self::$_NL;
        }
        
        return $block;
    }
}
