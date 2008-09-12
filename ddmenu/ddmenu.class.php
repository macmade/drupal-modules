<?php

/**
 * Drop-Down Menu module for Drupal
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
class ddmenu extends Oop_Drupal_ModuleBase
{
    /**
     * Class version constants.
     * Holds the version, the developpment state
     * and the PHP lower compatible version.
     */
    const CLASS_VERSION  = '0.1';
    const DEVEL_STATE    = 'alpha';
    const PHP_COMPATIBLE = '5.2.0';
    
    protected $_iconDir  = NULL;
    protected $_iconPage = NULL;
    protected $_path     = '';
    protected $_pathInfo = array();
    
    /**
     * Gets the 'view' section of the module
     * 
     * @param   Oop_Xhtml_Tag   The placeholder for the module content
     * @param   int             The delta offset, used to generate different contents for different blocks
     * @return  NULL
     */
    protected function _getView( Oop_Xhtml_Tag $content, $delta )
    {
        // Includes the module CSS file
        $this->_includeModuleCSS();
        
        $linkType = variable_get( $this->_modName . '_linktype', 'primary' );
        
        switch( $linkType ) {
            
            case 'primary':
                
                $section = 'primary-links';
                break;
                
            case 'secondary':
                
                $section = 'secondary-links';
                break;
            
            default:
                
                $section = $linkType;
                break;
        }
        
        $this->_path = self::$_request->q; 
        $pathInfo    = explode( '/', $this->_path );
        $lastPath    = '';
        
        foreach( $pathInfo as $path ) {
            
            $lastPath                     = ( $lastPath ) ? $lastPath . '/' . $path : $path;
            $this->_pathInfo[ $lastPath ] = true;
        }
        
        $this->_iconDir  = $this->_getIcon( 'folder.png' );
        $this->_iconPage = $this->_getIcon( 'page_white.png' );
        $this->_includeModuleScript();
        $list = $content->ul;
        $this->_getPages( $list, $section, 0 );
    }
    
    /**
     * 
     */
    protected function _getPages( Oop_Xhtml_Tag $list, $type, $parent )
    {
        // Parameters for the PDO query
        $sqlParams = array(
            ':menu_name' => $type,
            ':plid'      => $parent
        );
        
        // SQL query
        $sql = 'SELECT *
                FROM {menu_links} as page, {menu_router} as router
                WHERE page.router_path = router.path
                    AND page.menu_name = :menu_name
                    AND page.plid = :plid
                    AND page.hidden = 0
                ORDER BY page.weight, page.link_title';
        
        // Prepares the PDO query
        $query       = self::$_db->prepare( $sql );
        
        // Executes the PDO query
        $query->execute( $sqlParams );
        
        // Process each pages
        while( $page = $query->fetchObject() ) {
            
            $pathInfo    = explode( '/', $page->path );
            $loadObjects = array();
            $access      = false;
            
            if( $page->to_arg_functions ) {
                
                $argFuncs = unserialize( $page->to_arg_functions );
                
                foreach( $argFuncs as $index => $funcName ) {
                    
                    $pathInfo[ $index ] = $funcName( $map[ $index ], $map, $index );
                }
                
                $page->link_path = implode( '/', $pathInfo );
            }
            
            if( $page->load_functions ) {
                
                $loadFuncs = unserialize( $page->load_functions );
                
                foreach( $loadFuncs as $index => $funcName ) {
                    
                    $loadObjects[ $index ] = $funcName( $pathInfo[ $index ] );
                }
            }
            
            if( is_numeric( $page->access_callback ) ) {
                
                $access = ( boolean )$page->access_callback;
                
            } elseif( $page->access_callback ) {
                
                $args = unserialize( $page->access_arguments );
                
                foreach( $args as $key => $value ) {
                    
                    if( isset( $loadObjects[ $value ] ) ) {
                        
                        $args[ $key ] = &$loadObjects[ $value ];
                    }
                }
                
                $access = ( boolean )call_user_func_array( $page->access_callback, $args );
                
            }
            
            if( $page->title_callback ) {
                
                $args      = array( $page->title );
                
                if( $page->title_arguments ) {
                    
                    $titleArgs = unserialize( $page->title_arguments );
                    
                    foreach( $titleArgs as $key => $value ) {
                        
                        if( isset( $loadObjects[ $value ] ) ) {
                            
                            $args[ $key ] = &$loadObjects[ $value ];
                        }
                    }
                }
                
                $page->title = call_user_func_array( $page->title_callback, $args );
            }
            
            if( $access === false ) {
                
                continue;
            }
            
            $li   = $list->li;
            $icon = $li->span;
            
            $li->addTextData( ' ' );
            $li->addChildNode( $this->_link( $page->link_title, array(), false, $page->link_path ) );
            
            if( $page->has_children ) {
                
                $link           = $icon->a;
                $link[ 'href' ] = 'javascript:ddmenu.display( \'ddmenu-page-' . $page->mlid . '\' );';
                
                $link->addChildNode( $this->_iconDir );
                
                $subList            = $li->ul;
                
                $subList[ 'id' ]    = 'ddmenu-page-' . $page->mlid;
                
                
                if( isset( $this->_pathInfo[ $page->link_path ] ) ) {
                    
                    $this->_cssClass( $li, 'open' );
                    
                } else {
                    
                    $subList[ 'style' ] = 'display: none;';
                }
                
                $this->_getPages( $subList, $type, $page->mlid );
                
            } else {
                
                $icon->addChildNode( $this->_iconPage );
                
                if( $page->link_path === $this->_path ) {
                    
                    $this->_cssClass( $li, 'active' );
                }
            }
        }
    }
}
