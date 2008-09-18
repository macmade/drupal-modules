// <![CDATA[

// $Id$

/**
 * JavaScript class for the Drupal 'ddmenu' module
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
function ddmenu()
{
    /**
     * Makes a element appear using jQuery
     * 
     * @param   id      The ID of the element to display
     * @return  void
     */
    this.display = function( id )
    {
        // Gets the element
        var el = $( 'ul#' + id );
        
        // Checks the display state
        if( el.is( ':hidden' ) ) {
            
            // Makes it appear
            el.slideDown();
            
        } else {
            
            // Makes it disappear
            el.slideUp();
        }
    }
}

// Creates a new instance of the module class
ddmenu = new ddmenu();

// ]]>
