<?php

# $Id$

/**
 * AOP advisor abstract that allows AOP style programming in child classes
 * 
 * Aspect-Oriented Programming (AOP) is a programming paradigm which complements
 * Object-Oriented Programming (OOP) by separating concerns of a software
 * application to improve modularization. The separation of concerns (SoC) aims
 * for making a software easier to maintain by grouping features and behaviour
 * into manageable parts which all have a specific purpose and business to take
 * care of.
 * 
 * Two categories of AOP advices can be used:
 * 
 * - The first kind is the same for all the child classes. It uses PHP
 * magic methods as global join points, meaning you can have specific code
 * executed when a call to a PHP magic method is made on the child class is
 * made. Join points are available for the following magic methods:
 * 
 *      -# __construct - Oop_Aop_Advisor::ADVICE_TYPE_CONSTRUCT
 *      -# __destruct  - Oop_Aop_Advisor::ADVICE_TYPE_DESTRUCT
 *      -# __clone     - Oop_Aop_Advisor::ADVICE_TYPE_CLONE
 *      -# __get       - Oop_Aop_Advisor::ADVICE_TYPE_GET
 *      -# __set       - Oop_Aop_Advisor::ADVICE_TYPE_SET
 *      -# __isset     - Oop_Aop_Advisor::ADVICE_TYPE_ISSET
 *      -# __unset     - Oop_Aop_Advisor::ADVICE_TYPE_UNSET
 *      -# __sleep     - Oop_Aop_Advisor::ADVICE_TYPE_SLEEP
 *      -# __wakeup    - Oop_Aop_Advisor::ADVICE_TYPE_WAKEUP
 *      -# __toString  - Oop_Aop_Advisor::ADVICE_TYPE_TO_STRING
 * 
 * - The second uses specific join points, defined in the child class by it's
 * author, using the _registerJoinPoint() method. Available advices are:
 * 
 *      -# Oop_Aop_Advisor::ADVICE_TYPE_VALID_CALL
 *         Called before the join point is executed, and may prevents the join
 *         point to be executed
 *      -# Oop_Aop_Advisor::ADVICE_TYPE_BEFORE_CALL
           Called before the join point is executed
 *      -# Oop_Aop_Advisor::ADVICE_TYPE_BEFORE_RETURN
 *         Called before the return value of the join point is return, and may
 *         change the return value
 *      -# Oop_Aop_Advisor::ADVICE_TYPE_AFTER_CALL
 *         Called after the join point is executed
 *      -# Oop_Aop_Advisor::ADVICE_TYPE_AFTER_THROWING
 *         Called after an exception is thrown from the join point. The
 *         original exception won't be thrown if the callback method
 *         returns true
 * 
 * Please take a look at the documentation for the addAdvice() method, to learn
 * more about the type of advices.
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Oop/Aop
 * @version         0.1
 */
abstract class Oop_Aop_Advisor
{
    /**
     * The types of AOP advices that can be used
     */
    const ADVICE_TYPE_ALL             = 0x00007FFF;
    const ADVICE_TYPE_CONSTRUCT       = 0x00000001;
    const ADVICE_TYPE_DESTRUCT        = 0x00000002;
    const ADVICE_TYPE_CLONE           = 0x00000004;
    const ADVICE_TYPE_GET             = 0x00000008;
    const ADVICE_TYPE_SET             = 0x00000010;
    const ADVICE_TYPE_ISSET           = 0x00000020;
    const ADVICE_TYPE_UNSET           = 0x00000040;
    const ADVICE_TYPE_SLEEP           = 0x00000080;
    const ADVICE_TYPE_WAKEUP          = 0x00000100;
    const ADVICE_TYPE_TO_STRING       = 0x00000200;
    const ADVICE_TYPE_VALID_CALL      = 0x00000400;
    const ADVICE_TYPE_BEFORE_CALL     = 0x00000800;
    const ADVICE_TYPE_BEFORE_RETURN   = 0x00001000;
    const ADVICE_TYPE_AFTER_CALL      = 0x00002000;
    const ADVICE_TYPE_AFTER_THROWING  = 0x00004000;
    
