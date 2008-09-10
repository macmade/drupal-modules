// <![CDATA[

/**
 * JavaScript class for the Drupal 'helloworld' module
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
function helloworld()
{
    /**
     * The div that have been displayed
     */
    var _displayed = new Array();
    
    /**
     * Makes a div appear using Scriptaculous
     * 
     * @param   id      The ID of the div to display
     * @return  void
     */
    this.display = function( id )
    {
        // Checks the display state
        if( _displayed[ id ] === undefined || _displayed[ id ] === false ) {
            
            // Makes it appear
            $( 'div#' + id ).slideDown();
            
            // Sets the display state
            _displayed[ id ] = true;
            
        } else {
            
            // Makes it disappear
            $( 'div#' + id ).slideUp( 'slow' );
            
            // Sets the display state
            _displayed[ id ] = false;
        }
    }
}

// Creates a new instance of the module class
helloworld = new helloworld();

// ]]>
