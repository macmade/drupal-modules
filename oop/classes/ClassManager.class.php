<?php

/**
 * Class manager
 * 
 * This class will handle every request to a class from this project,
 * by automatically loading the class file (thanx to the SPL).
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
final class ClassManager
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
     * The loaded classes from this project
     */
    private $_loadedClasses   = array();
    
    /**
     * The available top packages
     */
    private $_packages        = array();
    
    /**
     * The instances of the modules
     */
    private $_modules         = array();
    
    /**
     * The list of the loaded modules
     */
    private $_moduleList      = array();
    
    /**
     * The directory which contains the classes
     */
    private $_classDir        = '';
    
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
        // Gets the list of the Drupal modules
        $this->_moduleList = module_list();
        
        // Process the module list
        foreach( $this->_moduleList as $modName => &$modPath ) {
            
            // Sets the module path
            $modPath = realpath( drupal_get_path( 'module', $modName ) ) . DIRECTORY_SEPARATOR;
        }
        
        // Stores the directory containing the classes
        $this->_classDir   = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
        
        // Creates a directory iterator in the directory containing this file
        $dirIterator       = new DirectoryIterator( $this->_classDir );
        
        // Process each directory
        foreach( $dirIterator as $file ) {
            
            // Checks if the file is a PHP class file
            if( substr( $file, strlen( $file ) - 10 ) === '.class.php' ) {
                
                // Stores the file name, with it's full path
                $this->_packages[ ( string )$file ] = $file->getPathName();
                
                // Process the next file
                continue;
            }
            
            // Checks if the file is a directory
            if( !$file->isDir() ) {
                
                // File - Process the next file
                continue;
            }
            
            // Checks if the directory is hidden
            if( substr( $file, 0, 1 ) === '.' ) {
                
                // HIdden - Process the next file
                continue;
            }
            
            // Stores the directory name, with it's full path
            $this->_packages[ ( string )$file ] = $file->getPathName();
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
    
    /**
     * SPL autoload method
     * 
     * @param   string  The name of the class to load
     * @return  boolean
     * @see     getInstance
     * @see     _loadClass
     */
    public static function autoLoad( $className )
    {
        // Instance of this class
        static $instance = NULL;
        
        // Checks if the instance of the class has already been fetched
        if( !is_object( $instance ) ) {
            
            // Gets the instance of this class
            $instance = self::getInstance();
        }
        
        // Gets the class root package
        $rootPkg = substr( $className, 0, strpos( $className, '_' ) );
        
        // Checks if the requested class belongs to this project
        if( isset( $instance->_packages[ $rootPkg ] ) || isset( $instance->_packages[ $className . '.class.php' ] ) ) {
            
            // Loads the class
            return $instance->_loadClass( $className );
            
        }
        
        // The requested class does not belong to this project
        return false;
    }
    
    /**
     * Loads a class from this project
     * 
     * @param   string  The name of the class to load
     * @return  boolean
     */
    private function _loadClass( $className )
    {
        // Gets the class path
        $classPath = $this->_classDir . str_replace( '_', DIRECTORY_SEPARATOR, $className ) . '.class.php';
        
        // Checks if the class file exists
        if( file_exists( $classPath ) ) {
            
            // Includes the class file
            require_once( $classPath );
            
            // Checks if the PHP_COMPATIBLE constant is defined
            if( !defined( $className . '::PHP_COMPATIBLE' ) ) {
                
                // Class does not respect the project conventions
                trigger_error( 'The requested constant PHP_COMPATIBLE is not defined in class ' . $className, E_USER_ERROR );
            }
            
            // Gets the minimal PHP version required (eval() is required as late static bindings are implemented only in PHP 5.3)
            eval( '$phpCompatible = ' . $className . '::PHP_COMPATIBLE;' );
            
            // Checks the PHP version
            if( version_compare( PHP_VERSION, $phpCompatible, '<' ) ) {
                
                // PHP version is too old
                trigger_error( 'Class ' . $className . ' requires PHP version ' . $phpCompatible . ' (actual version is ' . PHP_VERSION . ')' , E_USER_ERROR );
            }
            
            // Adds the class to the loaded classes array
            $this->_loadedClasses[ $className ] = $classPath;
            
            // Class was successfully loaded
            return true;
        }
        
        // Class file was not found
        return false;
    }
    
    /**
     * 
     */
    public function getModule( $name )
    {
        if( isset( $this->_modules[ $name ] ) ) {
            
            return $this->_modules[ $name ];
        }
        
        if( !isset( $this->_moduleList[ $name ] ) ) {
            
            throw new ClassManager_Exception( 'The module ' . $name . ' is not loaded', ClassManager_Exception::EXCEPTION_MODULE_NOT_LOADED );
        }
        
        $path = $this->_moduleList[ $name ]
              . $name
              . '.class.php';
        
        if( !file_exists( $path ) ) {
            
            throw new ClassManager_Exception( 'The class file for module ' . $name . ' does not exists (path: ' . $path . ')', ClassManager_Exception::EXCEPTION_NO_MODULE_CLASS_FILE );
        }
        
        require_once( $path );
        
        if( !class_exists( $name ) ) {
            
            throw new ClassManager_Exception( 'The class for module ' . $name . ' is not defined', ClassManager_Exception::EXCEPTION_NO_MODULE_CLASS );
        }
        
        // Checks if the PHP_COMPATIBLE constant is defined
        if( !defined( $name . '::PHP_COMPATIBLE' ) ) {
            
            // Class does not respect the project conventions
            throw new ClassManager_Exception( 'The requested constant PHP_COMPATIBLE is not defined in class ' . $name, ClassManager_Exception::EXCEPTION_NO_PHP_VERSION );
        }
        
        // Gets the minimal PHP version required (eval() is required as late static bindings are implemented only in PHP 5.3)
        eval( '$phpCompatible = ' . $name . '::PHP_COMPATIBLE;' );
        
            // Checks the PHP version
        if( version_compare( PHP_VERSION, $phpCompatible, '<' ) ) {
            
            // PHP version is too old
            throw new ClassManager_Exception( 'Class ' . $name . ' requires PHP version ' . $phpCompatible . ' (actual version is ' . PHP_VERSION . ')' , ClassManager_Exception::EXCEPTION_PHP_VERSION_TOO_OLD );
        }
        
        // Creates an instance of the module
        $this->_modules[ $name ] = new $name( dirname( $path ) . DIRECTORY_SEPARATOR );
        
        // Returns the instance of the module
        return $this->_modules[ $name ];
    }
    
    /**
     * 
     */
    public function getModulePath( $name )
    {
        return ( isset( $this->_moduleList[ $name ] ) ) ? $this->_moduleList[ $name ] : false;
    }
    
    /**
     * Gets the loaded classes from this project
     * 
     * @return  array   An array with the loaded classes
     */
    public function getLoadedClasses()
    {
        // Returns the loaded classes from this project
        return $this->_loadedClasses;
    }
}
