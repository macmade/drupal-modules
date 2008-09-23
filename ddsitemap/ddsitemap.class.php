<?php

# $Id$

/**
 * Drop-Down Site Map module for Drupal
 * 
 * Displays a drop-down site map for the primary, secondary or navigation links
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
class ddsitemap extends Oop_Drupal_ModuleBase implements Oop_Drupal_Node_Interface, Oop_Drupal_MenuItem_Interface
{
    /**
     * 
     */
    protected $_iconDir  = NULL;
    
    /**
     * 
     */
    protected $_iconPage = NULL;
    
    /**
     * The permissions array
     */
    protected $_perms    = array(
        'access ddsitemap admin',
        'create ddsitemap node',
        'edit own ddsitemap node',
        'edit ddsitemap node',
        'access ddsitemap node'
    );
    
    /**
     * 
     */
    protected $_path     = '';
    
    /**
     * 
     */
    protected function _getPages( array $pages, Oop_Xhtml_Tag $list, $type )
    {
        // Process each pages
        foreach( $pages as $id => $page ) {
            
            $li   = $list->li;
            $icon = $li->span;
            
            $li->addTextData( ' ' );
            $title = $li->span;
            $title->addChildNode( $this->_link( $page->getTitle(), array(), false, $page->getPath() ) );
            
            $subPages = array();
            
            if( $page->has_children ) {
                
                // Parameters for the PDO query
                $sqlParams = array(
                    ':menu_name' => $type,
                    ':plid'      => $page->mlid
                );
                
                // WHERE clause to select the pages
                $where = '{menu_links}.menu_name    = :menu_name
                            AND {menu_links}.plid   = :plid
                            AND {menu_links}.hidden = 0
                          ORDER BY {menu_links}.weight, {menu_links}.link_title';
                
                // Gets the pages
                $subPages = Oop_Drupal_Page_Getter::getPages( $where, $sqlParams, true );
            }
            
            if( is_array( $subPages ) && count( $subPages ) ) {
                
                $link            = $icon->a;
                $link[ 'href' ]  = 'javascript:oopManager.getInstance().getModule( \'ddsitemap\' ).display( \'ddsitemap-page-' . $page->mlid . '\' );';
                $link[ 'title' ] = $this->_lang->openClose;
                
                $link->addChildNode( $this->_iconDir );
                
                $subList            = $li->ul;
                
                $subList[ 'id' ]    = 'ddsitemap-page-' . $page->mlid;
                
                if( $page->getPath( false ) === $this->_path ) {
                    
                    $this->_cssClass( $title, 'active' );
                    
                    $parent = $li->getParent();
                    
                    unset( $parent[ 'style' ] );
                    
                    while( $parent = $parent->getParent()->getParent() ) {
                        
                        unset( $parent[ 'style' ] );
                    }
                    
                } else {
                    
                    $subList[ 'style' ] = 'display: none;';
                }
                
                $this->_getPages( $subPages, $subList, $type );
                
            } else {
                
                $icon->addChildNode( $this->_iconPage );
                
                if( $page->getPath( false ) === $this->_path ) {
                    
                    $this->_cssClass( $title, 'active' );
                    
                    $parent = $li->getParent();
                
                    unset( $parent[ 'style' ] );
                    
                    while( $parent = $parent->getParent()->getParent() ) {
                        
                        unset( $parent[ 'style' ] );
                    }
                }
            }
        }
    }
    
    /**
     * Gets the node view
     * 
     * @param   Oop_Xhtml_Tag   The placeholder for the module content
     * @param   stdClass        The node object
     * @param   boolean         Wheter a teaser must be generated instead of the full content
     * @param   boolean         Whether the node is being displayed as a standalone page
     * @return  NULL
     */
    public function getNode( Oop_Xhtml_Tag $content, stdClass $node, $teaser, $page )
    {
        if( isset( $this->_modVars[ 'css_file' ] ) && $this->_modVars[ 'css_file' ] ) {
            
            $this->_includeCss( $this->_modVars[ 'css_file' ] );
            
        } else {
            
            $this->_includeModuleCSS();
        }
        
        $linkType = ( isset( $this->_modVars[ 'linktype_' . $delta ] ) ) ? $this->_modVars[ 'linktype_' . $delta ] : 'primary';
        
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
        
        $this->_iconDir  = $this->_getIcon( 'folder.png' );
        $this->_iconPage = $this->_getIcon( 'page_white.png' );
        $this->_includeModuleScript();
        $list = $content->ul;
        
        // Parameters for the PDO query
        $sqlParams = array(
            ':menu_name' => $section,
            ':plid'      => 0
        );
        
        // WHERE clause to select the pages
        $where = '{menu_links}.menu_name    = :menu_name
                    AND {menu_links}.plid   = :plid
                    AND {menu_links}.hidden = 0
                  ORDER BY {menu_links}.weight, {menu_links}.link_title';
        
        // Gets the pages
        $pages = Oop_Drupal_Page_Getter::getPages( $where, $sqlParams );
        
        if( is_array( $pages ) && count( $pages ) ) {
            
            $this->_getPages( $pages, $list, $section );
        }
    }
    
    /**
     * Adds items to the Drupal menu
     * 
     * @param   array   An array in which to place the menu items, passed by reference. It may contains existing menu items, for instance if an administration settings form exists
     * @return  NULL
     */
     public function addMenuItems( array &$items )
     {}
}