    /**
     * The join points defined in each child class
     */
    private static $_joinPoints       = array();
    
    /**
     * The name of the join points defined in each child class
     */
    private static $_joinPointsByName = array();
    
    /**
     * The registered AOP advices for each child class
     */
    private static $_advices          = array(
        array(),
        array(),
        array(),
        array(),
        array(),
        array(),
        array(),
        array(),
        array(),
        array(),
        array(),
        array(),
        array(),
        array(),
        array()
    );
    
    /**
     * A hash to identify a specific instance of a child class
     */
    private $_objectHash              = '';
    
    /**
     * The name of the child class
     */
    private $_className               = '';
    
    /**
     * Class constructor
     * 
     * This method is declared, so the advices can be launched for the child
     * classes. If you need to declare this method in a child class, remember
     * to call this one before anything else, using parent::__construct().
     * Otherwise, the advices won't be executed.
     * 
     * @return  NULL
     * @see     _processGlobalAdvices
     */
    public function __construct()
    {
        // Stores a hash for the current object, and gets the class name
        $this->_objectHash = spl_object_hash( $this );
        $this->_className  = get_class( $this );
        
        // Process the __construct advices
        self::_processGlobalAdvices(
            self::ADVICE_TYPE_CONSTRUCT,
            $this,
            __FUNCTION__
        );
    }
    
    /**
     * Class destructor
     * 
     * This method is declared, so the advices can be launched for the child
     * classes. If you need to declare this method in a child class, remember
     * to call this one before anything else, using parent::__destruct().
     * Otherwise, the advices won't be executed.
     * 
     * @return  NULL
     * @see     _processGlobalAdvices
     */
    public function __destruct()
    {
        // Process the __destruct advices
        self::_processGlobalAdvices(
            self::ADVICE_TYPE_DESTRUCT,
            $this,
            __FUNCTION__
        );
    }
    
    /**
     * Object cloning
     * 
     * This method is declared, so the advices can be launched for the child
     * classes. If you need to declare this method in a child class, remember
     * to call this one before anything else, using parent::__clone().
     * Otherwise, the advices won't be executed.
     * 
     * @return  NULL
     * @see     _processGlobalAdvices
     */
    public function __clone()
    {
        // Process the __clone advices
        self::_processGlobalAdvices(
            self::ADVICE_TYPE_CLONE,
            $this,
            __FUNCTION__
        );
    }
    
    /**
     * PHP getter method
     * 
     * This method is declared, so the advices can be launched for the child
     * classes. If you need to declare this method in a child class, remember
     * to call this one before anything else, using parent::__get().
     * Otherwise, the advices won't be executed.
     * 
     * @return  NULL
     * @see     _processGlobalAdvices
     */
    public function __get( $name )
    {
        // Process the __get advices
        self::_processGlobalAdvices(
            self::ADVICE_TYPE_GET,
            $this,
            __FUNCTION__,
            array( $name )
        );
    }
    
    /**
     * PHP setter method
     * 
     * This method is declared, so the advices can be launched for the child
     * classes. If you need to declare this method in a child class, remember
     * to call this one before anything else, using parent::__set().
     * Otherwise, the advices won't be executed.
     * 
     * @return  NULL
     * @see     _processGlobalAdvices
     */
    public function __set( $name, $value )
    {
        // Process the __set advices
        self::_processGlobalAdvices(
            self::ADVICE_TYPE_SET,
            $this,
            __FUNCTION__,
            array( $name, $value )
        );
    }
    
