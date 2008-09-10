<?php

/**
 * Drupal database class
 * 
 * The goal of the class is to provide Drupal with the functionnalities of
 * PDO (PHP Data Object).
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Drupal
 * @version         0.1
 */
final class Drupal_Database
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
     * The PDO object for the Drupal database
     */
    private $_pdo             = NULL;
    
    /**
     * The distinguised server name for the Drupal database
     */
    private $_dsn             = '';
    
    /**
     * Class constructor
     * 
     * The class constructor is private to avoid multiple instances of the
     * class (singleton).
     * 
     * @return  NULL
     * @throws  Drupal_Database_Exception   If the PDO class is not available
     * @throws  Drupal_Database_Exception   If the PDO object cannot be created
     */
    private function __construct()
    {
        // Checks if PDO is available
        if( !class_exists( 'PDO' ) ) {
            
            // PDO is not available
            throw new Drupal_Database_Exception( 'PDO is not available', Drupal_Database_Exception::EXCEPTION_NO_CONNECTION );
        }
        
        // Storage
        $dsnParts = array();
        
        // Gets the different parts of the Drupal database URL, from the Drupal configuration
        preg_match( '/^([^:]+):\/\/([^:]+):([^@]+)@([^\/]+)\/(.*)$/', $GLOBALS[ 'db_url' ], $dsnParts );
        
        // Stores the driver (mysqli is not a valid PDO driver, so it will be replaced by mysql)
        $driver     = ( $dsnParts[ 1 ] === 'mysqli' ) ? 'mysql' : $dsnParts[ 1 ];
        
        // Stores the other DSN informations
        $user       = $dsnParts[ 2 ];
        $pass       = $dsnParts[ 3 ];
        $host       = $dsnParts[ 4 ];
        $db         = $dsnParts[ 5 ];
        
        // Stores the full DSN
        $this->_dsn = $driver . ':host=' . $host . ';dbname=' . $db;
        
        try {
            
            // Creates the PDO object
            $this->_pdo = new PDO( $this->_dsn, $user, $pass );
            
        } catch( Exception $e ) {
            
            // The PDO object cannot be created - Reroute the exception
            throw new Drupal_Database_Exception( $e->getMessage(), Drupal_Database_Exception::EXCEPTION_NO_CONNECTION );
        }
    }
    
    /**
     * Class destructor
     * 
     * This method will close the PDO connection to the Drupal database.
     * 
     * @return  NULL
     */
    public function __destruct()
    {
        $this->_pdo = NULL;
    }
    
    /**
     * PHP method calls overloading
     * 
     * This method will reroute all the call on this object to the PDO object.
     * 
     * @param   string                      The name of the called method
     * @param   array                       The arguments for the called method
     * @return  mixed                       The result of the PDO method called
     * @throws  Drupal_Database_Exception   If the called method does not exist
     */
    public function __call( $name, array $args = array() )
    {
        // Checks if the method can be called
        if( !is_callable( array( $this->_pdo, $name ) ) ) {
            
            // Called method does not exist
            throw new Drupal_Database_Exception( 'The method \'' . $name . '\' cannot be called on the PDO object', Drupal_Database_Exception::EXCEPTION_BAD_METHOD );
        }
        
        // Gets the number of arguments
        $argCount = count( $args );
        
        // Ww won't use call_user_func_array, as it cannot return references
        switch( $argCount ) {
            
            case 1:
                
                return $this->_pdo->$name( $args[ 0 ] );
                break;
            
            case 2:
                
                return $this->_pdo->$name( $args[ 0 ], $args[ 1 ] );
                break;
            
            case 3:
                
                return $this->_pdo->$name( $args[ 0 ], $args[ 1 ], $args[ 2 ] );
                break;
            
            case 4:
                
                return $this->_pdo->$name( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ] );
                break;
                break;
            
            case 5:
                
                return $this->_pdo->$name( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ] , $args[ 4 ] );
                break;
            
            default:
                
                return $this->_pdo->$name();
                break;
        }
    }
    
    /**
     * Clones an instance of the class
     * 
     * A call to this method will produce an exception, as the class cannot
     * be cloned (singleton).
     * 
     * @return  NULL
     * @throws  Singleton_Exception Always, as the class cannot be cloned (singleton)
     */
    public function __clone()
    {
        throw new Singleton_Exception( 'Class ' . __CLASS__ . ' cannot be cloned', Singleton_Exception::EXCEPTION_CLONE );
    }
    
    /**
     * Gets the unique class instance
     * 
     * This method is used to get the unique instance of the class
     * (singleton). If no instance is available, it will create it.
     * 
     * @return  Drupal_Database     The unique instance of the class
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
}
