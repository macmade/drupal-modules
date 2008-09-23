<?php

# $Id$

/**
 * HTML writer class
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Oop/Xhtml
 * @version         0.1
 */
class Oop_Xhtml_Tag implements ArrayAccess, Iterator
{
    /**
     * Wether the output is formatted or not
     */
    protected static $_formattedOutput = true;
    
    /**
     * Wether the static variables are set or not
     */
    protected static $_hasStatic       = false;
    
    /**
     * The list of the XHTML empty tags (as in the XHTML 1.0 Strict DTD)
     */
    protected static $_emptyTags      = array(
        'area'  => true,
        'base'  => true,
        'br'    => true,
        'col'   => true,
        'img'   => true,
        'input' => true,
        'hr'    => true,
        'link'  => true,
        'meta'  => true,
        'param' => true
    );
    
    /**
     * The new line character
     */
    protected static $_NL              = '';
    
    /**
     * The tabulation character
     */
    protected static $_TAB             = '';
    
    /**
     * The name of the current tag
     */
    protected $_tagName                = '';
    
    /**
     * The attributes of the current tag
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
    protected $_parents                = array();
    
    /**
     * The current position for the SPL Iterator methods
     */
    protected $_iteratorIndex          = 0;
    
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
            
            case 'comment':
                
                return $this->_addComment( $args[ 0 ] );
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
     * Moves the position to the first tag (SPL Iterator method)
     * 
     * @return  NULL
     */
    public function rewind()
    {
        $this->_iteratorIndex = 0;
    }
    
    /**
     * Returns the current tag (SPL Iterator method)
     * 
     * @return  Oop_Xhtml_Tag   The current HTML tag object
     */
    public function current()
    {
        return $this->_children[ $this->_iteratorIndex ];
    }
    
    /**
     * Gets the tag name for the current tag (SPL Iterator method)
     * 
     * @return  int     The name of the current tag
     */
    public function key()
    {
        return $this->_children[ $this->_iteratorIndex ]->_tagName;
    }
    
    /**
     * Moves the position to the next tag (SPL Iterator method)
     * 
     * @return  NULL
     */
    public function next()
    {
        $this->_iteratorIndex++;
    }
    
    /**
     * Checks for a current tag (SPL Iterator method)
     * 
     * @return  boolean
     */
    public function valid()
    {
        return isset( $this->_children[ $this->_iteratorIndex ] );
    }
    
    /**
     * Sets the needed static variables
     * 
     * @return  NULL
     */
    private static function _setStaticVars()
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
    public static function useFormattedOutput( $value )
    {
        $oldValue               = self::$_formattedOutput;
        self::$_formattedOutput = ( boolean )$value;
        
        return $oldValue;
    }
    
    /**
     * 
     */
    protected function _addSpacer( $pixels )
    {
        $spacer            = $this->_addChild( 'div' );
        $spacer[ 'class' ] = 'spacer';
        $spacer[ 'style' ] = 'margin-top: ' . $pixels . 'px';
        return $spacer;
    }
    
    /**
     * 
     */
    protected function _addComment( $text )
    {
        if( !isset( $this->_childrenByName[ '<!--' ] ) ) {
            
            $this->_childrenByName[ $name ]      = array();
            $this->_childrenCountByName[ $name ] = 0;
        }
        
        $comment             = new Oop_Xhtml_Comment( $text );
        $comment->_parents[] = $this;
        
        $this->_children[]                = $comment;
        $this->_childrenByName[ '<!--' ][] = $comment;
        
        $this->_childrenCountByName[ '<!--' ]++;
        $this->_childrenCount++;
        
        $this->_hasNodeChildren = true;
        
        return $comment;
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
        
        $child             = new self( $name );
        $child->_parents[] = $this;
        
        $this->_children[]                = $child;
        $this->_childrenByName[ $name ][] = $child;
        
        $this->_childrenCountByName[ $name ]++;
        $this->_childrenCount++;
        
        $this->_hasNodeChildren = true;
        
        return $child;
    }
    
