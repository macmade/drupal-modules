<?php

# $Id$

/**
 * Abstract for the Drupal modules
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Drupal
 * @version         0.1
 */
abstract class Oop_Drupal_Module
{
    /**
     * Wether the static variables are set or not
     */
    private static $_hasStatic      = false;
    
    /**
     * The instance of the class manager
     */
    protected static $_classManager = NULL;
    
    /**
     * The instance of the database class
     */
    protected static $_db           = NULL;
    
    /**
     * The instance of the request helper
     */
    protected static $_request      = NULL;
    
    /**
     * The instance of the string utilities
     */
    protected static $_string       = NULL;
    
    /**
     * The instance of the Drupal utilities
     */
    protected static $_string       = NULL;
    
    /**
     * The new line character
     */
    protected static $_NL           = '';
    
    /**
     * The language object for the module
     */
    protected $_lang                = NULL;
    
    /**
     * The full (absolute) path of the module
     */
    protected $_modPath             = '';
    
    /**
     * The name of the module
     */
    protected $_modName             = '';
    
    /**
     * The request variables for the module
     */
    public $_reqVars                = array();
    
    /**
     * The database variables for the module
     */
    public $_modVars                = array();
    
    /**
     * Class constructor
     * 
     * @param   string  The path of the module
     * @return  NULL
     * @see     Oop_Lang_Getter::getInstance
     * @see     _setStaticVars
     * @see     _getModuleRequestVariables
     * @see     _getModuleDatabaseVariables
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
        $this->_getModuleRequestVariables();
        
        // Gets the database variables for the current module
        $this->_modVars = self::$_utils->getModuleVariables( $this->_modName );
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
        
        // Gets the instance of the Drupal utilities class
        self::$_utils        = Oop_Drupal_Utils::getInstance();
        
        // Sets the new line character
        self::$_NL           = chr( 10 );
    }
    
    /**
     * Gets each request variable from this module
     * 
     * @return  NULL
     * @see     Oop_Request_Getter::requestVarExists
     * @see     Oop_Request_Getter::getRequestVar
     */
    private function _getModuleRequestVariables()
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
                    if( !isset( $this->_reqVars[ $varName ] ) ) {
                        
                        // Stores the variable
                        $this->_reqVars[ $varName ] =& $value; 
                    }
                }
            }
        }
    }
    
    /**
     * 
     */
    protected function _storeSessionVar( $name, $value )
    {
        if( !isset( $_SESSION[ $this->_modName ] ) ) {
            
            $_SESSION[ $this->_modName ] = array();
        }
        
        $_SESSION[ $this->_modName ][ $name ] = $value;
    }
    
    /**
     * 
     */
    protected function _storeModuleVar( $name, $value )
    {
        return self::$_db->insertRecord(
            'OOP_MODULES_VARIABLES',
            array(
                'module_name'    => $this->_modName,
                'variable_name'  => $name,
                'variable_value' => $value
            )
        );
    }
}