    /**
     * PHP __isset magic method
     * 
     * This method is declared, so the advices can be launched for the child
     * classes. If you need to declare this method in a child class, remember
     * to call this one before anything else, using parent::__isset().
     * Otherwise, the advices won't be executed.
     * 
     * @return  NULL
     * @see     _processGlobalAdvices
     */
    public function __isset( $name )
    {
        // Process the __isset advices
        self::_processGlobalAdvices(
            self::ADVICE_TYPE_ISSET,
            $this,
            __FUNCTION__,
            array( $name )
        );
    }
    
    /**
     * PHP __unset magic method
     * 
     * This method is declared, so the advices can be launched for the child
     * classes. If you need to declare this method in a child class, remember
     * to call this one before anything else, using parent::__unset().
     * Otherwise, the advices won't be executed.
     * 
     * @return  NULL
     * @see     _processGlobalAdvices
     */
    public function __unset( $name )
    {
        // Process the __unset advices
        self::_processGlobalAdvices(
            self::ADVICE_TYPE_UNSET,
            $this,
            __FUNCTION__,
            array( $name )
        );
    }
    
    /**
     * Object serialization
     * 
     * This method is declared, so the advices can be launched for the child
     * classes. If you need to declare this method in a child class, remember
     * to call this one before anything else, using parent::__sleep().
     * Otherwise, the advices won't be executed.
     * 
     * @return  NULL
     * @see     _processGlobalAdvices
     */
    public function __sleep()
    {
        // Process the __sleep advices
        self::_processGlobalAdvices(
            self::ADVICE_TYPE_SLEEP,
            $this,
            __FUNCTION__
        );
    }
    
    /**
     * Object un-serialization
     * 
     * This method is declared, so the advices can be launched for the child
     * classes. If you need to declare this method in a child class, remember
     * to call this one before anything else, using parent::__wakeup().
     * Otherwise, the advices won't be executed.
     * 
     * @return  NULL
     * @see     _processGlobalAdvices
     */
    public function __wakeup()
    {
        // Process the __wakeup advices
        self::_processGlobalAdvices(
            self::ADVICE_TYPE_WAKEUP,
            $this,
            __FUNCTION__
        );
    }
    
    /**
     * Object string conversion
     * 
     * This method is declared, so the advices can be launched for the child
     * classes. If you need to declare this method in a child class, remember
     * to call this one before anything else, using parent::__toString().
     * Otherwise, the advices won't be executed.
     * 
     * @return  NULL
     * @see     _processGlobalAdvices
     */
    public function __toString()
    {
        // Process the __toString advices
        self::_processGlobalAdvices(
            self::ADVICE_TYPE_TO_STRING,
            $this,
            __FUNCTION__
        );
    }
    
    /**
     * Process the advices for the PHP magic methods join points
     * 
     * @param   int             The type of the advice
     * @param   Oop_Aop_Advisor The instance of the child class (will be added as
     *                          first argument of the advice callback)
     * @param   string          The name of the PHP magic method
     * @param   array           An array with the arguments to pass to the advice
     *                          callback
     * @return  NULL        
     * @see     _invoke
     */
    private static function _processGlobalAdvices( $type, Oop_Aop_Advisor $object, $method, array $args = array() )
    {
        // Gets the class of the object
        $className = get_class( $object );
        
        // Checks for advices
        if( isset( self::$_advices[ $type ][ $className ] ) ) {
            
            // Adds the object instance to the arguments
            array_unshift( $args, $object );
            
            // Process the advices for the given type
            foreach( self::$_advices[ $type ][ $className ] as $callback ) {
                
                // Invokes the advice callback
                self::_invoke( $callback, $args, $object, $method );
            }
        }
    }
    
