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
     * Makes a div appear using Scriptaculous
     * 
     * @param   id      The ID of the div to display
     * @return  void
     */
    this.display = function( id )
    {
        // Gets the requested div
        var infos = $( id );
        
        // Makes it appear
        Effect.BlindDown( infos );
    }
}

// Creates a new instance of the module class
helloworld = new helloworld();

// ]]>
