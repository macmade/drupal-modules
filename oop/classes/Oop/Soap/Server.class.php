<?php

# $Id$

/**
 * SOAP server
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Oop/Soap
 * @version         0.1
 */
class Oop_Soap_Server
{
    /**
     * The instance of the SOAP server
     */
    private $_soapServer = NULL;
    
    /**
     * Class constructor
     * 
     * @param   string  The URL to the WSDL file
     * @return  NULL
     */
    public function __construct( $wsdl )
    {
        // Checks if the Soap_Server class is available
        if( !class_exists( 'Soap_Server' ) ) {
            
            // Error - SOAP support is disabled
            throw new Oop_Soap_Server_Exception( 'The SoapServer class does not exist', Oop_Soap_Server_Exception::EXCEPTION_NO_SOAP );
        }
        
        // Checks if we have raw POST data
        if ( !isset( $GLOBALS[ 'HTTP_RAW_POST_DATA' ] ) ) {
            
            // Sets the raw POST data (compatibility issue)
            $GLOBALS[ 'HTTP_RAW_POST_DATA' ] = file_get_contents( 'php://input' );
        }
        
        // Creates the SOAP server
        $this->_soapServer = new SoapServer( $wsdl );    
    }
    
    /**
     * Sets the WSDL cache property (PHP configuration value)
     * 
     * @param   boolean Wether to cache the WSDL files
     * @return  boolean The old configuration value
     */
    public static function setWsdlCache( $value )
    {
        // Checks if we can call the ini_set() function
        if( is_callable( 'ini_set' ) && is_callable( 'ini_get' ) ) {
            
            // Gets the old value
            $oldValue = ini_get( 'soap.wsdl_cache_enabled' );
            
            // Sets the new value
            ini_set( 'soap.wsdl_cache_enabled', ( boolean )$value );
            
            // Returns the new value
            return $oldValue;
        }
        
        // The ini_set() function cannot be called
        throw new Oop_Soap_Server_Exception( 'Cannot set the WSDL cache property through the ini_set() function', Oop_Soap_Server_Exception::EXCEPTION_NO_INI_SET );
    }
    
    /**
     * Sets the class that will handle the SOAP procedures
     * 
     * @param   string  The name of the class
     * @param   array   An optional array with the arguments to pass to the class constructor
     * @return  NULL
     */
    public function setHandlerClass( $className, array $args = array() )
    {
        $this->_soapServer->setClass( $className, $args );
        $this->_soapServer->handle();
    }
    
    /**
     * Gets the available SOAP procedures
     * 
     * @return  array   An array with the available SOAP procedures
     */
    public function getFunctions()
    {
        return $this->_soapServer->getFunctions();
    }
    
    /**
     * Sets the persistence mode of the SOAP server
     * 
     * @param   int     One of the SOAP_PERSISTENCE_XXX constants
     * @return  NULL
     */
    public function setPersistence( $mode )
    {
        $this->_soapServer->setPersistence( $mode );
    }
    
    /**
     * Issues a SoapServer fault indicating an error
     * 
     * @param   string  The error code
     * @param   string  The error message
     * @param   string  
     * @param   mixed   
     * @param   string  
     * @return  NULL
     */
    public function fault( $code, $string, $actor = false, $details = false, $name = false )
    {
        if( $actor === false ) {
            
            $this->_soapServer->setPersistence( $code, $string );
            
        } elseif( $details === false ) {
            
            $this->_soapServer->setPersistence( $code, $string, $actor );
            
        } elseif( $name === false ) {
            
            $this->_soapServer->setPersistence( $code, $string, $actor, $details );
            
        } else {
            
            $this->_soapServer->setPersistence( $code, $string, $actor, $details, $name );
        }
    }
}
