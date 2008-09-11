<?php

/**
 * Getter class for the request variables
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Oop/Core
 * @version         0.1
 */
final class Oop_Request_Getter
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
     * The unique instance of the class (singleton)
     */
    private static $_instance = NULL;
    
    /**
     * The global lookup order
     */
    private $_lookupOrder     = 'GPCSE';
    
    /**
     * An array with references to $_GET, $_POST, $_COOKIE, $_SESSION and $_ENV
     */
    private $_requestVars     = array();
    
    /**
     * Class constructor
     * 
     * The class constructor is private to avoid multiple instances of the
     * class (singleton).
     * 
     * @return NULL
     */
    private function __construct()
    {
        // Stores references to the request vars
        $this->_requestVars = array(
            'G' => &$_GET,
            'P' => &$_POST,
            'C' => &$_COOKIE,
            'S' => &$_SESSION,
            'E' => &$_ENV
        );
    }
    
    /**
     * Clones an instance of the class
     * 
     * A call to this method will produce an exception, as the class cannot
     * be cloned (singleton).
     * 
     * @return  NULL
     * @throws  Oop_Core_Singleton_Exception    Always, as the class cannot be cloned (singleton)
     */
    public function __clone()
    {
        throw new Oop_Core_Singleton_Exception( 'Class ' . __CLASS__ . ' cannot be cloned', Oop_Core_Singleton_Exception::EXCEPTION_CLONE );
    }
    
    /**
     * 
     */
    public function __get( $name )
    {
        return $this->getRequestVar( $name, $this->_lookupOrder );
    }
    
    /**
     * 
     */
    public function __isset( $name )
    {
        return $this->requestVarExists( $name );
    }
    
    /**
     * Gets the unique class instance
     * 
     * This method is used to get the unique instance of the class
     * (singleton). If no instance is available, it will create it.
     * 
     * @return  Oop_Core_ClassManager   The unique instance of the class
     */
    public static function getInstance()
    {
        // Checks if the unique instance already exists
        if( !is_object( self::$_instance ) ) {
            
            // Creates the unique instance
            self::$_instance = new self();
        }
        
        // Returns the unique instance
        return self::$_instance;
    }
    
    /**
     * 
     */
    public function setLookupOrder( $lookupOrder )
    {
        $oldValue           = $this->_lookupOrder;
        $this->_lookupOrder = ( string )$lookupOrder;
        return $oldValue;
    }
    
    /**
     * 
     */
    public function getRequestVar( $name, $order = '' )
    {
        $order = ( $order ) ? $order : $this->_lookupOrder;
        $keys  = preg_split( '//', $order );
        
        foreach( $keys as $key ) {
            
            if( isset( $this->_requestVars[ $key ][ $name ] ) ) {
                
                return $this->_requestVars[ $key ][ $name ];
            }
        }
        
        return false;
    }
    
    /**
     * 
     */
    public function requestVarExists( $name, $order = '' )
    {
        $order = ( $order ) ? $order : $this->_lookupOrder;
        $keys  = preg_split( '//', $order );
        
        foreach( $keys as $key ) {
            
            if( isset( $this->_requestVars[ $key ][ $name ] ) ) {
                
                return true;
            }
        }
        
        return false;
    }
}