    /**
     * Returns the output of the current tag
     * 
     * @param   boolean Wheter the output must be XML compliant
     * @param   int     The indentation level
     * @return  string  The output of the current tag (tag name and content)
     */
    protected function _output( $xmlCompliant = false, $level = 0 )
    {
        // Starts the tag
        $tag = '<' . $this->_tagName;
        
        // Process each registered attribute
        foreach( $this->_attribs as $key => &$value ) {
            
            // Adds the current attribute
            $tag .= ' ' . $key . '="' . $value . '"';
        }
        
        // Checks if we children to display
        if( !$this->_childrenCount ) {
            
            // No - Checks if the tag is self closed
            $tag .= ( isset( self::$_emptyTags[ $this->_tagName ] ) || $xmlCompliant ) ? ' />' : '></' . $this->_tagName . '>';
            
        } else {
            
            // Ends the start tag
            $tag .= '>';
            
            // Process each children
            foreach( $this->_children as $child ) {
                
                // Checks the current child is a tag or a string
                if( $child instanceof self ) {
                    
                    // Checks if we have to format the output
                    if( self::$_formattedOutput ) {
                        
                        // Adds the current child
                        $tag .= self::$_NL . str_pad( '', $level + 1, self::$_TAB );
                        $tag .= $child->_output( $xmlCompliant, $level + 1 );
                        
                    } else {
                        
                        // Adds the current child
                        $tag .= $child->_output( $xmlCompliant, $level + 1 );
                    }
                    
                } elseif( $xmlCompliant ) {
                    
                    // If we must be XML compliant, nodes and data are not allwed in a single node
                    if( $this->_hasNodeChildren ) {
                        
                        // Protect the data with CDATA, and adds a span tag for the XML compliancy
                        $tag .= '<span><![CDATA[' . $child . ']]></span>';
                        
                    } else {
                        
                        // Protects the data with CDATA
                        $tag .= '<![CDATA[' . $child . ']]>';
                    }
                    
                } else {
                    
                    // String - Adds the child data
                    $tag .= ( string )$child;
                }
            }
            
            // Checks if we have to format the output
            if( self::$_formattedOutput && $this->_hasNodeChildren ) {
                
                // Adds a new line and the current indentation
                $tag .= self::$_NL . str_pad( '', $level, self::$_TAB );
            }
            
            // Closes the tag
            $tag .= '</' . $this->_tagName . '>';
        }
        
        // Returns the tag
        return $tag;
    }
    
    /**
     * 
     */
    public function addChildNode( Oop_Xhtml_Tag $child )
    {
        if( !isset( self::$_emptyTags[ $this->_tagName ] ) ) {
            
            if( !isset( $this->_childrenByName[ $child->_tagName ] ) ) {
                
                $this->_childrenByName[ $child->_tagName ]      = array();
                $this->_childrenCountByName[ $child->_tagName ] = 0;
            }
            
            $child->_parents[] = $this;
            
            $this->_children[]                           = $child;
            $this->_childrenByName[ $child->_tagName ][] = $child;
            
            $this->_childrenCountByName[ $child->_tagName ]++;
            $this->_childrenCount++;
            
            $this->_hasNodeChildren = true;
            
            return $child;
        }
        
        return NULL;
    }
    
    /**
     * 
     */
    public function addTextData( $data )
    {
        if( !isset( self::$_emptyTags[ $this->_tagName ] ) ) {
            
            $this->_children[] = ( string )$data;
            $this->_childrenCount++;
        }
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
    
    /**
     * 
     */
    public function getParent( $parentIndex = 0 )
    {
        if( isset( $this->_parents[ $parentIndex ] ) ) {
            
            return $this->_parents[ $parentIndex ];
        }
        
        return $this->_parents[ 0 ];
    }
}
