<?php

/**
 * Abstract for the Drupal modules
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Drupal
 * @version         0.1
 */
abstract class Oop_Drupal_ModuleBase
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
     * Abstract method to get the 'view' section of the modules
     */
    abstract protected function _getView( Oop_Html_Tag $content, $delta );
    
    /**
     * Wether the static variables are set or not
     */
    protected static $_hasStatic      = false;
    
    /**
     * Whether the Mootools JS framework has been included
     */
    private static $_hasMootools      = false;
    
    /**
     * Whether the Prototype JS framework has been included
     */
    private static $_hasPrototype     = false;
    
    /**
     * Whether the Prototype JS framework has been included
     */
    private static $_hasScriptaculous = false;
    
    /**
     * Whether the JS file for the current module has been included
     */
    private $_hasScriptFile           = false;
    
    /**
     * Whether the CSS file for the current module has been included
     */
    private $_hasCssFile              = false;
    
    /**
     * The instance of the class manager
     */
    protected static $_classManager   = NULL;
    
    /**
     * The instance of the database class
     */
    protected static $_db             = NULL;
    
    /**
     * An array with the Drupal permission for the module
     */
    protected static $_perms          = array();
    
    /**
     * The new line character
     */
    protected static $_NL             = '';
    
    /**
     * The language object for the module
     */
    protected $_lang                  = NULL;
    
    /**
     * The full (absolute) path of the module
     */
    protected $_modPath               = '';
    
    /**
     * The name of the module
     */
    protected $_modName               = '';
    
    /**
     * Class constructor
     * 
     * @param   string  The path of the module
     * @return  NULL
     * @see     Oop_Drupal_Database::getInstance
     * @see     Oop_Lang_Getter::getInstance
     */
    public function __construct( $modPath )
    {
        // Sets the module path
        $this->_modPath = $modPath;
        
        // Sets the module name
        $this->_modName = get_class( $this );
            
        // Gets the instance of the database class
        $this->_lang    = Oop_Lang_Getter::getInstance( $this->_modName );
        
        // Checks if the static variables are set
        if( !self::$_hasStatic ) {
            
            // Sets the static variables
            self::_setStaticVars();
        }
    }
    
    /**
     * Sets the needed static variables
     * 
     * @return  NULL
     */
    protected static function _setStaticVars()
    {
        // Gets the instance of the database class
        self::$_db           = Oop_Drupal_Database::getInstance();
        
        // Gets the instance of the class manager
        self::$_classManager = Oop_Core_ClassManager::getInstance();
        
        // Sets the new line character
        self::$_NL           = chr( 10 );
    }
    
    /**
     * Includes the Mootools JS framework
     * 
     * @return NULL
     */
    protected function _includeMootools()
    {
        // Only includes the script once
        if( !self::$_hasMootools ) {
            
            // Adds the JS script
            drupal_add_js(
                drupal_get_path( 'module', 'oop' )
              . '/ressources/javascript/mootools/mootools.js',
                'module'
            );
        }
        
        // Script has been included
        self::$_hasMootools = true;
    }
    
    /**
     * Includes the Prototype JS framework
     * 
     * @return NULL
     */
    protected function _includePrototype()
    {
        // Only includes the script once
        if( !self::$_hasPrototype ) {
            
            // Adds the JS script
            drupal_add_js(
                drupal_get_path( 'module', 'oop' )
              . '/ressources/javascript/prototype/prototype.js',
                'module'
            );
        }
        
        // Script has been included
        self::$_hasPrototype = true;
    }
    
    /**
     * Includes the Scriptaculous JS framework
     * 
     * @return NULL
     */
    protected function _includeScriptaculous()
    {
        // Only includes the script once
        if( !self::$_hasScriptaculous ) {
            
            // Includes the Prototype JS framework
            $this->_includePrototype();
            
            // Adds the JS script
            drupal_add_js(
                drupal_get_path( 'module', 'oop' )
              . '/ressources/javascript/scriptaculous/scriptaculous.js',
                'module'
            );
        }
        
        // Script has been included
        self::$_hasScriptaculous = true;
    }
    
    /**
     * Includes the script file for the current module
     * 
     * @return NULL
     */
    protected function _includeModuleScript()
    {
        // Only includes the script once
        if( !$this->_hasScriptFile ) {
            
            // Adds the JS script
            drupal_add_js(
                drupal_get_path( 'module', $this->_modName )
              . '/' . $this->_modName . '.js',
                'module'
            );
        }
        
        // Script has been included
        $this->_hasScriptFile = true;
    }
    
    /**
     * Includes the CSS file for the current module
     * 
     * @return NULL
     */
    protected function _includeModuleCss()
    {
        // Only includes the script once
        if( !$this->_hasCssFile ) {
            
            // Adds the JS script
            drupal_add_css(
                drupal_get_path( 'module', $this->_modName )
              . '/' . $this->_modName . '.css',
                'module'
            );
        }
        
        // CSS have been included
        $this->_hasCssFile = true;
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
                return '<p>' . $this->_lang->help . '</p>';
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
     * @param   string  The kind of block to display
     * @param   int     The delta offset, used to generate different contents for different blocks
     */
    public function block( $op = 'list', $delta = 0 )
    {
        // Storage
        $block = array();
        
        // Checks the block type
        if( $op === 'list' ) {
            
            // Returns the help text
            $block[0] = array(
                'info' => $this->_lang->help
            );
            
        } elseif( $op === 'view' ) {
            
            // Creates the storage tag for the module
            $content            = new Oop_Html_Tag( 'div' );
            
            // Adds the base CSS class
            $content[ 'class' ] = 'module-' . $this->_modName;
            
            // Gets the 'view' section from the child class
            $this->_getView( $content, $delta );
            
            // Adds the title and the content, wrapped in HTML comments
            $block['subject'] = $this->_lang->blockSubject; 
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
}
