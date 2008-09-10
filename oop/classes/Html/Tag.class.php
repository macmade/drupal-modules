<?php

/**
 * HTML writer class
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
class Html_Tag implements ArrayAccess
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
    protected static $_formattedOutput = true;
    
    /**
     * Wether the static variables are set or not
     */
    protected static $_hasStatic       = false;
    
    /**
     * 
     */
    protected static $_NL              = '';
    
    /**
     * 
     */
    protected static $_TAB             = '';
    
    /**
     * 
     */
    protected $_tagName                = '';
    
    /**
     * 
     */
    protected $_attribs                = array();
    
    /**
     * 
     */
    protected $_children               = array();
    
    /**
     * 
     */
    protected $_childrenByName         = array();
    
    /**
     * 
     */
    protected $_childrenCountByName    = array();
    
    /**
     * 
     */
    protected $_childrenCount          = 0;
    
    /**
     * 
     */
    protected $_hasNodeChildren        = false;
    
    /**
     * 
     */
    protected $_parent                 = NULL;
    
    /**
     * 
     */
    public function __construct( $tagName )
    {
        // Checks if the static variables are set
        if( !self::$_hasStatic ) {
            
            // Sets the static variables
            self::_setStaticVars();
        }
        
        // Sets the tag name
        $this->_tagName = ( string )$tagName;
    }
    
    /**
     * 
     */
    public function __toString()
    {
        return $this->asHtml();
    }
    
    /**
     * 
     */
    public function __set( $name, $value )
    {
        $this->_addChild( $name )->addTextData( $value );
    }
    
    /**
     * 
     */
    public function __get( $name )
    {
        return $this->_addChild( $name );
    }
    
    /**
     * 
     */
    public function __call( $name, array $args = array() )
    {
        switch( $name ) {
            
            case 'spacer':
                
                return $this->_addSpacer( $args[ 0 ] );
                break;
        }
    }
    
    /**
     * 
     */
    public function offsetExists( $offset )
    {
        return isset( $this->_attribs[ $offset ] );
    }
    
    /**
     * 
     */
    public function offsetGet( $offset )
    {
        return $this->_attribs[ $offset ];
    }
    
    /**
     * 
     */
    public function offsetSet( $offset, $value )
    {
        $this->_attribs[ $offset ] = ( string )$value;
    }
    
    /**
     * 
     */
    public function offsetUnset( $offset )
    {
        unset( $this->_attribs[ $offset ] );
    }
    
    /**
     * Sets the needed static variables
     * 
     * @return  NULL
     */
    protected static function _setStaticVars()
    {
        // Sets the new line character
        self::$_NL        = chr( 10 );
        
        // Sets the tabulation character
        self::$_TAB       = chr( 9 );
        
        // Static variables are set
        self::$_hasStatic = true;
    }
    
    /**
     * 
     */
    protected function _addSpacer( $pixels )
    {
        $spacer            = $this->div;
        $spacer[ 'class' ] = 'spacer';
        $spacer[ 'style' ] = 'margin-top: ' . $pixels . 'px';
        return $spacer;
    }
    
    /**
     * 
     */
    protected function _addChild( $name )
    {
        if( !isset( $this->_childrenByName[ $name ] ) ) {
            
            $this->_childrenByName[ $name ]      = array();
            $this->_childrenCountByName[ $name ] = 0;
        }
        
        $child          = new self( $name );
        $child->_parent = $this;
        
        $this->_children[]                = $child;
        $this->_childrenByName[ $name ][] = $child;
        
        $this->_childrenCountByName[ $name ]++;
        $this->_childrenCount++;
        
        $this->_hasNodeChildren = true;
        
        return $child;
    }
    
    protected function _output( $xmlCompliant = false, $level = 0 )
    {
        $tag = '<' . $this->_tagName;
        
        foreach( $this->_attribs as $key => &$value ) {
            
            $tag .= ' ' . $key . '="' . $value . '"';
        }
        
        if( !$this->_childrenCount ) {
            
            $tag .= ' />';
            
        } else {
            
            $tag .= '>';
            
            foreach( $this->_children as $child ) {
                
                if( $child instanceof self ) {
                    
                    if( self::$_formattedOutput ) {
                        
                        $tag .= self::$_NL . str_pad( '', $level + 1, self::$_TAB );
                        $tag .= $child->_output( $xmlCompliant, $level + 1 );
                        
                    } else {
                        
                        $tag .= $child->_output( $xmlCompliant, $level + 1 );
                    }
                    
                } elseif( $xmlCompliant ) {
                    
                    if( $this->_hasNodeChildren ) {
                        
                        $tag .= '<span><![CDATA[' . $child . ']]></span>';
                        
                    } else {
                        
                        $tag .= '<![CDATA[' . $child . ']]>';
                    }
                    
                } else {
                    
                    $tag .= ( string )$child;
                }
            }
            
            if( self::$_formattedOutput && $this->_hasNodeChildren ) {
                
                $tag .= self::$_NL . str_pad( '', $level, self::$_TAB );
            }
            
            $tag .= '</' . $this->_tagName . '>';
        }
        
        return $tag;
    }
    
    /**
     * 
     */
    public function addTextData( $data )
    {
        $this->_children[] = ( string )$data;
        $this->_childrenCount++;
    }
    
    /**
     * 
     */
    public function asHtml()
    {
        return $this->_output( false );
    }
    
    /**
     * 
     */
    public function asXml()
    {
        return $this->_output( true );
    }
    
    public function useFormattedOutput( $value )
    {
        $oldValue               = self::$_formattedOutput;
        self::$_formattedOutput = ( boolean )$value;
        
        return $oldValue;
    }
}
