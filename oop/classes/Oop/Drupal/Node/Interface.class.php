<?php

# $Id$

/**
 * Interface for the Drupal node modules
 * 
 * @author          Jean-David Gadina <macmade@eosgarden.com>
 * @copyright       Copyright &copy; 2008
 * @package         Drupal
 * @version         0.1
 */
interface Oop_Drupal_Node_Interface
{
    public function getNode( Oop_Xhtml_Tag $content, stdClass $node, $teaser, $page );
}