    /**
     * Calls a specific join point
     * 
     * This method is used to redirect calls on a specific join point to it's
     * internal method, executing all available AOP advices registered for
     * the join point.
     * 
     * @param   string                      The name of the join point to invoke
     * @param   array                       The arguments to pass to the join point
     *                                      method
     * @return  mixed                       The return value of the join point method
     * @throws  Oop_Aop_Advisor_Exception   If the called join point has not been
     *                                      registered
     * @see     _invoke
     */
    public function __call( $name, array $args = array() )
    {
        // Checks if the join point has been registered
        if( !isset( self::$_joinPoints[ $this->_className ][ $this->_objectHash ][ $name ] ) ) {
            
            // Error - The join point has not been registered
            throw new Oop_Aop_Advisor_Exception( 'No joint point named ' . $name . '. Call to undefined method ' . $this->_className . '::' . $name . '()', Oop_Aop_Advisor_Exception::EXCEPTION_NO_JOINPOINT );
        }
        
        // Gets the method to use and the allowed advices type for the join point
        $method            = self::$_joinPoints[ $this->_className ][ $this->_objectHash ][ $name ][ 0 ];
        $allowedAdviceType = self::$_joinPoints[ $this->_className ][ $this->_objectHash ][ $name ][ 1 ];
        
        // By default, the call on the join point internal method is allowed
        $valid = true;
        
        // Checks if we can have advices to validate the call to the internal method
        if( self::ADVICE_TYPE_VALID_CALL & $allowedAdviceType ) {
            
            // Process each validation advice
            foreach( self::$_advices[ self::ADVICE_TYPE_VALID_CALL ][ $this->_className ][ $name ] as $advice ) {
                
                // Checks if the advice can be called on the current instance
                if( $advice[ 1 ] === false || $advice[ 1 ] === $this->_objectHash ) {
                    
                    // Invokes the validation advice
                    $valid = self::_invoke( $advice[ 0 ], $args, $this, $name );
                    
                    // Checks if an advice is preventing the execution of the internal method
                    if( $valid === false ) {
                        
                        // Internal method won't be executed - No need to process the following advices
                        break;
                    }
                }
            }
        }
        
        // Checks if the internal method can be executed
        if( $valid ) {
            
            // Checks if we can have advices before the call to the internal method
            if( self::ADVICE_TYPE_BEFORE_CALL & $allowedAdviceType ) {
                
                // Process each 'beforeCall' advice
                foreach( self::$_advices[ self::ADVICE_TYPE_BEFORE_CALL ][ $this->_className ][ $name ] as $advice ) {
                    
                    // Checks if the advice can be called on the current instance
                    if( $advice[ 1 ] === false || $advice[ 1 ] === $this->_objectHash ) {
                        
                        // Invokes the advice
                        self::_invoke( $advice[ 0 ], $args, $this, $name );
                    }
                }
            }
            
            // We are catching exceptions from the internal method, so we'll be able to run the 'afterThrowing' advices
            try {
                
                // Gets the return value of the internal method
                $returnValue = self::_invoke( array( $this, $method ), $args );
                
            } catch( Exception $e ) {
                
                // Checks if we can have advices to catch exceptions thrown from the internal method, and if so, if such advices are available
                if(    !count( self::$_advices[ self::ADVICE_TYPE_AFTER_THROWING ][ $this->_className ][ $name ] )
                    || !( self::ADVICE_TYPE_AFTER_THROWING & $allowedAdviceType )
                ) {
                    
                    // No - Throws the original exception back
                    throw $e;
                    
                } else {
                    
                    // Boolean value to check if the exception has been caught, and so should not be thrown
                    $exceptionCaught = false;
                    
                    // Process each 'afterThrowing' advice
                    foreach( self::$_advices[ self::ADVICE_TYPE_AFTER_THROWING ][ $this->_className ][ $name ] as $advice ) {
                        
                        // Checks if the advice can be called on the current instance
                        if( $advice[ 1 ] === false || $advice[ 1 ] === $this->_objectHash ) {
                            
                            // Invokes the advice
                            $exceptionCaught = self::_invoke( $advice[ 0 ], array( $e, $this ), $this, $name );
                            
                            // Checks if the exception has been caught by the advice
                            if( $exceptionCaught === true ) {
                                
                                // Exception was caught - Do not executes the next advices
                                break;
                            }
                        }
                    }
                    
                    // Checks if the exception has been caught by the advice
                    if( $exceptionCaught !== true ) {
                        
                        // Exception was not caught by an advice - Throws it back
                        throw $e;
                    }
                }
            }
            
            // Checks if we have a return value, meaning no exception was thrown
            if( isset( $returnValue ) ) {
                
                // Checks if we ca have advices that will be able to modify the return value of the internal method
                if( self::ADVICE_TYPE_BEFORE_RETURN & $allowedAdviceType ) {
                    
                    // Process each 'beforeReturn' advice
                    foreach( self::$_advices[ self::ADVICE_TYPE_BEFORE_RETURN ][ $this->_className ][ $name ] as $advice ) {
                        
                        // Checks if the advice can be called on the current instance
                        if( $advice[ 1 ] === false || $advice[ 1 ] === $this->_objectHash ) {
                            
                            // Invokes the advice and stores the return value
                            $returnValue = self::_invoke( $advice[ 0 ], array( $returnValue, $args ), $this, $name );
                        }
                    }
                }
                
                // Checks if we can have advices after the call to the internal method
                if( self::ADVICE_TYPE_AFTER_CALL & $allowedAdviceType ) {
                    
                    // Process each 'afterCall' advice
                    foreach( self::$_advices[ self::ADVICE_TYPE_AFTER_CALL ][ $this->_className ][ $name ] as $advice ) {
                        
                        // Checks if the advice can be called on the current instance
                        if( $advice[ 1 ] === false || $advice[ 1 ] === $this->_objectHash ) {
                            
                            // Invokes the advice
                            self::_invoke( $advice[ 0 ], $args, $this, $name );
                        }
                    }
                }
                
                // Returns the return value
                return $returnValue;
            }
        }
    }
    
