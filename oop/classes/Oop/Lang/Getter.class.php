<?php

/**
 * 
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Oop/Lang
 * @version         0.1
 */
final class Oop_Lang_Getter
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
    private static $_instances           = array();
    
    /**
     * The number of instances
     */
    private static $_nbInstances         = 0;
    
    /**
     * The name of the default instance
     */
    private static $_defaultInstanceName = 'oop';
    
    /**
     * The instance of the class manager
     */
    private static $_classManager        = NULL;
    
    /**
     * The default language
     */
    private static $_defaultLanguage     = 'en';
    
    /**
     * The current language
     */
    private static $_language            = '';
    
    /**
     * The SimpleXMLElement object containing tha language labels
     */
    private $_labels                     = NULL;
    
    /**
     * The name of the current instance (multi-singleton)
     */
    private $_instanceName               = '';
    
    /**
     * Class constructor
     * 
     * The class constructor is private to avoid multiple instances of the
     * class (singleton).
     * 
     * @return NULL
     */
    private function __construct( $name )
    {
        // Sets the current instance name
        $this->_instanceName = $name;
        
        // Checks if we are constructing the default instance
        if( !self::$_nbInstances ) {
            
            self::$_classManager = Oop_Core_ClassManager::getInstance();
            
            self::$_language     = $GLOBALS[ 'language' ]->language;
        }
        
        $modPath  = self::$_classManager->getModulePath( $this->_instanceName );
        
        $langFile = $modPath . 'lang' . DIRECTORY_SEPARATOR . self::$_language . '.xml';
        
        if( !file_exists( $langFile ) ) {
            
            $langFile = $modPath . 'lang' . DIRECTORY_SEPARATOR . self::$_defaultLanguage . '.xml';
        }
        
        if( !file_exists( $langFile ) ) {
            
            throw new Oop_Lang_Exception( 'The lang file for module ' . $this->_instanceName . ' does not exist (path: ' . $langFile . ')', Oop_Lang_Exception::EXCEPTION_NO_LANG_FILE );
        }
        
        try {
            
            $this->_labels = simplexml_load_file( $langFile );
            
        } catch( Exception $e ) {
            
            throw new Oop_Lang_Exception( $e->getMessage(), Oop_Lang_Exception::EXCEPTION_BAD_XML );
        }
        
        // Registers the current instance
        self::$_instances[ $name ] = $this;
        self::$_nbInstances++;
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
        return $this->getLabel( $name, 'module' );
    }
    
    /**
     * 
     */
    protected function getLabel( $name, $section )
    {
        if( isset( $this->_labels->$section->$name ) ) {
            
            return ( string )$this->_labels->$section->$name;
        }
        
        if( isset( self::$_instances[ self::$_defaultInstanceName ]->_labels->$section->$name ) ) {
            
            return ( string )self::$_instances[ self::$_defaultInstanceName ]->_labels->$section->$name;
        }
        
        return '[LABEL: ' . $name . ']';
    }
    
    /**
     * 
     */
    public static function getInstance( $name )
    {
        // Creates the default instance if it does not exists
        if( !self::$_nbInstances ) {
            
            new self( self::$_defaultInstanceName );
        }
        
        // Creates the required instance if it does not exists
        if( !isset( self::$_instances[ $name ] ) ) {
            
            new self( $name );
        }
        
        // Returns the required instance
        return self::$_instances[ $name ];
    }
    
    /**
     * 
     */
    public static function setDefaultLanguage( $language )
    {
        $oldLanguage            = self::$_defaultLanguage;
        
        self::$_defaultLanguage = $language;
        
        return $oldLanguage;
    }
}
