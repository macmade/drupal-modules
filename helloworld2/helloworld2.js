// <![CDATA[

/**
 * JavaScript class for the Drupal 'helloworld2' module
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
function helloworld2()
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
            $( 'div#' + id ).slideUp();
            
            // Sets the display state
            _displayed[ id ] = false;
        }
    }
}

// Creates a new instance of the module class
helloworld2 = new helloworld2();

// ]]>
