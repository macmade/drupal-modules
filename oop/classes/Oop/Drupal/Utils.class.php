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
     * An array with the variables for the OOP modules
     */
    private static $_modVars  = array();
    
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
     * Gets the variables belonging to a module from the Drupal database
     * 
     * @param   string  The module name
     * @return  array   An array with all the module variables
     */
    public function getModuleVariables( $name )
    {
        // Checks if the variables already exists for the requested module
        if( isset( $this->_modVars[ $name ] ) ) {
            
            // Returns the module variables
            return $this->_modVars[ $name ];
        }
        
        // Creates the storage array
        $this->_modVars[ $name ] = array();
        
        // Parameters for the PDO query
        $params = array(
            ':module_name' => $name
        );
        
        // SQL query to select the variables from this module
        $sql    = 'SELECT *
                   FROM {oop_modules_variables}
                   WHERE module_name = :module_name
                   ORDER BY variable_name';
        
        // Prepares the PDO query
        $query  = self::$_db->prepare( $sql );
        
        // Executes the PDO query
        $query->execute( $params );
        
        // Process each variable
        while( $variable = $query->fetchObject() ) {
            
            // Stores the current variable
            $this->_modVars[ $name ][ $variable->variable_name ] = $variable->variable_value;
        }
        
        // Returns the module variables
        return $this->_modVars[ $name ];
    }
    
    /**
     * Gets a variables belonging to a module from the Drupal database
     * 
     * @param   string  The module name
     * @param   string  The variable name
     * @param   mixed   An optionnal default value to return if the variable does not exist
     * @return  mixed   The value of the variable
     */
    public function getModuleVariable( $modName, $varName, $defaultValue = false )
    {
        // Checks if the module variables exists
        if( !isset( $this->_modVars[ $modName ] ) ) {
            
            // Gets all the module variables
            $this->getModuleVariables( $modName );
        }
        
        // Checks if the variable exist
        if( isset( $this->_modVars[ $modName ][ $varName ] ) ) {
            
            // Returns the variable value
            return $this->_modVars[ $modName ][ $varName ];
        }
        
        // Returns the default value
        return $defaultValue;
    }
    
    /**
     * Deletes the variables belonging to a module from the Drupal database
     * 
     * @param   string  The name of the module
     * @return  NULL
     */
    public function deleteModuleVariables( $name )
    {
        // Parameters for the PDO query
        $sqlParams = array(
            ':module_name'   => $name
        );
        
        // SQL query
        $sql       = 'DELETE FROM {oop_modules_variables}
                      WHERE module_name = :module_name';
        
        // Prepares the PDO query
        $query     = self::$_db->prepare( $sql );
        
        // Executes the PDO query
        return $query->execute( $sqlParams );
    }
    
    /**
     * Stores a variable belonging to a module in the Drupal database
     * 
     * @param   string  The module name
     * @param   string  The variable name
     * @param   mixed   Tha value to store in the database
     * @return  boolean
     */
    public function storeModuleVariable( $modName, $varName, $value )
    {
        return self::$_db->insertRecord(
            'oop_modules_variables',
            array(
                'module_name'    => $modName,
                'variable_name'  => $varName,
                'variable_value' => $value
            )
        );
    }
}
