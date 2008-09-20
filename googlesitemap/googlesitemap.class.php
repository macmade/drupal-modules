<?php

# $Id$

/**
 * Google sitemap module for Drupal
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @version         0.1
 */
class googlesitemap extends Oop_Drupal_ModuleBase
{
    /**
     * Adds items to the Drupal menu
     * 
     * @param   array   An array in which to place the menu items (may have existing items, depending on the call context)
     * @return  array   The modified items array
     */
    public function addMenuItems( array $items = array() )
    {
        $items[ 'googlesitemap' ] = array(
            'title'            => $this->_lang->getLabel( 'menu_item_title', 'system' ),
            'description'      => $this->_lang->getLabel( 'menu_item_description', 'system' ),
            'page callback'    => 'googlesitemap_show',
            'access arguments' => array( '' ),
        );
        
        return $items;
    }
    
    /**
     * Shows a menu item
     * 
     * @param   Oop_Xhtml_Tag   The placeholder for the module content
     * @return  NULL
     */
    public function show( Oop_Xhtml_Tag $content )
    {
        // Gets the kind of link to display
        $linktype = variable_get( 'googlesitemap_linktype', 'both' );
        
        // Checks the link type
        switch( $linktype ) {
            
            // Displays primary links
            case 'primary':
                
                // WHERE clause to select the pages
                $where = '{menu_links}.menu_name  = \'primary-links\'
                          AND {menu_links}.hidden = 0
                          ORDER BY {menu_links}.weight, {menu_links}.link_title';
                break;
            
            // Displays secondary links
            case 'secondary':
                
                // WHERE clause to select the pages
                $where = '{menu_links}.menu_name  = \'secondary-links\'
                          AND {menu_links}.hidden = 0
                          ORDER BY {menu_links}.weight, {menu_links}.link_title';
                break;
            
            // Displays primary and secondary links
            default:
                
                // WHERE clause to select the pages
                $where = '( {menu_links}.menu_name   = \'primary-links\'
                          OR {menu_links}.menu_name  = \'secondary-links\' )
                          AND {menu_links}.hidden = 0
                          ORDER BY {menu_links}.weight, {menu_links}.link_title';
                break;
        }
        
        // Gets the pages
        $pages          = Oop_Drupal_Page_Getter::getPages( $where );
        
        // Storage
        $xml            = new Oop_Xhtml_Tag( 'urlset' );
        $xml[ 'xmlns' ] = 'http://www.sitemaps.org/schemas/sitemap/0.9';
        
        // Host
        $host           = ( isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] ) ? 'https://' . $_SERVER[ 'HTTP_HOST' ] . '/' : 'http://' . $_SERVER[ 'HTTP_HOST' ] . '/';
        
        // Process the pages
        foreach( $pages as $page ) {
            
            // Adds the page URL
            $url      = $xml->url;
            $url->loc = $host . $page->getPath();
        }
        
        // Adds the XML declaration
        print '<?xml version="1.0" encoding="utf-8"?' . '>';
        
        // Prints the XML content
        print $xml;
        
        // Adds the XML header
        header( 'Content-type: text/xml' );
        
        // Aborts the script
        exit();
    }
}
