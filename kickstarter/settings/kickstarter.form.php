<?php

# $Id$

$formConf = array(
    'infos' => array(
        '#type'        => 'fieldset',
        '#collapsible' => true,
        '#collapsed'   => false,
        'infos_name' => array(
            '#type'     => 'textfield',
            '#size'     => 30,
            '#required' => true
        ),
        'infos_title' => array(
            '#type'     => 'textfield',
            '#size'     => 30,
            '#required' => true
        ),
        'infos_description' => array(
            '#type'     => 'textfield',
            '#size'     => 100,
            '#required' => true
        ),
        'infos_description' => array(
            '#type'     => 'textfield',
            '#size'     => 100,
            '#required' => true
        ),
        'infos_package' => array(
            '#type'     => 'textfield',
            '#size'     => 30,
            '#required' => false
        ),
        'infos_cvs'     => array(
            '#type'          => 'checkbox',
            '#default_value' => true
        )
    ),
    'author' => array(
        '#type'        => 'fieldset',
        '#collapsible' => true,
        '#collapsed'   => false,
        'author_name' => array(
            '#type'     => 'textfield',
            '#size'     => 30,
            '#required' => true
        ),
        'author_email' => array(
            '#type'     => 'textfield',
            '#size'     => 30,
            '#required' => true
        )
    ),
    'dependencies' => array(
        '#type'        => 'fieldset',
        '#collapsible' => true,
        '#collapsed'   => true,
        'dependencies_version_core' => array(
            '#type'          => 'textfield',
            '#size'          => 10,
            '#default_value' => ( int )VERSION . '.x',
            '#required'      => true
        ),
        'dependencies_version_php' => array(
            '#type'          => 'textfield',
            '#size'          => 10,
            '#default_value' => ( double )PHP_VERSION,
            '#required'      => true
        ),
        'dependencies_dependencies' => array(
            '#type'          => 'textfield',
            '#size'          => 100,
            '#required'      => false
        )
    ),
    'block' => array(
        '#type'        => 'fieldset',
        '#collapsible' => true,
        '#collapsed'   => true,
        'block_add' => array(
            '#type' => 'checkbox'
        ),
        'block_title' => array(
            '#type' => 'textfield',
            '#size' => 30
        ),
        'block_description' => array(
            '#type' => 'textfield',
            '#size' => 100
        ),
        'block_add_config' => array(
            '#type' => 'checkbox'
        ),
        'block_access' => array(
            '#type' => 'textfield',
            '#size' => 100
        )
    ),
    'node' => array(
        '#type'        => 'fieldset',
        '#collapsible' => true,
        '#collapsed'   => true,
        'node_add' => array(
            '#type' => 'checkbox'
        ),
        'node_title' => array(
            '#type' => 'textfield',
            '#size' => 30
        ),
        'node_description' => array(
            '#type' => 'textfield',
            '#size' => 100
        ),
        'node_access' => array(
            '#type' => 'textfield',
            '#size' => 100
        )
    ),
    'admin' => array(
        '#type'        => 'fieldset',
        '#collapsible' => true,
        '#collapsed'   => true,
        'admin_add' => array(
            '#type' => 'checkbox'
        ),
        'admin_title' => array(
            '#type' => 'textfield',
            '#size' => 30
        ),
        'admin_description' => array(
            '#type' => 'textfield',
            '#size' => 100
        ),
        'admin_blocks_number' => array(
            '#type' => 'checkbox'
        )
    ),
    'menu' => array(
        '#type'        => 'fieldset',
        '#collapsible' => true,
        '#collapsed'   => true,
        'menu_add' => array(
            '#type' => 'checkbox'
        ),
        'menu_path' => array(
            '#type' => 'textfield',
            '#size' => 30
        ),
        'menu_title' => array(
            '#type' => 'textfield',
            '#size' => 30
        ),
        'menu_description' => array(
            '#type' => 'textfield',
            '#size' => 100
        ),
        'menu_access' => array(
            '#type'          => 'textfield',
            '#size'          => 100,
            '#default_value' => 'access administration pages'
        )
    ),
    'misc' => array(
        '#type'        => 'fieldset',
        '#collapsible' => true,
        '#collapsed'   => true,
        'misc_css' => array(
            '#type' => 'checkbox'
        ),
        'misc_js' => array(
            '#type' => 'checkbox',
        )
    ),
    'submit' => array(
        '#type' => 'submit'
    )
);
