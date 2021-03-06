<?php

# $Id$

// Checks the PHP version
if( ( double )PHP_VERSION < 5 ) {
    
    // We are running PHP4
    print 'PHP version 5 is required to use this script (actual version is ' . PHP_VERSION . ')';
    exit();
}

// Checks for the SPL
if( !function_exists( 'spl_autoload_register' ) ) {
    
    // The SPL is unavailable
    throw new Exception( 'The SPL (Standard PHP Library) is required to use this script' );
}

// Checks for the SimpleXmlElement class
if( !class_exists( 'SimpleXmlElement' ) ) {
    
    // SimpleXml is unavailable
    throw new Exception( 'The SimpleXmlElement class is required to use this script' );
}

// Includes the class manager
require_once(
    dirname( __FILE__ )
  . DIRECTORY_SEPARATOR
  . 'classes'
  . DIRECTORY_SEPARATOR
  . 'Oop'
  . DIRECTORY_SEPARATOR
  . 'Core'
  . DIRECTORY_SEPARATOR
  . 'ClassManager.class.php'
);

// Registers an SPL autoload method to use to load the classes form this package
spl_autoload_register( array( 'Oop_Core_ClassManager', 'autoLoad' ) );
