<?php

/**
 * Class manager
 * 
 * This class will handle every request to a class from this project,
 * by automatically loading the class file (thanx to the SPL).
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Oop/Core
 * @version         0.1
 */
final class Oop_Core_ClassManager
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
     * Overrides for the modules
     */
    private $_overrides       = array();
    
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
            
            // Gets the relative path
            $relPath = drupal_get_path( 'module', $modName );
            
            // Sets the module paths (absolute and relative)
            $modPath = array(
                realpath( $relPath ) . DIRECTORY_SEPARATOR,
                $relPath . '/'
            );
        }
        
        // Stores the directory containing the classes
        $this->_classDir   = realpath( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . '..' ) . DIRECTORY_SEPARATOR;
        
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
                
                // Hidden - Process the next file
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
     * @throws  Oop_Core_Singleton_Exception    Always, as the class cannot be cloned (singleton)
     */
    public function __clone()
    {
        throw new Oop_Core_Singleton_Exception( 'Class ' . __CLASS__ . ' cannot be cloned', Oop_Core_Singleton_Exception::EXCEPTION_CLONE );
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
     * SPL autoload method
     * 
     * When registered with the spl_autoload_register() function, this method
     * will be called each time a class cannot be found, and will try to
     * load it.
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
        
        // Checks if the class belongs to the 'Oop' package
        if( substr( $className, 0, 4 ) === 'Oop_' ) {
            
            // Gets the class root package
            $rootPkg = substr( $className, 4, strpos( $className, '_', 4 ) - 4 );
            
            // Checks if the requested class belongs to this project
            if( isset( $instance->_packages[ $rootPkg ] ) || isset( $instance->_packages[ $className . '.class.php' ] ) ) {
                
                // Loads the class
                return $instance->_loadClass( $className );
                
            }
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
        $classPath = $this->_classDir . str_replace( '_', DIRECTORY_SEPARATOR, substr( $className, 4 ) ) . '.class.php';
        
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
     * Loads a module class
     * 
     * @param   string                          The module name
     * @return  NULL
     * @throws  Oop_Core_ClassManager_Exception If the class file for the module does not exist
     * @throws  Oop_Core_ClassManager_Exception If the module class is not defined
     * @throws  Oop_Core_ClassManager_Exception If the module class does not contain the PHP_COMPATIBLE constant
     * @throws  Oop_Core_ClassManager_Exception If the module class is incompatible with the current PHP version
     */
    private function _loadModuleClass( $name )
    {
        // Path to the module class file
        $path = $this->_moduleList[ $name ][ 0 ]
              . $name
              . '.class.php';
        
        // Checks if the class file exists
        if( !file_exists( $path ) ) {
            
            // The class file does not exist
            throw new Oop_Core_ClassManager_Exception( 'The class file for module ' . $name . ' does not exists (path: ' . $path . ')', Oop_Core_ClassManager_Exception::EXCEPTION_NO_MODULE_CLASS_FILE );
        }
        
        // Includes the class file
        require_once( $path );
        
        // Checks if the class for the module is defined
        if( !class_exists( $name ) ) {
            
            // The class is not defined
            throw new Oop_Core_ClassManager_Exception( 'The class for module ' . $name . ' is not defined', Oop_Core_ClassManager_Exception::EXCEPTION_NO_MODULE_CLASS );
        }
        
        // Checks if the PHP_COMPATIBLE constant is defined
        if( !defined( $name . '::PHP_COMPATIBLE' ) ) {
            
            // Class does not respect the project conventions
            throw new Oop_Core_ClassManager_Exception( 'The requested constant PHP_COMPATIBLE is not defined in class ' . $name, Oop_Core_ClassManager_Exception::EXCEPTION_NO_PHP_VERSION );
        }
        
        // Gets the minimal PHP version required (eval() is required as late static bindings are implemented only in PHP 5.3)
        eval( '$phpCompatible = ' . $name . '::PHP_COMPATIBLE;' );
        
            // Checks the PHP version
        if( version_compare( PHP_VERSION, $phpCompatible, '<' ) ) {
            
            // PHP version is too old
            throw new Oop_Core_ClassManager_Exception( 'Class ' . $name . ' requires PHP version ' . $phpCompatible . ' (actual version is ' . PHP_VERSION . ')' , Oop_Core_ClassManager_Exception::EXCEPTION_PHP_VERSION_TOO_OLD );
        }
    }
    
    /**
     * Gets an instance of a Drupal module
     * 
     * @param   string                          The name of the Drupal module
     * @return  Oop_Drupal_ModuleBase           An instance of the requested module
     * @throws  Oop_Core_ClassManager_Exception If the module does not exist
     * @throws  Oop_Core_ClassManager_Exception If the class file for the module does not exist
     * @throws  Oop_Core_ClassManager_Exception If the module class is not defined
     * @throws  Oop_Core_ClassManager_Exception If the module class does not contain the PHP_COMPATIBLE constant
     * @throws  Oop_Core_ClassManager_Exception If the module class is incompatible with the current PHP version
     */
    public function getModule( $name )
    {
        // Checks if an override is defined
        if( isset( $this->_overrides[ $name ] ) ) {
            
            // Loads the initial module class, so the override can extend it
            $this->_loadModuleClass( $name );
            
            // Sets the new module name
            $name = $this->_overrides[ $name ];
        }
        
        // Checks if the module class has already been instanciated
        if( isset( $this->_modules[ $name ] ) ) {
            
            // Returns the instance
            return $this->_modules[ $name ];
        }
        
        // Checks if the module is available
        if( !isset( $this->_moduleList[ $name ] ) ) {
            
            // The module does not seem to be loaded
            throw new Oop_Core_ClassManager_Exception( 'The module ' . $name . ' is not loaded', Oop_Core_ClassManager_Exception::EXCEPTION_MODULE_NOT_LOADED );
        }
        
        // Loads the module class
        $this->_loadModuleClass( $name );
        
        // Creates an instance of the module
        $this->_modules[ $name ] = new $name( dirname( $path ) . DIRECTORY_SEPARATOR );
        
        // Returns the instance of the module
        return $this->_modules[ $name ];
    }
    
    /**
     * Overrides a module with another
     * 
     * @param   string  The name of the module to override
     * @param   string  The name of the module which will override the other
     * @return  NULL
     */
    public function overrideModule( $module, $override )
    {
        $this->_overrides[ $module ] = $override;
    }
    
    /**
     * Gets the full (absolute) path of a Drupal module
     * 
     * @param   string  The name of the module
     * @return  mixed   The path of the module, or false is the module is not available
     */
    public function getModulePath( $name )
    {
        return ( isset( $this->_moduleList[ $name ] ) ) ? $this->_moduleList[ $name ][ 0 ] : false;
    }
    
    /**
     * Gets the relative path of a Drupal module
     * 
     * @param   string  The name of the module
     * @return  mixed   The path of the module, or false is the module is not available
     */
    public function getModuleRelativePath( $name )
    {
        return ( isset( $this->_moduleList[ $name ] ) ) ? $this->_moduleList[ $name ][ 1 ] : false;
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
