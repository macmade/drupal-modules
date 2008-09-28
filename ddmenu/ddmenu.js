// <![CDATA[

// $Id$

// Registers the class for the 'ddmenu' module
oopManager.getInstance().registerModuleClass(
    'ddmenu',
    function()
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
                el.show( 'blind', {}, 1000 );
                
            } else {
                
                // Makes it disappear
                el.hide( 'blind', {}, 1000 );
            }
        }
    }
);

// ]]>
