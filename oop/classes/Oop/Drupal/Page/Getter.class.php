<?php

/**
 * Page getter for Drupal
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Drupal
 * @version         0.1
 */
final class Oop_Drupal_Page_Getter implements ArrayAccess
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
     * The path path
     */
    private $_path               = '';
    
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
    private function __construct( $path, stdClass $row = NULL )
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
        
        // Stores the path
        $this->_path     = $path;
        
        // Checks for a page row, meaning the page has already been fetched from the database
        if( $row ) {
            
            // Stores the page row
            $this->_page = $row;
            
            // Process the page
            $this->_processPage();
            
        } elseif( $this->_getPageObject() ) {
            
            // Process the page
            $this->_processPage();
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
    public function __isset( $name )
    {
        return isset( $this->_page->$name );
    }
    
    /**
     * 
     */
    public function __get( $name )
    {
        if( isset( $this->_page->$name ) ) {
           
           return $this->_page->$name;
        }
    }
    
    /**
     * 
     */
    public function offsetExists( $offset )
    {
        return isset( $this->_page->$offset );
    }
    
    /**
     * 
     */
    public function offsetGet( $offset )
    {
        return $this->_page->$offset;
    }
    
    /**
     * 
     */
    public function offsetSet( $offset, $value )
    {
        return false;
    }
    
    /**
     * 
     */
    public function offsetUnset( $offset )
    {
        return false;
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
     * 
     */
    public static function getPages( $where, array $params = array() )
    {
        // Checks if the static variables are set
        if( !self::$_hasStatic ) {
            
            // Sets the static variables
            self::_setStaticVars();
        }
        
        // Storages
        $pages = array();
        
        // SQL query
        $sql   = 'SELECT *
                  FROM {menu_links}
                  LEFT JOIN {menu_router}
                    ON {menu_links}.router_path = {menu_router}.path
                  LEFT JOIN {url_alias}
                    ON {menu_links}.link_path = {url_alias}.src';
        
        // Adds the WHERE clause
        $sql  .= ' WHERE ' . $where;
        
        // Prepares the PDO query
        $query = self::$_db->prepare( $sql );
        
        // Executes the PDO query
        $query->execute( $params );
        
        // Process each pages
        while( $page = $query->fetchObject() ) {
            
            // Checks if an instance for that path already exist
            if( isset( self::$_instances[ $page->link_path ] ) ) {
                
                // Stores the existing instance
                $pages[ $page->mlid ] = self::$_instances[ $page->link_path ];
                
            } else {
                
                // Process and stores the current page
                $pages[ $page->mlid ] = new self( $page->link_path, $page );
            }
        }
        
        // Returns the pages
        return $pages;
    }
    
    /**
     * 
     */
    private function _getPageObject()
    {
        // Parameters for the PDO query
        $sqlParams = array(
            ':path' => $this->_path,
        );
        
        // SQL query
        $sql   = 'SELECT *
                  FROM {menu_links}
                  LEFT JOIN {menu_router}
                    ON {menu_links}.router_path = {menu_router}.path
                  LEFT JOIN {url_alias}
                    ON {menu_links}.link_path = {url_alias}.src
                  WHERE {menu_links}.link_path = :path';
        
        // Adds the WHERE clause
        $sql  .= ' ' . $where;
        
        // Prepares the PDO query
        $query = self::$_db->prepare( $sql );
        
        // Executes the PDO query
        $query->execute( $sqlParams );
        
        // Gets the page
        $page = $query->fetchObject();
        
        // Checks the page
        if( $page ) {
            
            // Stores the page
            $this->_page = $page;
            return true;
        }
        
        // Page was not found
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
        if( $this->_page->to_arg_functions ) {
            
            $argFuncs = unserialize( $this->_page->to_arg_functions );
            
            foreach( $argFuncs as $index => $funcName ) {
                
                $this->_pathInfo[ $index ] = Oop_Callback_Helper::apply( $funcName, array( $this->_pathInfo[ $index ], $this->_pathInfo, $index ) );
            }
            
            $this->_processedPath = implode( '/', $this->_pathInfo );
            
        } else {
            
            $this->_processedPath = $this->_page->link_path;
        }
    }
    
    /**
     * 
     */
    private function _processLoadCallbacks()
    {
        if( $this->_page->load_functions ) {
            
            $loadFuncs = unserialize( $this->_page->load_functions );
            
            foreach( $loadFuncs as $index => $funcName ) {
                
                $this->_loadObjects[ $index ] = Oop_Callback_Helper::apply( $funcName, array( $this->_pathInfo[ $index ] ) );
            }
        }
    }
    
    /**
     * 
     */
    private function _processAccessCallback()
    {
        if( is_numeric( $this->_page->access_callback ) ) {
            
            $access = ( boolean )$this->_page->access_callback;
            
        } elseif( $this->_page->access_callback ) {
            
            $args = unserialize( $this->_page->access_arguments );
            
            foreach( $args as $key => $value ) {
                
                if( isset( $this->_loadObjects[ $value ] ) ) {
                    
                    $args[ $key ] = &$this->_loadObjects[ $value ];
                }
            }
            
            $this->_access = Oop_Callback_Helper::apply( $this->_page->access_callback, $args );
        }
    }
    
    /**
     * 
     */
    private function _processTitleCallback()
    {
        if( $this->_page->title_callback ) {
            
            $args = array( $this->_page->title );
            
            if( $this->_page->title_arguments ) {
                
                $titleArgs = unserialize( $this->_page->title_arguments );
                
                foreach( $titleArgs as $key => $value ) {
                    
                    if( isset( $this->_loadObjects[ $value ] ) ) {
                        
                        $args[ $key ] = &$this->_loadObjects[ $value ];
                    }
                }
            }
            
            $this->_processedTitle = Oop_Callback_Helper::apply( $this->_page->title_callback, $args );
            
        } else {
            
            $this->_processedTitle = $this->_page->link_title;
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
    public function getPath( $alias = true )
    {
        if( !$alias ) {
            
            return $this->_processedPath;
        }
        
        return ( $this->_page->dst ) ? $this->_page->dst : $this->_processedPath;
    }
    
    /**
     * 
     */
    public function getTitle()
    {
        return $this->_processedTitle;
    }
}
