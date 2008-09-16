// <![CDATA[

/**
 * JavaScript class for the Drupal 'shell' module
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
function shell() {
    
    // Form HTML elements
    var _form                = null;
    var _result              = null;
    var _command             = null;
    var _history             = new Array();
    var _cwd                 = null;
    
    // Terminal prompt
    var _prompt              = '';
    
    // Number of commands to keep in the history
    var _historyLength       = 50;
    
    // Current history command
    var _currentHistoryIndex = -1;
    
    // URL for Ajax requests
    var _ajaxUrl             = '';
    
    // Calls the constructor
    __construct.apply( this, arguments );
    
    /**
     * Class constructor
     */
    function __construct()
    {
        // Get HTML elements
        _form    = document.getElementById( 'module-shell-form' );
        _result  = document.getElementById( 'module-shell-result' );
        _command = document.getElementById( 'module-shell-command' );
        _cwd     = document.getElementById( 'module-shell-cwd' );
        _cwdPath = document.getElementById( 'module-shell-cwdPath' );
        
        // Form submission method
        _form.onsubmit   = _exec;
        
        // Input keypress method
        _command.onkeyup = _keyUp;
    }
    
    /**
     * Executes a shell command
     * 
     * @param   string      An optionnal shell command. If it's not defined, the value form the text input will be taken.
     * @return  boolean     True if an argument is passed, otherwise false to prevent the form to be submitted.
     * @see     _addHistory
     */
    function _exec()
    {
        
        // Command to run (from arguments or text input)
        if( typeof arguments[ 0 ] == 'string' ) {
            
            // Gets the command to run from the arguments
            var command       = arguments[ 0 ];
            var formSubmitted = 0;
        
        } else {
            
            // Gets the command to run from the text input
            var command       = _command.value;
            var formSubmitted = 1;
        }
        
        // Resets the text input
        _command.value  = '';
        
        // Creates the command line element
        var commandLine = document.createElement( 'div' );
        
        // Creates the command prompt element
        var promptSpan       = document.createElement( 'span' );
        promptSpan.className = 'prompt';
        promptSpan.appendChild( document.createTextNode( _prompt ) );
        
        // Creates the command element
        var commandSpan       = document.createElement( 'span' );
        commandSpan.className = 'command';
        commandSpan.appendChild( document.createTextNode( ' ' + command ) );
        
        // Appends the prompt and the command
        commandLine.appendChild( promptSpan );
        commandLine.appendChild( commandSpan );
        
        // Adds the command line to result
        _result.appendChild( commandLine );
        
        // Adds the command to the history
        _addHistory( command );
        
        // Creates a new Ajax request
        $.ajax(
            {
                // URL to use
                url        : _ajaxUrl,
                
                // Method to use
                type       : 'GET',
                
                // Ajax parameters
                data       : {
                    'shell[ajaxCall]' : 1,
                    'shell[command]'  : command
                },
                
                // Function to call in case of success
                success    : function( data, textStatus )
                {
                    // Gets each line of the response
                    var cwdParts     = data.split( '\r\n' );
                    
                    // Current directory is the last line
                    var cwd          = cwdParts[ cwdParts.length - 1 ];
                    
                    // Removes the last line from the response
                    data             = data.replace( /\r\n.+$/i, '' );
                    
                    // Creates the result element
                    var result       = document.createElement( 'div' );
                    result.className = 'module-shell-result';
                    result.appendChild( document.createTextNode( data ) );
                    
                    // Appends the result to the console
                    _result.appendChild( result );
                    
                    // Scrolls to the bottom
                    _result.scrollTop = _result.scrollHeight;
                    
                    // Removes the old working directory
                    _cwd.removeChild( _cwdPath );
                    
                    // Creates the element for the new working directory
                    _cwdPath    = document.createElement( 'path' );
                    _cwdPath.id = 'module-shell-cwdPath';
                    _cwdPath.appendChild( document.createTextNode( cwd ) );
                    
                    // Appends the working directory
                    _cwd.appendChild( _cwdPath );
                }
            }
        );
        
        // Return false if the command has been taken from the text input
        // This will prevent the page to relaod
        return ( formSubmitted ) ? false : true;
    }
    
    /**
     * Adds a command to the history select
     * 
     * @param   string      command     The shell command
     * @return  boolean
     */
    function _addHistory( command )
    {
        // Inserts the new command in the history
        _history.push( command );
        
        // Checks the length of the history menu
        if( _history.length > _historyLength ) {
            
            // Removes the first item from the history
            _history.shift();
        }
        
        // Increase the directory index
        _currentHistoryIndex++;
        
        return true;
    }
    
    /**
     * Sets the command prompt
     * 
     * @param   string      prompt      The shell command
     * @return  boolean
     */
    function _setPrompt( prompt )
    {
        _prompt = prompt;
        
        return true;
    }
    
    /**
     * Sets the URL for the Ajax requests
     * 
     * @param   string      url         The shell command
     * @return  boolean
     */
    function _setAjaxUrl( url )
    {
        _ajaxUrl = url;
        
        return true;
    }
    
    /**
     * Process keyboard events for the command input
     * 
     * @param   object      event       The event object
     * @return  boolean
     */
    function _keyUp( event )
    {
        // Normalize event object
        var event = ( !event ) ? window.event : event;
        
        // History direction
        var direction = ( event.keyCode == 38 ) ? -1 : ( ( event.keyCode == 40 ) ? 1 : false );
        
        if( direction ) {
            
            // New history index
            var newIndex = _currentHistoryIndex + 1 + direction;
            
            // Checks for the boundaries
            if( newIndex == -1 ) {
                
                // Stays at the beginning of the history
                newIndex = 0;
                
            } else if( newIndex == _history.length + 1 ) {
                
                // Stays at the end of the history
                newIndex = _history.length;
            }
            
            // Writes the command and stores the new index
            _command.value       = ( _history[ newIndex ] !== undefined ) ? _history[ newIndex ] : '';
            _currentHistoryIndex = newIndex - 1;
            
            return true;
        }
        
        return false;
    }
    
    // Public methods
    this.exec       = _exec;
    this.keyUp      = _keyUp;
    this.setPrompt  = _setPrompt;
    this.setAjaxUrl = _setAjaxUrl;
}

// Creates a new instance of the module class
shell = new shell();

// ]]>
