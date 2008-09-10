<?php

/**
 * Class to create HTML comments
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Html
 * @version         0.1
 */
class Html_Comment extends Html_Tag implements ArrayAccess
{
    /**
     * Class version constants.
     * Holds the version, the developpment state
     * and the PHP lower compatible version.
     */
    const CLASS_VERSION  = '0.1';
    const DEVEL_STATE    = 'alpha';
    const PHP_COMPATIBLE = '5.2.0';
    
    /**
     * The text of the comment
     */
    protected $_comment = '';
    
    /**
     * Class constructor
     * 
     * @return NULL
     * @see     Html_Tag::__construct
     */
    public function __construct( $text )
    {
        // Sets the comment text
        $this->_comment = $text;
        
        // Calls the parent constructor
        parent::__construct( '' );
    }
    
    /**
     * Returns the HTML comment
     * 
     * @param   boolean Wheter the output must be XML compliant
     * @param   int     The indentation level
     * @return  string  The HTML comment, if $xmlCompliant is false, otherwise a blank string
     */
    protected function _output( $xmlCompliant = false, $level = 0 )
    {
        // Checks if the output must be XML compliant
        if( !$xmlCompliant ) {
            
            // Returns the HTML comment
            $indent = str_pad( '', $level, self::$_TAB );
            return self::$_NL . $indent . '<!-- ' . $this->_comment . ' -->' . self::$_NL . $indent;
        }
        
        // Do not return the HTML comment when the output must be XML compliant
        return '';
    }
}