    /**
     * Invokes a callback
     * 
     * This method is used to avoid having to call the call_user_func_array()
     * function, which is slow and may have problems dealing with references.
     * The call_user_func_array() will only be used if more than ten arguments
     * must be passed to the callback, or if the callback is on a static class
     * method, as late static bindings are only available since PHP 5.3.
     * 
     * @param   mixed                       The callback to invoke (must be a valid
     *                                      PHP callback)
     * @param   array                       The arguments to pass to the callback
     * @param   string                      The joint point for which the callback
     *                                      is executed (used for the error messages)
     * @return  mixed                       The return value of the callback
     * @throws  Oop_Aop_Advisor_Exception   If the passed callback is not a valid
     *                                      PHP callback
     */
    private static function _invoke( $callback, array $args = array(), $joinPoint = '' )
    {
        // Ensures the callback is valid
        if( !is_callable( $callback ) ) {
            
            // Creates the exception message
            $exceptionMessage = ( $joinPoint ) ? 'Invalid advice callback for join point ' . $joinPoint : 'Invalid callback';
            
            // Error - The callback is not valid
            throw new Oop_Aop_Advisor_Exception( $exceptionMessage, Oop_Aop_Advisor_Exception::EXCEPTION_INVALID_ADVICE_TYPE_CALLBACK );
        }
        
        // Gets the number of arguments to pass to the callbak
        $argsCount = count( $args );
        
        // Checks if the callback is an array
        if( is_array( $callback ) ) {
            
            // Check if we need to make a member or a static call
            if( is_object( $callback[ 0 ] ) ) {
                
                // Gets the object and the method to use
                $object = $callback[ 0 ];
                $method = $callback[ 1 ];
                
                // Checks the number of arguments
                // This will avoid a call to call_user_func_array if the number of arguments is lower than ten
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
                    
                    // More than ten arguments - We'll use call_user_func_array()
                    default:
                        
                        return call_user_func_array( $callback, $args );
                        break;
                }
                
            }
            
            // Static call - We'll use call_user_func_array() as late static bindings are only available since PHP 5.3
            return call_user_func_array( $callback, $args );
        }
        
