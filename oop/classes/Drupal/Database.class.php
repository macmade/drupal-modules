<?php

/**
 * Drupal database class
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
     * 
     */
    private $_pdo             = NULL;
    
    /**
     * 
     */
    private $_dsn             = '';
    
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
        $dsnParts = array();
        
        preg_match( '/^([^:]+):\/\/([^:]+):([^@]+)@([^\/]+)\/(.*)$/', $GLOBALS[ 'db_url' ], $dsnParts );
        
        $driver     = ( $dsnParts[ 1 ] === 'mysqli' ) ? 'mysql' : $dsnParts[ 1 ];
        $user       = $dsnParts[ 2 ];
        $pass       = $dsnParts[ 3 ];
        $host       = $dsnParts[ 4 ];
        $db         = $dsnParts[ 5 ];
        
        $this->_dsn = $driver . ':host=' . $host . ';dbname=' . $db;
        
        try {
            
            $this->_pdo = new PDO( $this->_dsn, $user, $pass );
            
        } catch( Exception $e ) {
            
            throw new Drupal_Database_Exception( $e->getMessage(), $e->getCode() );
        }
    }
    
    /**
     * 
     */
    public function __destruct()
    {
        $this->_pdo = NULL;
    }
    
    /**
     * 
     */
    public function __call( $name, array $args = array() )
    {
        $argCount = count( $args );
        
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
     * @return  object  The unique instance of the class
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
