// <![CDATA[

// $Id$

/**
 * Manager for the OOP modules classes
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
function oopManager()
{
    // Checks the caller, as we don't want the class to be instanciated manually
    if( oopManager.caller !== oopManager.getInstance ) {
        
        // Error - Trying to instanciate the class manually
        throw new Error( 'Class oop is a singleton. Please use the getInstance() method to get a reference to the unique instance.' );
    }
    
    // Storage array for the instances of the module classes
    var _moduleClasses = new Array();
    
    /**
     * Registers a JavaScript class for a module
     * 
     * @param   string      The name of the module
     * @param   function    The module class
     * @return  void
     * @throws  Error       If the module is already registered
     */
    this.registerModuleClass = function( moduleName, moduleClass )
    {
        // Creates an instance of the module class, and stores it
        _moduleClasses[ moduleName ] = new moduleClass();
    }
    
    /**
     * Gets the instance of a module class
     * 
     * @param   string  The name of the module
     * @return  object  The instance of the requested module class
     * @throws  Error   If the requested module is not registered
     */
    this.getModule = function( moduleName )
    {
        // Checks if the instance of the module class exists
        if( _moduleClasses[ moduleName ] === undefined ) {
            
            // Error - The module is not registered
            throw new Error( 'Module \'' + moduleName + '\' has not been registered yet.' );
            
        } else {
            
            // Returns the instance of the module class
            return _moduleClasses[ moduleName ];
        }
    }
}

// Sets the instance of the oop class
oopManager._instance = null;

/**
 * Gets the unique instance of the oop class (singleton)
 * 
 * @return  object  The unique instance of the class
 */
oopManager.getInstance = function ()
{
    // Checks if the unique instance already exists
    if (this._instance === null ) {
        
        // Creates the unique instance
        this._instance = new oopManager();
    }
    
    // Returns the unique instance
    return this._instance;
}

// Registers the class for the 'oop' module
oopManager.getInstance().registerModuleClass(
    'oop',
    function()
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
            // Local variables
            var crypt     = '';
            var charValue = '';
            var charCode  = 0;
            var offset    = 0;
            
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
);

// ]]>
