<?php

/**
 * Page processor for Drupal
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Drupal
 * @version         0.1
 */
final class Oop_Drupal_Page_Processor
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
     * An array with the instances of the class (multi-singleton)
     */
    private static $_instances   = array();
    
    /**
     * The number of instances
     */
    private static $_nbInstances = 0;
    
    /**
     * The instance of the database class
     */
    private static $_db          = NULL;
    
    /**
     * Wether the static variables are set or not
     */
    protected static $_hasStatic = false;
    
    /**
     * The name of the current instance (multi-singleton)
     */
    private $_instanceName       = '';
    
    /**
     * The page object
     */
    private $_page               = NULL;
    
    /**
     * The router object
     */
    private $_router             = NULL;
    
    /**
     * The load objects for the current page
     */
    private $_loadObjects        = array();
    
    /**
     * The path segments
     */
    private $_pathInfo           = array();
    
    /**
     * Wheter access is granted to the page or not
     */
    private $_access             = false;
    
    /**
     * The processed page path
     */
    private $_processedPath      = '';
    
    /**
     * The processed page title
     */
    private $_processedTitle     = '';
    
    /**
     * Class constructor
     * 
     * The class constructor is private to avoid multiple instances of the
     * class (singleton).
     * 
     * @return NULL
     */
    private function __construct( $path )
    {
        // Sets the current instance name
        $this->_instanceName = $path;
        
        // Checks if the static variables are set
        if( !self::$_hasStatic ) {
            
            // Sets the static variables
            self::_setStaticVars();
        }
        
        // Registers the current instance
        self::$_instances[ $path ] = $this;
        self::$_nbInstances++;
        
        // Stores the path segments
        $this->_pathInfo = explode( '/', $path );
        
        // Try to get the page object
        if( $this->_getPageObject() ) {
            
            // Try to get the router object
            if( $this->_getRouterObject() ) {
                
                // Process the page
                $this->_processPage();
                
            } else {
                
                // No router for the page
                throw new Oop_Drupal_Page_Processor_Exception( 'The router for the page '. $path .' does not exist', Oop_Drupal_Page_Processor_Exception::EXCEPTION_NO_ROUTER );
            }
            
        } else {
            
            // The page does not exist
            throw new Oop_Drupal_Page_Processor_Exception( 'The requested page '. $path .' does not exist', Oop_Drupal_Page_Processor_Exception::EXCEPTION_NO_PAGE );
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
     * 
     */
    public static function getInstance( $path )
    {
        // Creates the required instance if it does not exists
        if( !isset( self::$_instances[ $path ] ) ) {
            
            new self( $path );
        }
        
        // Returns the required instance
        return self::$_instances[ $path ];
    }
    
    /**
     * Sets the needed static variables
     * 
     * @return  NULL
     */
    private static function _setStaticVars()
    {
        // Gets the instance of the database class
        self::$_db        = Oop_Drupal_Database::getInstance();
        
        // Static variables are set
        self::$_hasStatic = true;
    }
    
    /**
     * 
     */
    private function _getPageObject()
    {
        // Prepares the PDO query
        $query     = self::$_db->prepare( 'SELECT * FROM {menu_links} WHERE link_path = :path' );
        
        // Parameters for the PDO query
        $sqlParams = array(
            ':path' => $this->_instanceName
        );
        
        // Executes the PDO query
        $query->execute( $sqlParams );
        
        // Gets the page object
        $page = $query->fetchObject();
        
        // Checks the page object
        if( $page ) {
            
            // Stores the page object
            $this->_page = $page;
            
            // Page was found
            return true;
        }
        
        // Page was not found
        return false;
    }
    
    /**
     * 
     */
    private function _getRouterObject()
    {
        // Prepares the PDO query
        $query     = self::$_db->prepare( 'SELECT * FROM {menu_router} WHERE path = :path' );
        
        // Parameters for the PDO query
        $sqlParams = array(
            ':path' => $this->_page->router_path
        );
        
        // Executes the PDO query
        $query->execute( $sqlParams );
        
        // Gets the router object
        $router = $query->fetchObject();
        
        // Checks the router object
        if( $router ) {
            
            // Stores the router object
            $this->_router = $router;
            
            // Router was found
            return true;
        }
        
        // Router was not found
        return false;
    }
    
    /**
     * 
     */
    private function _processPage()
    {
        // Process the 'to_arg' callbacks
        $this->_processToArgCallbacks();
        
        // Process the 'load' callbacks
        $this->_processLoadCallbacks();
        
        // Process the 'title' callback
        $this->_processAccessCallback();
        
        // Process the 'access' callback
        $this->_processTitleCallback();
    }
    
    /**
     * 
     */
    private function _processToArgCallbacks()
    {
        if( $this->_router->to_arg_functions ) {
            
            $argFuncs = unserialize( $this->_router->to_arg_functions );
            
            foreach( $argFuncs as $index => $funcName ) {
                
                $this->_pathInfo[ $index ] = Oop_Callback_Helper::apply( $funcName, array( $this->_pathInfo[ $index ], $this->_pathInfo, $index ) );
            }
            
            $this->_processedPath = implode( '/', $this->_pathInfo );
        }
    }
    
    /**
     * 
     */
    private function _processLoadCallbacks()
    {
        if( $this->_router->load_functions ) {
            
            $loadFuncs = unserialize( $this->_router->load_functions );
            
            foreach( $loadFuncs as $index => $funcName ) {
                
                $this->_loadObjects[ $index ] = Oop_Callback_Helper::apply( $funcName, $this->_pathInfo[ $index ] );
            }
        }
    }
    
    /**
     * 
     */
    private function _processAccessCallback()
    {
        if( is_numeric( $this->_router->access_callback ) ) {
            
            $access = ( boolean )$this->_router->access_callback;
            
        } elseif( $this->_router->access_callback ) {
            
            $args = unserialize( $this->_router->access_arguments );
            
            foreach( $args as $key => $value ) {
                
                if( isset( $this->_loadObjects[ $value ] ) ) {
                    
                    $args[ $key ] = &$this->_loadObjects[ $value ];
                }
            }
            
            $this->_access = Oop_Callback_Helper::apply( $this->_router->access_callback, $args );
        }
    }
    
    /**
     * 
     */
    private function _processTitleCallback()
    {
        if( $this->_router->title_callback ) {
            
            $args = array( $this->_page->title );
            
            if( $this->_router->title_arguments ) {
                
                $titleArgs = unserialize( $this->_router->title_arguments );
                
                foreach( $titleArgs as $key => $value ) {
                    
                    if( isset( $this->_loadObjects[ $value ] ) ) {
                        
                        $args[ $key ] = &$this->_loadObjects[ $value ];
                    }
                }
            }
            
            $this->_processedTitle = Oop_Callback_Helper::apply( $this->_router->title_callback, $args );
        }
    }
    
    /**
     * 
     */
    public function isAccessible()
    {
        return $this->_access;
    }
    
    /**
     * 
     */
    public function getPath()
    {
        return $this->_processedPath;
    }
    
    /**
     * 
     */
    public function getTitle()
    {
        return $this->_processedTitle;
    }
    
    /**
     * 
     */
    public function getPage()
    {
        return clone( $this->_page );
    }
    
    /**
     * 
     */
    public function getRouter()
    {
        return clone( $this->_router );
    }
}