        // Checks the number of arguments
        // This will avoid a call to call_user_func_array if the number of arguments is lower than ten
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
                
                // More than ten arguments - We'll use call_user_func_array()
                return call_user_func_array( $callback, $args );
                break;
        }
    }
    
    /**
     * Adds an AOP advice on a target
     * 
     * This method registers a callback as an AOP advice, for a target. The
     * target can be a class name or an instance of class. In the first case,
     * the advice callback will be executed on all instances of the class. In
     * the second case, it will only be executed for the given instance.
     * 
     * Two categories of advice types are available:
     * 
     * - The first one allows you to execute code when a PHP magic method is
     * called on the target. The advices types are, in such a case:
     * 
     *      -# Oop_Aop_Advisor::ADVICE_TYPE_CONSTRUCT
     *         Called when the constructor of the target class is called
     *      -# Oop_Aop_Advisor::ADVICE_TYPE_DESTRUCT
     *         Called when the destructor of the target class is called
     *      -# Oop_Aop_Advisor::ADVICE_TYPE_CLONE
     *         Called when an object of the target class is cloned
     *      -# Oop_Aop_Advisor::ADVICE_TYPE_SLEEP
     *         Called when an object of the target class is serialized
     *      -# Oop_Aop_Advisor::ADVICE_TYPE_WAKEUP
     *         Called when an object of the target class is unserialized
     *      -# Oop_Aop_Advisor::ADVICE_TYPE_TO_STRING
     *         Called when an object of the target class is converted to a string
     * 
     * - The second one allows you to execute specific code on specific join
     * points, registered in the target class. In that case, you must specify
     * the name of the join point on which to place the advice as the fourth
     * parameter. Available advices types in such a case are:
     * 
     *      -# Oop_Aop_Advisor::ADVICE_TYPE_VALID_CALL
     *         Called before the join point is executed, and may prevents the join
     *         point to be executed
     *      -# Oop_Aop_Advisor::ADVICE_TYPE_BEFORE_CALL
     *         Called before the join point is executed
     *      -# Oop_Aop_Advisor::ADVICE_TYPE_BEFORE_RETURN
     *         Called before the return value of the join point is return, and may
     *         change the return value
     *      -# Oop_Aop_Advisor::ADVICE_TYPE_AFTER_CALL
     *         Called after the join point is executed
     *      -# Oop_Aop_Advisor::ADVICE_TYPE_AFTER_THROWING
     *         Called after an exception is thrown from the join point. The
     *         original exception won't be thrown if the callback method
     *         returns true
     * 
     * @param   int                         The type of the advice (one of the
     *                                      Oop_Aop_Advisor::ADVICE_TYPE_XXX
     *                                      constant)
     * @param   mixed                       The callback to invoke (must be a valid
     *                                      PHP callback)
     * @param   mixed                       The target on which to place the advice
     *                                      (either a class name or an object)
     * @param   string                      The join point on which to place the
     *                                      advice
     * @return  boolean
     * @throws  Oop_Aop_Advisor_Exception   If the join point has not been
     *                                      registered in the target
     * @throws  Oop_Aop_Advisor_Exception   If the advice type is not allowed for
     *                                      the join point
     * @throws  Oop_Aop_Advisor_Exception   If the advice type does not exist
     */
    final public static function addAdvice( $type, $callback, $target, $joinPoint = '' )
    {
        // Checks if the callback must be executed for a specific object or for all instances
        if( is_object( $target ) ) {
            
            // Gets the class name and object hash, so the callback will be added for the specific object only
            $className  = get_class( $target );
            $objectHash = spl_object_hash( $target );
            
        } else {
            
            // Callback will be executed for all instances
            $className  = $target;
            $objectHash = false;
        }
        
        // Checks if the advice type is for a specific join point or not
        if(    $type === self::ADVICE_TYPE_CONSTRUCT
            || $type === self::ADVICE_TYPE_DESTRUCT
            || $type === self::ADVICE_TYPE_CLONE
            || $type === self::ADVICE_TYPE_SLEEP
            || $type === self::ADVICE_TYPE_WAKEUP
            || $type === self::ADVICE_TYPE_TO_STRING
        ) {
            
            // Checks if the storage array for the advice exists
            if( !isset( self::$_advices[ $type ][ $className ] ) ) {
                
                // Creates the storage array for the advice
                self::$_advices[ $type ][ $className ] = array();
            }
            
            // Adds the advice callback for the join point
            self::$_advices[ $type ][ $className ][] = $callback;
            return true;
            
        } else {
            
            // Checks if the join point exists
            if( !isset( self::$_joinPointsByName[ $className ][ $joinPoint ] ) ) {
                
                // Error - No such join point in the target class
                throw new Oop_Aop_Advisor_Exception( 'The join point ' . $joinPoint .' does not exist in class ' . $className, Oop_Aop_Advisor_Exception::EXCEPTION_NO_JOINPOINT );
            }
            
            // Checks the advice type
            if(    $type === self::ADVICE_TYPE_VALID_CALL
                || $type === self::ADVICE_TYPE_BEFORE_CALL
                || $type === self::ADVICE_TYPE_BEFORE_RETURN
                || $type === self::ADVICE_TYPE_AFTER_CALL
                || $type === self::ADVICE_TYPE_AFTER_THROWING
            ) {
                
                // Storage for the allowed advice types
                $allowedAdviceTypes = 0;
                
                // Process the join points for each instance of the target class
                foreach( self::$_joinPoints[ $className ] as $joinPoints ) {
                    
                    // Adds the allowed types of advices for the joint point
                    $allowedAdviceTypes |= $joinPoints[ $joinPoint ][ 1 ];
                }
                
                // Checks if the advice type is allowed
                if( $type & $allowedAdviceTypes ) {
                    
                    // Adds the advice callback for the join point
                    self::$_advices[ $type ][ $className ][ $joinPoint ][] = array( $callback, $objectHash );
                    return true;
                
                } else {
                    
                    // Error - The advice type is not allowed for the join point
                    throw new Oop_Aop_Advisor_Exception( 'Advice of type ' . $type . ' is not permitted for join point ' . $joinPoint, Oop_Aop_Advisor_Exception::EXCEPTION_ADVICE_TYPE_NOT_PERMITTED );
                }
                
            }
        }
        
        // Error - Advice type is invalid
        throw new Oop_Aop_Advisor_Exception( 'Invalid advice type (' . $type . ')', Oop_Aop_Advisor_Exception::EXCEPTION_INVALID_ADVICE_TYPE );
    }
    
    /**
     * Registers an AOP join point
     * 
     * This function allows you to register an AOP join point for a specific
     * method of your class. That will allow people to add advices on your
     * join point. Once the join point is registered, please note that you'll
     * have to call your method by the name of the join point, not directly by
     * it's name. Otherwise, the advices won't be called.
     * You can also control which type of advice is available for your join
     * point by giving a value in the third parameter. For instance for a
     * join point named 'joinPointName', using the method 'internalMethodName',
     * allowing only the 'before' and 'after' advices types, use the following
     * statement:
     * 
     * <code>
     * $this->_registerJoinPoint(
     *      'joinPointName',
     *      'internalMethodName',
     *      Oop_Aop_Advisor::ADVICE_TYPE_AFTER_CALL | Oop_Aop_Advisor::ADVICE_TYPE_AFTER_CALL
     * );
     * </code>
     * 
     * @param   string                      The name of the join point
     * @param   string                      The method to use when the join point
     *                                      is called
     * @param   int                         The type of advices that are available
     *                                      for the join point (typically a bitwise
     *                                      operation with some
     *                                      Oop_Aop_Advisor::ADVICE_TYPE_XXX
     *                                      constants)
     * @return  NULL
     * @throws  Oop_Aop_Advisor_Exception   If the joint point method does not exist
     * @throws  Oop_Aop_Advisor_Exception   If a join point with the same name is
     *                                      already registered
     */
    final protected function _registerJoinPoint( $name, $method, $availableAdviceTypes = 0 )
    {
        // Checks of the method for the join point exists
        if( !method_exists( $this, $method ) ) {
            
            // Error - The method does not exist
            throw new Oop_Aop_Advisor_Exception( 'The method ' . $method . ' for join point ' . $name .' does not exist in class ' . $this->_className, Oop_Aop_Advisor_Exception::EXCEPTION_NO_JOINPOINT_METHOD );
        }
        
        // Checks if the storage array for the join points of the child class already exists
        if( !isset( self::$_joinPoints[ $this->_className ] ) ) {
            
            // Creates the storage arrays for the join points
            self::$_joinPoints[ $this->_className ]                             = array();
            self::$_joinPointsByName[ $this->_className ]                       = array();
            
            // Creates the storage arrays for the advices
            self::$_advices[ self::ADVICE_TYPE_VALID_CALL ][ $this->_className ]     = array();
            self::$_advices[ self::ADVICE_TYPE_BEFORE_CALL ][ $this->_className ]    = array();
            self::$_advices[ self::ADVICE_TYPE_BEFORE_RETURN ][ $this->_className ]  = array();
            self::$_advices[ self::ADVICE_TYPE_AFTER_CALL ][ $this->_className ]     = array();
            self::$_advices[ self::ADVICE_TYPE_AFTER_THROWING ][ $this->_className ] = array();
        }
        
        // Checks if the storage array for the joint points of the current object already exists
        if( !isset( self::$_joinPoints[ $this->_className ][ $this->_objectHash ] ) ) {
            
            // Creates the storage array for the join points of the current object
            self::$_joinPoints[ $this->_className ][ $this->_objectHash ] = array();
        }
        
        // Checks if a join point with the same name has already been registered
        if( isset( self::$_joinPoints[ $this->_className ][ $this->_objectHash ][ $name ] ) ) {
            
            // Error - A joint point with the same name already exists
            throw new Oop_Aop_Advisor_Exception( 'A join point named ' . $name . ' is already registered for object ' . $this->_objectHash . ' of class ' . $this->_className, Oop_Aop_Advisor_Exception::EXCEPTION_JOINPOINT_EXISTS );
        }
        
        // Creates the storage arrays for the advices on the join point
        self::$_advices[ self::ADVICE_TYPE_VALID_CALL ][ $this->_className ][ $name ]     = array();
        self::$_advices[ self::ADVICE_TYPE_BEFORE_CALL ][ $this->_className ][ $name ]    = array();
        self::$_advices[ self::ADVICE_TYPE_BEFORE_RETURN ][ $this->_className ][ $name ]  = array();
        self::$_advices[ self::ADVICE_TYPE_AFTER_CALL ][ $this->_className ][ $name ]     = array();
        self::$_advices[ self::ADVICE_TYPE_AFTER_THROWING ][ $this->_className ][ $name ] = array();
        
        // Checks if we have specific advice type
        if( $availableAdviceTypes === 0 ) {
            
            // All types of advices are available by default
            $availableAdviceTypes = self::ADVICE_TYPE_ALL;
        }
        
        // Registers the join point
        self::$_joinPoints[ $this->_className ][ $this->_objectHash ][ $name ]       = array( $method, $availableAdviceTypes );
        self::$_joinPointsByName[ $this->_className ][ $name ]                       = true;
    }
    
    /**
     * Checks if a join point exists in a class
     * 
     * @param   string  The name of the class
     * @param   string  The name of the join point
     * @return  boolean
     */
    final public static function joinPointExists( $className, $joinPoint )
    {
        return isset( self::$_joinPointsByName[ $className ][ $joinPoint ] );
    }
}
