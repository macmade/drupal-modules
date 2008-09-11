// <![CDATA[

/**
 * JavaScript class for the Drupal 'ddsm' module
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
function ddsm()
{
    /**
     * The div that have been displayed
     */
    var _displayed = new Array();
    
    /**
     * Makes a list appear using jQuery
     * 
     * @param   id      The ID of the div to display
     * @return  void
     */
    this.display = function( id )
    {
        // Checks the display state
        if( _displayed[ id ] === undefined || _displayed[ id ] === false ) {
            
            // Makes it appear
            $( 'ul#' + id ).slideDown();
            
            // Sets the display state
            _displayed[ id ] = true;
            
        } else {
            
            // Makes it disappear
            $( 'ul#' + id ).slideUp();
            
            // Sets the display state
            _displayed[ id ] = false;
        }
    }
}

// Creates a new instance of the module class
ddsm = new ddsm();

// ]]>
