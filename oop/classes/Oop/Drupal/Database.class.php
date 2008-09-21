<?php

# $Id$

/**
 * Drupal database class
 * 
 * The goal of the class is to provide Drupal with the functionnalities of
 * PDO (PHP Data Object).
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Oop/Drupal
 * @version         0.1
 */
final class Oop_Drupal_Database
{
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
     * @throws  Oop_Drupal_Database_Exception   If the PDO class is not available
     * @throws  Oop_Drupal_Database_Exception   If the PDO object cannot be created
     */
    private function __construct()
    {
        // Checks if PDO is available
        if( !class_exists( 'PDO' ) ) {
            
            // PDO is not available
            throw new Oop_Drupal_Database_Exception( 'PDO is not available', Oop_Drupal_Database_Exception::EXCEPTION_NO_CONNECTION );
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
            throw new Oop_Drupal_Database_Exception( $e->getMessage(), Oop_Drupal_Database_Exception::EXCEPTION_NO_CONNECTION );
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
     * @param   string                          The name of the called method
     * @param   array                           The arguments for the called method
     * @return  mixed                           The result of the PDO method called
     * @throws  Oop_Drupal_Database_Exception   If the called method does not exist
     */
    public function __call( $name, array $args = array() )
    {
        // Checks if the method can be called
        if( !is_callable( array( $this->_pdo, $name ) ) ) {
            
            // Called method does not exist
            throw new Oop_Drupal_Database_Exception( 'The method \'' . $name . '\' cannot be called on the PDO object', Oop_Drupal_Database_Exception::EXCEPTION_BAD_METHOD );
        }
        
        // Checks the method
        if( $name === 'exec' || $name === 'prepare' || $name === 'query' ) {
            
            // We need to replace the table name with their real values
            $args[ 0 ] = db_prefix_tables( $args[ 0 ] );
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
    
    /**
     * Creates a Drupal database schema corresponding to the OOP framework conventions
     * 
     * This method will automatically create a Drupal database schema for the
     * requested database table. Conventions are the following:
     * - The table name will be converted in uppercase
     * - The primary key will be 'id_' plus the table name in lowercase
     * 
     * Additionnaly, some fields, which are going to be used by the OOP
     * framework, will be automatically added:
     * - ctime: a timestamp corresponding to the creation time of the record
     * - mtime: a timestamp corresponding to the last update time of the record
     * - hidden: a flag telling if the record should be hidden in the frontend
     * - deleted: a flag telling if the record has been deleted
     * - id_users: the id of the user that created the record
     * 
     * @param   string  The name of the table
     * @param   array   An array with the fields, as specified by the Drupal database schema API
     * @param   array   An array with the indexes, if any
     * @param   array   An array with the unique fields, if any
     * @return  array   The final database schema
     */
    public static function createSchema( $tableName, array $fields, array $indexes = array(), array $unique = array() )
    {
        // Name of the primary key
        $pKey      = 'id_' . $tableName;
        
        // Creates the basic schema
        $schema = array(
            $tableName => array(
                'primary key' => array( $pKey ),
                'fields'      => array()
            )
        );
        
        // Process each field
        foreach( $fields as $key => $value ) {
            
            // Adds the current field
            $schema[ $tableName ][ 'fields' ][ $key ] = $value;
        }
        
        // Adds the control fields
        $schema[ $tableName ][ 'fields' ][ $pKey ] = array(
            'type'        => 'serial',
            'unsigned'    => true,
            'not null'    => true
        );
        $schema[ $tableName ][ 'fields' ][ 'ctime' ] = array(
            'type'     => 'int',
            'unsigned' => true,
            'not null' => true,
            'default ' => 0
        );
        $schema[ $tableName ][ 'fields' ][ 'mtime' ] = array(
            'type'     => 'int',
            'unsigned' => true,
            'not null' => true,
            'default ' => 0
        );
        $schema[ $tableName ][ 'fields' ][ 'hidden' ] = array(
            'type'     => 'int',
            'size'     => 'tiny',
            'unsigned' => true,
            'not null' => true,
            'default ' => 0
        );
        $schema[ $tableName ][ 'fields' ][ 'deleted' ] = array(
            'type'     => 'int',
            'size'     => 'tiny',
            'unsigned' => true,
            'not null' => true,
            'default ' => 0
        );
        $schema[ $tableName ][ 'fields' ][ 'id_users' ] = array(
            'type'     => 'int',
            'unsigned' => true,
            'not null' => true,
            'default ' => 0
        );
        
        // Checks for indexes
        if( count( $indexes ) ) {
            
            // Creates the schema entry
            $schema[ $tableName ][ 'indexes' ] = array();
            
            // Index name
            $indexName                         = ( is_array( $value ) ) ? 'index_' . implode( '_', $value ) : 'index_' . $value;
            
            // Process each field
            foreach( $indexes as $key => $value ) {
                
                // Adds the current field
                $schema[ $tableName ][ 'indexes' ][ $indexName ] = $value;
            }
        }
        
        // Checks for unique fields
        if( count( $unique ) ) {
            
            // Creates the schema entry
            $schema[ $tableName ][ 'unique keys' ] = array();
            
            // Unique name
            $uniqueName                            = ( is_array( $value ) ) ? 'index_' . implode( '_', $value ) : 'index_' . $value;
            
            // Process each field
            foreach( $unique as $key => $value ) {
                
                // Adds the current field
                $schema[ $tableName ][ 'unique keys' ][ $uniqueName ] = $value;
            }
        }
    }
    
    /**
     * 
     */
    public function getRecord( $table, $id, $getHidden = false )
    {
        // Primary key
        $pKey   = 'id_' . $table;
        
        // Table name, to support prefixes
        $table  = '{' . $table . '}';
        
        // Parameters for the PDO query
        $params = array(
            ':id'      => $id,
            ':deleted' => 0
        );
        
        // Checks if the hidden records must be selected or not
        if( $getHidden === false ) {
            
            // Do not select hidden records
            $params[ ':hidden' ] = 0;
        
            // Prepares the PDO query
            $query = $this->prepare(
                'SELECT * FROM ' . $table . '
                 WHERE ' . $pKey . ' = :id
                    AND hidden = :hidden
                 LIMIT 1'
            );
            
        } else {
            
            // Prepares the PDO query
            $query = $this->prepare(
                'SELECT * FROM ' . $table . '
                 WHERE ' . $pKey . ' = :id
                 LIMIT 1'
            );
        }
        
        // Executes the PDO query
        $query->execute( $params );
        
        // Returns the record
        return $query->fetchObject();
    }
    
    /**
     * 
     */
    public function getRecordsByFields( $table, array $fieldsValues, $getHidden = false  )
    {
        // Primary key
        $pKey   = 'id_' . $table;
        
        // Table name, to support prefixes
        $table  = '{' . $table . '}';
        
        // Starts the query
        $sql = 'SELECT * FROM ' . $table . ' WHERE ';
        
        // Parameters for the PDO query
        $params = array();
        
        // Process each field to check
        foreach( $fieldsValues as $fieldName => $fieldValue ) {
            
            // Adds the parameter
            $params[ ':' . $fieldName ] = $fieldValue;
            
            // Adds the statement
            $sql .= $fieldName . ' = :' . $fieldName . ' AND ';
        }
        
        // Checks if the hidden records must be selected or not
        if( $getHidden === false ) {
            
            // Do not select hidden records
            $params[ ':hidden' ] = 0;
            
            // Adds the statement
            $sql .= ' hidden = :hidden';
            
        } else {
            
            // Removes the last 'AND' from the sql query
            $sql = substr( $sql, 0, -5 );
        }
        
        // Prepares the PDO query
        $query = $this->prepare( $sql );
        
        // Executes the PDO query
        $query->execute( $params );
        
        // Storage
        $rows = array();
        
        // Process each row
        while( $row = $query->fetchObject() ) {
            
            // Stores the current row
            $rows[ $row->$pKey ] = $row;
        }
        
        // Returns the rows
        return $rows;
    }
    
    /**
     * 
     */
    public function insertRecord( $table, array $values )
    {
        // Table name to support prefixes
        $table  = '{' . $table . '}';
        
        // Gets the current time
        $time   = time();
        
        // Parameters for the PDO query
        $params = array(
            ':ctime' => $time,
            ':mtime' => $time
        );
        
        // SQL for the insert statement
        $sql    = 'INSERT INTO ' . $table . ' SET ctime = :ctime, mtime = :mtime,';
        
        // Checks for a connected used
        if( isset( $GLOBALS[ 'user' ] ) && $GLOBALS[ 'user' ] instanceof stdClass ) {
            
            // Adds the user ID
            $params[ ':id_users' ] = $GLOBALS[ 'user' ]->uid;
            $sql                  .= ' id_users = :id_users,';
        }
        
        // Process each value
        foreach( $values as $fieldName => $value ) {
            
            // Adds the PDO parameter for the current value
            $params[ ':' . $fieldName ] = $value;
            
            // Adds the update statement for the current value
            $sql .= ' ' . $fieldName . ' = :' . $fieldName . ',';
        }
        
        // Removes the last comma
        $sql  = substr( $sql, 0, -1 );
        
        // Prepares the PDO query
        $query = $this->prepare( $sql );
        
        // Executes the PDO query
        $query->execute( $params );
        
        // Returns the insert ID
        return $this->lastInsertId();
    }
    
    /**
     * 
     */
    public function updateRecord( $table, $id, array $values )
    {
        // Primary key
        $pKey   = 'id_' . $table;
        
        // Table name to support prefixes
        $table  = '{' . $table . '}';
        
        // Parameters for the PDO query
        $params = array(
            ':' . $pKey => $id,
            ':mtime'    => time()
        );
        
        // SQL for the update statement
        $sql    = 'UPDATE ' . $table . ' SET mtime = :mtime,';
        
        // Process each value
        foreach( $values as $fieldName => $value ) {
            
            // Adds the PDO parameter for the current value
            $params[ ':' . $fieldName ] = $value;
            
            // Adds the update statement for the current value
            $sql .= ' ' . $fieldName . ' = :' . $fieldName . ',';
        }
        
        // Removes the last comma
        $sql  = substr( $sql, 0, -1 );
        
        // Adds the where clause
        $sql .= ' WHERE ' . $pKey . ' = :' . $pKey;
        
        // Prepares the PDO query
        $query = $this->prepare( $sql );
        
        // Executes the PDO query
        return $query->execute( $params );
    }
    
    /**
     * 
     */
    public function deleteRecord( $table, $id, $deleteFromTable = false )
    {
        // Checks if we should really delete the record, or just set the delete flag
        if( $deleteFromTable ) {
            
            // Primary key
            $pKey   = '' . $table;
            
            // Table name to support prefixes
            $table  = '{' . $table . '}';
            
            // Parameters for the PDO query
            $params = array(
                ':id' => $id
            );
            
            // SQL for the update statement
            $sql = 'DELETE FROM ' . $table . ' WHERE ' . $pKey . ' = :id';
            
            // Prepares the PDO query
            $query = $this->prepare( $sql );
            
            // Executes the PDO query
            return $this->execute( $params );
            
        } else {
            
            // Just sets the delete flag
            return $this->updateRecord( $table, $id, array( 'deleted' => 1 ) );
        }
    }
    
    /**
     * 
     */
    public function removeDeletedRecords( $table )
    {
        // Table name to support prefixes
        $table  = '{' . $table . '}';
        
        // Prepares the PDO query
        $query = $this->prepare(
            'DELETE FROM ' . $table . ' WHERE deleted = 1'
        );
        
        // Executes the PDO query
        return $this->execute( $params );
    }
}
