// <![CDATA[

// $Id$

// Registers the class for the 'helloworld' module
oopManager.getInstance().registerModuleClass(
    'helloworld',
    function()
    {
        /**
         * The div that have been displayed
         */
        var _displayed = new Array();
        
        /**
         * Makes a div appear using jQuery
         * 
         * @param   id      The ID of the div to display
         * @return  void
         */
        this.display = function( id )
        {
            // Checks the display state
            if( _displayed[ id ] === undefined || _displayed[ id ] === false ) {
                
                // Makes it appear
                $( 'div#' + id ).show( 'blind', {}, 1000 );
                
                // Sets the display state
                _displayed[ id ] = true;
                
            } else {
                
                // Makes it disappear
                $( 'div#' + id ).hide( 'blind', {}, 1000 );
                
                // Sets the display state
                _displayed[ id ] = false;
            }
        }
    }
);

// ]]>
