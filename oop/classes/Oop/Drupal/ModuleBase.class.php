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
     * Wether the static variables are set or not
     */
    private static $_hasStatic        = false;
    
    /**
     * Whether the Mootools JS framework has been included
     */
    private static $_hasMootools      = false;
    
    /**
     * Whether the Prototype JS framework has been included
     */
    private static $_hasPrototype     = false;
    
    /**
     * Whether the Scriptaculous JS framework has been included
     */
    private static $_hasScriptaculous = false;
    
    /**
     * Whether the Oop JS file has been included
     */
    private static $_hasOopJs         = false;
    
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
     * The instance of the request helper
     */
    protected static $_request        = NULL;
    
    /**
     * The instance of the string utilities
     */
    protected static $_string         = NULL;
    
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
     * The request variables for the module
     */
    public $_modVars                  = array();
    
    /**
     * Class constructor
     * 
     * @param   string  The path of the module
     * @return  NULL
     * @see     Oop_Lang_Getter::getInstance
     * @see     _setStaticVars
     * @see     _getModuleVariables
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
        
        // Gets the request variables for the current module
        $this->_getModuleVariables();
    }
    
    /**
     * Sets the needed static variables
     * 
     * @return  NULL
     * @see     Oop_Core_ClassManager::getInstance
     * @see     Oop_Drupal_Database::getInstance
     * @see     Oop_Request_Getter::getInstance
     */
    private static function _setStaticVars()
    {
        // Gets the instance of the class manager
        self::$_classManager = Oop_Core_ClassManager::getInstance();
        
        // Gets the instance of the database class
        self::$_db           = Oop_Drupal_Database::getInstance();
        
        // Gets the instance of the request class
        self::$_request      = Oop_Request_Getter::getInstance();
        
        // Gets the instance of the string utilities class
        self::$_string       = Oop_String_Utils::getInstance();
        
        // Sets the new line character
        self::$_NL           = chr( 10 );
    }
    
    /**
     * Checks if a method is defined in a module
     * 
     * @param   string  The name of the method to check
     * @return  NULL
     * @throws  Oop_Drupal_ModuleBase_Exception If the method does not exist
     */
    private function _checkMethod( $name )
    {
        // Checks for the method
        if( !method_exists( $this, $name ) ) {
            
            // The method does not exist
            throw new Oop_Drupal_ModuleBase_Exception( 'The required method ' . $name . ' is not defined in the class of module ' . $this->_modName, Oop_Drupal_ModuleBase_Exception::EXCEPTION_NO_METHOD );
        }
    }
    
    /**
     * Gets each request variable from this module
     * 
     * @return  NULL
     * @see     Oop_Request_Getter::requestVarExists
     * @see     Oop_Request_Getter::getRequestVar
     */
    private function _getModuleVariables()
    {
        // Keys to search in the request variables
        $requestKeys = array( 'G', 'P', 'C', 'S', 'E' );
        
        // Process each request key
        foreach( $requestKeys as $key ) {
            
            // Checks if a variable corresponding to the module name exist
            if( self::$_request->requestVarExists( $this->_modName, $key ) ) {
                
                // Gets the variable
                $var = self::$_request->getRequestVar( $this->_modName, $key );
                
                // Checks for an array
                if( !is_array( $var ) ) {
                    
                    // Process the next request key
                    continue;
                }
                
                // Process each entry of the array
                foreach( $var as $varName => &$value ) {
                    
                    // Checks if we are processing GET variables
                    if( $key === 'G' ) {
                        
                        // Decodes the value
                        $value = urldecode( $value );
                    }
                    
                    // Only sets the value if the variable does not already exist
                    if( !isset( $this->_modVars[ $varName ] ) ) {
                        
                        // Stores the variable
                        $this->_modVars[ $varName ] =& $value; 
                    }
                }
            }
        }
    }
    
    /**
     * Includes the Mootools JS framework
     * 
     * @return  NULL
     * @see     Oop_Core_ClassManager::getModuleRelativePath
     */
    protected function _includeMootools()
    {
        // Only includes the script once
        if( !self::$_hasMootools ) {
            
            // Adds the JS script
            drupal_add_js(
                self::$_classManager->getModuleRelativePath( 'oop' )
              . 'ressources/javascript/mootools/mootools.js',
                'module'
            );
        }
        
        // Script has been included
        self::$_hasMootools = true;
    }
    
    /**
     * Includes the Prototype JS framework
     * 
     * @return  NULL
     * @see     Oop_Core_ClassManager::getModuleRelativePath
     */
    protected function _includePrototype()
    {
        // Only includes the script once
        if( !self::$_hasPrototype ) {
            
            // Adds the JS script
            drupal_add_js(
                self::$_classManager->getModuleRelativePath( 'oop' )
              . 'ressources/javascript/prototype/prototype.js',
                'module'
            );
        }
        
        // Script has been included
        self::$_hasPrototype = true;
    }
    
    /**
     * Includes the Scriptaculous JS framework
     * 
     * @return  NULL
     * @see     Oop_Core_ClassManager::getModuleRelativePath
     */
    protected function _includeScriptaculous()
    {
        // Only includes the script once
        if( !self::$_hasScriptaculous ) {
            
            // Includes the Prototype JS framework
            $this->_includePrototype();
            
            // Adds the JS script
            drupal_add_js(
                self::$_classManager->getModuleRelativePath( 'oop' )
              . 'ressources/javascript/scriptaculous/scriptaculous.js',
                'module'
            );
        }
        
        // Script has been included
        self::$_hasScriptaculous = true;
    }
    
    /**
     * Includes the Oop JS file
     * 
     * @return  NULL
     * @see     Oop_Core_ClassManager::getModuleRelativePath
     */
    protected function _includeOopJs()
    {
        // Only includes the script once
        if( !self::$_hasOopJs ) {
            
            // Adds the JS script
            drupal_add_js(
                self::$_classManager->getModuleRelativePath( 'oop' )
              . 'ressources/javascript/oop/oop.js',
                'module'
            );
        }
        
        // Script has been included
        self::$_hasOopJs = true;
    }
    
    /**
     * Includes the script file for the current module
     * 
     * @return  NULL
     * @see     Oop_Core_ClassManager::getModuleRelativePath
     */
    protected function _includeModuleScript()
    {
        // Only includes the script once
        if( !$this->_hasScriptFile ) {
            
            // Adds the JS script
            drupal_add_js(
                self::$_classManager->getModuleRelativePath( $this->_modName )
              . $this->_modName . '.js',
                'module'
            );
        }
        
        // Script has been included
        $this->_hasScriptFile = true;
    }
    
    /**
     * Includes the CSS file for the current module
     * 
     * @return  NULL
     * @see     Oop_Core_ClassManager::getModuleRelativePath
     */
    protected function _includeModuleCss()
    {
        // Only includes the script once
        if( !$this->_hasCssFile ) {
            
            // Adds the JS script
            drupal_add_css(
                self::$_classManager->getModuleRelativePath( $this->_modName )
              . $this->_modName . '.css',
                'module'
            );
        }
        
        // CSS have been included
        $this->_hasCssFile = true;
    }
    
    /**
     * Gets the image tag for an icon from th 'oop' module
     * 
     * @param   string                          The name of the icon, including the extension
     * @param   string                          The package of the icon (default is famfam). See 'oop/ressources/icons' for details
     * @return  Oop_Xhtml_Tag                   An image tag for the requested icon
     * @throws  Oop_Drupal_ModuleBase_Exception If the image does not exist
     * @see     Oop_Core_ClassManager::getModulePath
     * @see     Oop_Core_ClassManager::getModuleRelativePath
     */
    protected function getIcon( $name, $package = 'famfam' )
    {
        // Gets the icon path
        $iconPath = self::$_classManager->getModulePath( 'oop' )
                  . 'ressources'
                  . DIRECTORY_SEPARATOR
                  . 'icons'
                  . DIRECTORY_SEPARATOR
                  . $package
                  . DIRECTORY_SEPARATOR
                  . $name;
        
        // Checks if the icon exists
        if( !file_exists( $iconPath ) ) {
            
            // Icon deos not exist
            throw new Oop_Drupal_ModuleBase_Exception( 'The requested icon does not exist (path: ' . $iconPath . ')', Oop_Drupal_ModuleBase_Exception::EXCEPTION_NO_FILE );
        }
        
        // Gets the relative icon path
        $iconRelPath  = self::$_classManager->getModuleRelativePath( 'oop' )
                      . 'ressources/icons/'
                      . $package
                      . '/'
                      . $name;
        
        // Creates the image tag
        $img          = new Oop_Xhtml_Tag( 'img' );
        
        // Adds the source and alt attributes
        $img[ 'src' ] = $GLOBALS[ 'base_path' ] . $iconRelPath;
        $img[ 'alt' ] = substr( $name, 0, strrpos( $name, '.' ) );
        
        // Checks if the icon is readable
        if( is_readable( $iconPath ) ) {
            
            // Gets the image size
            $size = getimagesize( $iconPath );
            
            // Adds the image dimensions
            $img[ 'width' ]  = $size[ 0 ];
            $img[ 'height' ] = $size[ 1 ];
            
        }
        
        // Returns the image tag
        return $img;
    }
    
    /**
     * Adds a CSS class for the module on an XHTML tag object
     * 
     * @param   Oop_Xhtml_Tag   The XHTML tag object on which to set the CSS class
     * @param   string          The CSS class name (will be prepended with the module name)
     * @return  NULL
     */
    protected function _cssClass( Oop_Xhtml_Tag $tag, $className )
    {
        // Adds the CSS class name
        $tag[ 'class' ] = 'module-' . $this->_modName . '-' . $className;
    }
    
    /**
     * Creates a link
     * 
     * @param   string          The text of the link
     * @param   array           The module variables to set, as key/value pairs
     * @param   mixed           If true, all the existing module variables will be kept, if false, no existing variable will be kept, if an array, only the variables corresponding to the array values will be kept
     * @param   string          The target path (if not specified, the current one will be used)
     * @return  Oop_Xhtml_Tag   The link object   
     */
    protected function _link( $text, array $setVars = array(), $keepVars = false, $path = '' )
    {
        // Gets the path (current if not specified)
        $path = ( $path ) ? $path : self::$_request->q;
        
        // Checks if clean URLs are enabled
        if( $GLOBALS[ 'conf' ][ 'clean_url' ] == 1 ) {
            
            // Target URL
            $url         = $GLOBALS[ 'base_path' ] . $path;
            
            // Flag to know if the query string has been started
            $queryString = false;
            
        } else {
            
            // Target URL
            $url         = $GLOBALS[ 'base_path' ] . '?' . $path;
            
            // Flag to know if the query string has been started
            $queryString = true;
        }
        
        // Checks if we have to keep all variables, only some, or none
        if( $keepVars === true ) {
            
            // Keep all variables
            $vars = $this->_modVars;
            
            // Gets the final URL variables
            $vars = array_merge( $vars, $setVars );
            
        } elseif( is_array( $keepVars ) ) {
            
            // Storage
            $vars = $setVars;
            
            // Process each variable to keep
            foreach( $keepVars as $varName ) {
                
                // Checks if the variable can be added
                if( isset( $this->_modVars[ $varName ] ) && !isset( $vars[ $varName ] ) ) {
                    
                    // Adds the variable
                    $vars[ $varName ] = &$this->_modVars[ $varName ];
                }
            }
            
        } else {
            
            // Only add new variables
            $vars = $setVars;
        }
        
        // Process the URL parameters
        foreach( $vars as $key => $value ) {
            
            // Checks if we have to start the query string
            if( $queryString === false ) {
                
                // Start of the query string
                $url        .= '?' . $this->_modName . '[' . $key . ']=' . urlencode( $value );
                
                // Query string has been started
                $queryString = true;
                
            } else {
                
                // Append the variable to the query string
                $url .= '&' . $this->_modName . '[' . $key . ']=' . urlencode( $value );
            }
        }
        
        // Creates the link
        $link           = new Oop_Xhtml_Tag( 'a' );
        $link[ 'href' ] = $url;
        
        // Adds the text
        $link->addTextData( $text );
        
        // Returns the link
        return $link;
    }
    
    /**
     * Creates an encrypted email link
     * 
     * @param   string          The email address
     * @return  Oop_Xhtml_Tag   The link object
     * @see     Oop_String_Utils::cryptEmail
     */
    protected function _email( $email )
    {
        // Creates a link
        $link = new Oop_Xhtml_Tag( 'a' );
        
        // Validates the email address
        if( !valid_email_address( $email ) ) {
            
             // Invalid email address
             $link[ 'href' ] = '#';
             $link->addTextData( $email );
             
             // Returns the link
             return $link;
        }
        
        // Includes the Oop JS file
        $this->_includeOopJs();
        
        // Crypts the email
        $link[ 'href' ] = 'javascript:oop.decryptEmail( \''
                        . self::$_string->cryptEmail( $email )
                        . '\' );';
        
        // Adds the email text without the @ character
        $link->addTextData( str_replace( '@', '(at)', $email ) );
        
        // Returns the link
        return $link;
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
     * @param   string                          The kind of block to display
     * @param   int                             The delta offset, used to generate different contents for different blocks
     * @return  array                           The Drupal block
     * @throws  Oop_Drupal_ModuleBase_Exception If the method _getView() is not defined in the module class
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
                'info' => $this->_lang->help
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
    
    /**
     * Drupal 'filter' hook
     * 
     * @param   string  Which filtering operation to perform
     * @param   int     Which of the module's filters to use
     * @param   int     Which input format the filter is being used
     * @param   string  The content to filter
     * @return  mixed   Depends on $op
     * @throws  Oop_Drupal_ModuleBase_Exception If the method _prepareFilter() is not defined in the module class
     * @throws  Oop_Drupal_ModuleBase_Exception If the method _processFilter() is not defined in the module class
     * @see     _checkMethod 
     */
    public function filter( $op, $delta = 0, $format = -1, $text = '' )
    {
        // Checks the operation to perform
        if( $op === 'list' ) {
            
            // Returns the filter title
            return array(
                $this->_lang->filter
            );
            
        } elseif( $op === 'description' ) {
            
            // Returns the filter description
            return $this->_lang->filterDescription;
            
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
}
