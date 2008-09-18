<?php

# $Id$

/**
 * Callback helper class
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Oop/Core
 * @version         0.1
 */
class Oop_Callback_Helper
{
    /**
     * Class constructor
     * 
     * The class constructor is private as this class should not been instanciated.
     * 
     * @return NULL
     */
    private function __construct()
    {}
    
    /**
     * 
     */
    public static function apply( $callback, array $args = array() )
    {
        $argsCount = count( $args );
        
        if( $argCount > 10 ) {
            
            trigger_error( 'More than ten arguments were passed to callback, so the call_user_func_array() function was used', E_USER_NOTICE );
        }
        
        if( is_array( $callback ) ) {
            
            if( !isset( $callback[ 0 ] ) || !isset( $callback[ 0 ] ) ) {
                
                throw new Oop_Callback_Helper_Exception( 'Invalid callback', Oop_Callback_Helper_Exception::EXCEPTION_INVALID_CALLBACK );
            }
            
            if( is_object( $callback[ 0 ] ) ) {
                
                $object = $callback[ 0 ];
                $method = $callback[ 1 ];
                
                switch( $argsCount ) {
                    
                    case 0:
                        
                        return $object->$method();
                        break;
                    
                    case 1:
                        
                        return $object->$method( $args[ 0 ] );
                        break;
                    
                    case 2:
                        
                        return $object->$method( $args[ 0 ], $args[ 1 ] );
                        break;
                    
                    case 3:
                        
                        return $object->$method( $args[ 0 ], $args[ 1 ], $args[ 2 ] );
                        break;
                    
                    case 4:
                        
                        return $object->$method( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ] );
                        break;
                    
                    case 5:
                        
                        return $object->$method( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ] );
                        break;
                    
                    case 6:
                        
                        return $object->$method( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ], $args[ 5 ] );
                        break;
                    
                    case 7:
                        
                        return $object->$method( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ], $args[ 5 ], $args[ 6 ] );
                        break;
                    
                    case 8:
                        
                        return $object->$method( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ], $args[ 5 ], $args[ 6 ], $args[ 7 ] );
                        break;
                    
                    case 9:
                        
                        return $object->$method( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ], $args[ 5 ], $args[ 6 ], $args[ 7 ], $args[ 8 ] );
                        break;
                    
                    case 10:
                        
                        return $object->$method( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ], $args[ 5 ], $args[ 6 ], $args[ 7 ], $args[ 8 ], $args[ 9 ] );
                        break;
                    
                    default:
                        
                        return call_user_func_array( $callback, $arguments );
                        break;
                }
                
            }
            
            return call_user_func_array( $callback, $arguments );
        }
        
        switch( $argsCount ) {
                    
            case 0:
                
                return $callback();
                break;
            
            case 1:
                
                return $callback( $args[ 0 ] );
                break;
            
            case 2:
                
                return $callback( $args[ 0 ], $args[ 1 ] );
                break;
            
            case 3:
                
                return $callback( $args[ 0 ], $args[ 1 ], $args[ 2 ] );
                break;
            
            case 4:
                
                return $callback( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ] );
                break;
            
            case 5:
                
                return $callback( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ] );
                break;
            
            case 6:
                
                return $callback( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ], $args[ 5 ] );
                break;
            
            case 7:
                
                return $callback( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ], $args[ 5 ], $args[ 6 ] );
                break;
            
            case 8:
                
                return $callback( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ], $args[ 5 ], $args[ 6 ], $args[ 7 ] );
                break;
            
            case 9:
                
                return $callback( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ], $args[ 5 ], $args[ 6 ], $args[ 7 ], $args[ 8 ] );
                break;
            
            case 10:
                
                return $callback( $args[ 0 ], $args[ 1 ], $args[ 2 ], $args[ 3 ], $args[ 4 ], $args[ 5 ], $args[ 6 ], $args[ 7 ], $args[ 8 ], $args[ 9 ] );
                break;
            
            default:
                
                return call_user_func_array( $callback, $arguments );
                break;
        }
    }
}
