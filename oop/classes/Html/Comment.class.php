<?php

/**
 * HTML writer class
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
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
     * 
     */
    protected $_comment = '';
    
    /**
     * 
     */
    public function __construct( $text )
    {
        $this->_comment = $text;
    }
    
    /**
     * 
     */
    protected function _output( $xmlCompliant = false, $level = 0 )
    {
        if( !$xmlCompliant ) {
            
            $indent = str_pad( '', $level, self::$_TAB );
            return self::$_NL . $indent . '<!-- ' . $this->_comment . ' -->' . self::$_NL;
        }
        
        return '';
    }
}
