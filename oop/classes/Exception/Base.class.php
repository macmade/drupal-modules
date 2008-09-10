<?php

/**
 * Abstract for the exception classes
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Exception
 * @version         0.1
 */
abstract class Exception_Base extends Exception
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
     * Wheter to print the backtrace or not, when an exception is not caught
     */
    protected static $_debug        = true;
    
    /**
     * The common CSS styles for the HTML elements produced by this class
     */
    protected static $_commonStyles = 'font-family: Verdana, sans-serif; font-size: 10px; color: #666666;';
    
    /**
     * Class constructor
     * 
     * The class constructor is declared so the message argument is mandatory.
     * 
     * @param   string  The exception message
     * @param   int     The exception code, if any
     * @return  NULL
     */
    public function __construct( $message, $code = 0 )
    {
        // Calls the Exception constructor
        parent::__construct( $message, $code );
    }
    
    /**
     * 
     */
    public function __toString()
    {
        // Checks the debug state
        if( self::$_debug ) {
            
            // Prints the backtrace
            print $this->getInfos();
        }
        
        // Returns the exception message
        return get_class( $this ) . ' exception with message "' . $this->message . '"';
    }
    
    /**
     * 
     */
    protected function _traceTitle( $title )
    {
        // Returns a title
        return '<h1 style="' . self::$_commonStyles . ' color: #FF0000;">' . $title . '</h1>';
    }
    
    /**
     * 
     */
    protected function _traceInfo( $label, $value )
    {
        // Creates the label DIV
        $labelDiv = '<div style="' . self::$_commonStyles . ' font-weight: bold;">' . $label . '</div>';
        
        // Creates the value DIV
        $valueDiv = '<div style="' . self::$_commonStyles . '">' . $value . '</div>';
        
        // Returns the trace information
        return '<div style="margin-bottom: 5px;">' . $labelDiv . $valueDiv . '</div>';
    }
    
    /**
     * 
     */
    protected function _traceHistory()
    {
        // Gets the trace array
        $traceArray = $this->getTrace();
        
        // Storage
        $str        = '';
        
        // Checks the trace array
        if( is_array( $traceArray ) ) {
            
            // Process each trace entry
            foreach( $traceArray as $key => $value  ) {
                
                // Gets the available informations from the trace entry
                $file     = ( isset( $value[ 'file' ] ) )                              ? $this->_traceInfo( 'File:', $value[ 'file' ] )                                            : '';
                $line     = ( isset( $value[ 'line' ] ) )                              ? $this->_traceInfo( 'Line:', $value[ 'line' ] )                                            : '';
                $function = ( isset( $value[ 'function' ] ) )                          ? $this->_traceInfo( 'Function:', $value[ 'function' ] )                                    : '';
                $class    = ( isset( $value[ 'class' ] ) )                             ? $this->_traceInfo( 'Class:', $value[ 'class' ] )                                          : '';
                $type     = ( isset( $value[ 'type' ] ) )                              ? $this->_traceInfo( 'Call type:', ( ( $value[ 'type' ] == '::' ) ? 'static' : 'member' ) ) : '';
                $code     = ( isset( $value[ 'file' ] ) && isset( $value[ 'line' ] ) ) ? $this->_traceInfo( 'Code:', $this->_getCode( $value[ 'file' ], $value[ 'line' ] ) )       : '';
                
                // Checks for arguments
                if( isset( $value[ 'args' ] ) && is_array( $value[ 'args' ] ) && count( $value[ 'args' ] ) ) {
                    
                    // Gets the pased arguments
                    $args = $this->_traceInfo( 'Arguments:', $this->_getArgs( $value[ 'args' ] ) );
                    
                } else {
                    
                    $args = '';
                }
                
                $str .= '<div style="margin-top: 5px; padding: 5px; border: solid 1px #58B4EC; background-color: #FFFFFF;">' . $class . $function . $type . $file . $line . $args . $code . '</div>';
            }
        }
        
        return $str;
    }
    
    /**
     * 
     */
    protected function _getArgs( array $args )
    {
        // Storage
        $argsList = '';
        
        // Process each argument
        foreach( $args as $argNum => $argValue ) {
            
            // Checks the type of the argument
            if( is_object( $argValue ) ) {
                
                // Object - Shows the class name 
                $argType = 'Object: ' . get_class( $argValue );
                
            } elseif( is_array( $argValue ) ) {
                
                // Array - Shows the number of entry
                $argType = 'Array: ' . count( $argValue );
                
            } elseif( is_bool( $argValue ) ) {
                
                // Boolean - Shows the value
                $argType = 'Boolean: ' . ( ( $argValue ) ? 'true' : 'false' );
                
            } elseif( is_int( $argValue ) ) {
                
                // Integer - Shows the value
                $argType = 'Integer: ' . $argValue;
                
            } elseif( is_float( $argValue ) ) {
                
                // Float - Shows the value
                $argType = 'Floating point: ' . $argValue;
                
            } elseif( is_resource( $argValue ) ) {
                
                // Resource - Shows the resource type
                $argType = 'Ressource: ' . get_resource_type( $argValue );
                
            } elseif( is_null( $argValue ) ) {
                
                // NULL
                $argType = 'Null';
                
            } elseif( is_string( $argValue ) ) {
                
                // String - Shows the value
                $argType = ( strlen( $argValue ) > 128 ) ? 'String: ' . htmlspecialchars( substr( $argValue, 0, 128 ) ) . '[...]' : 'String: ' . htmlspecialchars( $argValue );
                
            } else {
                
                // Unknown
                $argType = 'Other';
            }
            
            // Adds the current argument
            $argsList .= $argNum . ') ' . $argType . '<br />';
        }
        
        // Returns the list of the arguments
        return $argsList;
    }
    
    /**
     * 
     */
    protected function _getCode( $file, $line )
    {
        // Gets all the lines from the file
        $lines = file( $file );
        
        // Checks the lines array
        if( is_array( $lines ) ) {
            
            // Length of the last line
            $lineLength = strlen( $line + 3 );
            
            // Gets the given line
            $line3      = ( isset( $lines[ $line - 1 ] ) ) ? '<strong style="color: #000000;">' . str_pad( $line, $lineLength, 0, STR_PAD_LEFT ) . ': ' . $lines[ $line -1 ] . '</strong>' : '';
            
            // Gets some lines above and below the given one
            $line0      = ( isset( $lines[ $line - 4 ] ) ) ? str_pad( $line - 3, $lineLength, 0, STR_PAD_LEFT ) . ': ' . htmlspecialchars( $lines[ $line - 4 ] ) : '';
            $line1      = ( isset( $lines[ $line - 3 ] ) ) ? str_pad( $line - 2, $lineLength, 0, STR_PAD_LEFT ) . ': ' . htmlspecialchars( $lines[ $line - 3 ] ) : '';
            $line2      = ( isset( $lines[ $line - 2 ] ) ) ? str_pad( $line - 1, $lineLength, 0, STR_PAD_LEFT ) . ': ' . htmlspecialchars( $lines[ $line - 2 ] ) : '';
            $line4      = ( isset( $lines[ $line ] ) )     ? str_pad( $line + 1, $lineLength, 0, STR_PAD_LEFT ) . ': ' . htmlspecialchars( $lines[ $line ] )     : '';
            $line5      = ( isset( $lines[ $line + 1 ] ) ) ? str_pad( $line + 2, $lineLength, 0, STR_PAD_LEFT ) . ': ' . htmlspecialchars( $lines[ $line + 1 ] ) : '';
            $line6      = ( isset( $lines[ $line + 2 ] ) ) ? str_pad( $line + 3, $lineLength, 0, STR_PAD_LEFT ) . ': ' . htmlspecialchars( $lines[ $line + 2 ] ) : '';
            
            // Returns the lines
            return '<div style="' . self::$_commonStyles . ' white-space: pre; font-family: monospace; border: solid 1px #58B4EC; color: #FFFFFF; background-color: #58B4EC; padding: 5px; margin-top: 5px;">' . $line0 . $line1 . $line2 . $line3 . $line4 . $line5 . $line6 . '</div>';
        }
        
        // No lines
        return '';
    }
    
    /**
     * 
     */
    public static function setDebugState( $value )
    {
        // Gets the previous state
        $oldState     = self::$_debug;
        
        // Sets the new state
        self::$_debug = ( boolean )$value;
        
        // Returns the previous state
        return $oldState;
    }
    
    /**
     * 
     */
    public function getInfos()
    {        
        $trace = '<div style="' . self::$_commonStyles . ' background-color: #CCE8F9; border: solid 1px #58B4EC; margin: 10px; padding: 10px;">'
               . $this->_traceTitle( 'Exception of type \'' . get_class( $this ) . '\'' )
               . $this->_traceInfo( 'Message:', $this->message )
               . $this->_traceInfo( 'Code:',    $this->code )
               . $this->_traceInfo( 'File:',    $this->file )
               . $this->_traceInfo( 'Line:',    $this->line )
               . $this->_traceTitle( 'Debug backtrace:' )
               . $this->_traceHistory()
               . '</div>';
        
        return $trace;
    }
}
