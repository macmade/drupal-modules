<?php

# $Id$

/**
 * Abstract for the Drupal modules which provides useful methods
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Oop/Drupal
 * @version         0.1
 */
abstract class Oop_Drupal_ModuleBase extends Oop_Drupal_Hooks
{
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
     * Whether the CSS file for the current module has been included
     */
    private $_cssFiles                = array();
    
    /**
     * The substitution symbol for the @ character
     */
    private static $_emailCryptSymbol = '';
    
    /**
     * Whether the JS file for the current module has been included
     */
    private $_hasScriptFile           = false;
    
    /**
     * Whether the CSS file for the current module has been included
     */
    private $_hasCssFile              = false;
    
    /**
     * Sets the substitution character for the @ symbol
     * 
     * @param   string  The substitution character
     * @return  NULL
     */
    public static function setEmailCryptSymbol( $symbol )
    {
        self::$_emailCryptSymbol = ( string )$symbol;
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
              . 'oop.js',
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
            
            // Includes the OOP JS script
            $this->_includeOopJs();
            
            // Gets the override status
            $override = self::$_classManager->isOverride( $this->_modName );
            
            // Checks if the module is an ovveride
            if( $override ) {
                
                // Adds the base JS script
                drupal_add_js(
                    self::$_classManager->getModuleRelativePath( $override )
                  . $override . '.js',
                    'module'
                );
            }
            
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
     * @param   string  The path to the CSS file (relative to the drupal site)
     * @return  NULL
     */
    protected function _includeCss( $path )
    {
        // Only includes the script once
        if( !isset( $this->_cssFiles[ $path ] ) ) {
            
            // Adds the CSS script
            drupal_add_css( $path );
            
            // CSS file has been include
            $this->_cssFiles[ $path ] = true;
        }
    }
    
    /**
     * Includes a CSS file
     * 
     * @return  NULL
     * @see     Oop_Core_ClassManager::getModuleRelativePath
     */
    protected function _includeModuleCss()
    {
        // Only includes the script once
        if( !$this->_hasCssFile ) {
            
            // Gets the override status
            $override = self::$_classManager->isOverride( $this->_modName );
            
            // Checks if the module is an ovveride
            if( $override ) {
                
                // Adds the base CSS file
                drupal_add_css(
                    self::$_classManager->getModuleRelativePath( $override )
                  . $override . '.css',
                    'module'
                );
            }
            
            // Adds the CSS file
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
    protected function _getIcon( $name, $package = 'famfam' )
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
        // Checks if the current module is an override
        $override = self::$_classManager->isOverride( $this->_modName );
        
        // Name of the module, to support the overrides
        $modName  = ( $override ) ? $override : $this->_modName;
        
        // Adds the CSS class name
        $tag[ 'class' ] = 'module-' . $modName . '-' . $className;
    }
    
    /**
     * Adds an ID for the module on an XHTML tag object
     * 
     * @param   Oop_Xhtml_Tag   The XHTML tag object on which to set the CSS class
     * @param   string          The ID (will be prepended with the module name)
     * @return  NULL
     */
    protected function _id( Oop_Xhtml_Tag $tag, $id )
    {
        // Checks if the current module is an override
        $override = self::$_classManager->isOverride( $this->_modName );
        
        // Name of the module, to support the overrides
        $modName  = ( $override ) ? $override : $this->_modName;
        
        // Adds the CSS class name
        $tag[ 'id' ] = 'module-' . $modName . '-' . $id;
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
            $vars = $this->_reqVars;
            
            // Gets the final URL variables
            $vars = array_merge( $vars, $setVars );
            
        } elseif( is_array( $keepVars ) ) {
            
            // Storage
            $vars = $setVars;
            
            // Process each variable to keep
            foreach( $keepVars as $varName ) {
                
                // Checks if the variable can be added
                if( isset( $this->_reqVars[ $varName ] ) && !isset( $vars[ $varName ] ) ) {
                    
                    // Adds the variable
                    $vars[ $varName ] = &$this->_reqVars[ $varName ];
                }
            }
            
        } else {
            
            // Only add new variables
            $vars = $setVars;
        }
        
        // Checks if the current module is an override
        $override = self::$_classManager->isOverride( $this->_modName );
        
        // Name of the module, to support the overrides
        $modName  = ( $override ) ? $override : $this->_modName;
        
        // Process the URL parameters
        foreach( $vars as $key => $value ) {
            
            // Checks if we have to start the query string
            if( $queryString === false ) {
                
                // Start of the query string
                $url        .= '?' . $modName . '[' . $key . ']=' . urlencode( $value );
                
                // Query string has been started
                $queryString = true;
                
            } else {
                
                // Append the variable to the query string
                $url .= '&' . $modName . '[' . $key . ']=' . urlencode( $value );
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
        $link[ 'href' ] = 'javascript:oopManager.getInstance().getModule( \'oop\' ).decryptEmail( \''
                        . self::$_string->cryptEmail( $email )
                        . '\' );';
        
        // Adds the email text without the @ character
        $link->addTextData( str_replace( '@', self::$_emailCryptSymbol, $email ) );
        
        // Returns the link
        return $link;
    }
}
