// <![CDATA[

function Oop()
{
    /**
     * Character code encryption.
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
    function _cryptCharCode( charCode, start, end, offset )
    {
        // Adds the offset to the character
        charCode += offset;
        
        // Checks the offset and the range
        if ( offset > 0 && charCode > end ) {
            
            // Computes the new character
            charCode = start + ( charCode - end - 1 );
            
        } else if ( offset < 0 && charCode < start ) {
            
            // Computes the new character
            charCode = end - ( start - charCode - 1 );
        }
        
        // Returns the new character
        return charCode;
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
     * @param   boolean Encryption process direction ( 0 = crypt / 1 = decrypt )
     * @return  string  The encrypted / decrypted email
     */
    this.cryptEmail = function( email, reverse )
    {
        // Storage
        crypt = '';
        
        // Process each character of the input email
        for ( i = 0; i < email.length; i++ ) {
            
            // Stores the current character
            charValue = email.charAt( i );
            
            // Gets the character ASCII value
            charCode = email.charCodeAt( i );
            
            // Checks the ASCII range
            if ( charCode >= 33 && charCode <= 126 ) {
                    
                // Offset for the encryption / decryption
                offset = ( reverse ) ? -1 : 1;
                
                // Crypts the character
                charValue = String.fromCharCode( _cryptCharCode( charCode, 33, 126, offset ) );
            }
            
            // Add character
            crypt = crypt + charValue;
        }
        
        // Returns the encrypted / decrypted email
        return crypt;
    }
    
    /**
     * Decrypts an email address
     * 
     * @param   string  The email to decrypt
     * @return  void
     */
    this.decryptEmail = function( email ) {
        
        // Decrypt mail
        location.href = 'mailto:' + this.cryptEmail( email, true );
    }
}

// Creates a new instance of the Oop class
oop = new Oop();

// ]]>
