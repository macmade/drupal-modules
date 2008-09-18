<?php

# $Id$

/**
 * Miscellaneous Drupal utilities
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Oop/String
 * @version         0.1
 */
final class Oop_Drupal_Utils
{
    /**
     * The unique instance of the class (singleton)
     */
    private static $_instance = NULL;
    
    /**
     * The database object
     */
    private static $_db       = NULL;
    
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
        // Gets the instance of the database class
        self::$_db = Oop_Drupal_Database::getInstance();
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
     * @return  Oop_String_Utils    The unique instance of the class
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
     * Deletes variables belonging to a module from the Drupal database
     * 
     * @param   string  The name of the module
     * @return  NULL
     */
    public function deleteModuleVariables( $name )
    {
        // Parameters for the PDO query
        $sqlParams = array(
            ':name'   => $name . '_%',
        );
        
        // SQL query
        $sql       = 'DELETE FROM {variable}
                      WHERE name LIKE :name';
        
        // Prepares the PDO query
        $query     = self::$_db->prepare( $sql );
        
        // Executes the PDO query
        $query->execute( $sqlParams );
        
        print_r( $query->fetchAll() );
    }
}
