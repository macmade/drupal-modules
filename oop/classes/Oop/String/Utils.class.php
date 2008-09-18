<?php

/**
 * String utilities
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Oop/String
 * @version         0.1
 */
final class Oop_String_Utils
{
    /**
     * The unique instance of the class (singleton)
     */
    private static $_instance = NULL;
    
    /**
     * Class constructor
     * 
     * The class constructor is private to avoid multiple instances of the
     * class (singleton).
     * 
     * @return NULL
     */
    private function __construct()
    {}
    
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
     * Character code encryption
     * 
     * This method shifts the input character code with the specified offset,
     * within a given character range. Based on TYPO3 method encryptCharCode
     * of class tslib_fe, by Kasper Skårhøj.
     * 
     * @param   int     The input character code
     * @param   int     The starting character code for the range
     * @param   int     The ending character code for the range
     * @param   int     The offset for the encryption
     * @return  int     The encrypted character code
     */
    protected function _cryptCharCode( $charCode, $start, $end, $offset ) {
        
        // Adds the offset to the character
        $charCode += $offset;
        
        // Checks the offset and the range
        if ( $offset > 0 && $charCode > $end ) {
            
            // Computes the new character
            $charCode = $start + ( $charCode - $end - 1 );
            
        } else if ( $offset < 0 && $charCode < $start ) {
            
            // Computes the new character
            $charCode = $end - ( $start - $charCode - 1 );
        }
        
        // Returns the new character
        return $charCode;
    }
    
    /**
     * Encrypts / Decrypts an email address
     * 
     * This method is used to encrypt / decrypt and email address,
     * by shifting each character inside its range, in order to prevent
     * spammers to get the address.  Based on TYPO3 method encryptEmail
     * of class tslib_fe, by Kasper Skårhøj.
     * 
     * @param   string  The email to encrypt / decrypt
     * @param   boolean Encryption process direction ( true = crypt / false = decrypt )
     * @return  string  The encrypted / decrypted email
     */
    public function cryptEmail( $email, $reverse = false ) {
            
        // Storage
        $crypt = '';
        
        // Process each character of the input email
        for ( $i = 0; $i < strlen( $email ); $i++ ) {
            
            // Stores the current character
            $charValue = substr( $email, $i, $i + 1 );
            
            // Gets the character ASCII value
            $charCode = ord( $charValue );
            
            // Checks the ASCII range
            if ( $charCode >= 33 && $charCode <= 126 ) {
                    
                // Offset for the encryption / decryption
                $offset = ( $reverse ) ? -1 : 1;
                
                // Crypts the character
                $charValue = chr( $this->_cryptCharCode( $charCode, 33, 126, $offset ) );
            }
            
            // Adds the character
            $crypt .= $charValue;
        }
        
        // Return encrypted / decrypted email
        return $crypt;
    }
}
