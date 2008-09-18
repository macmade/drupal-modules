<?php

$formConf = array(
    'font_size' => array(
        '#type'          => 'textfield',
        '#default_value' => '10',
        '#size'          => 5,
        '#maxlength'     => 5,
        '#required'      => true
    ),
    'background' => array(
        '#type'          => 'textfield',
        '#default_value' => '#000000',
        '#size'          => 10,
        '#maxlength'     => 10,
        '#required'      => true
    ),
    'foreground' => array(
        '#type'          => 'textfield',
        '#default_value' => '#FFFFFF',
        '#size'          => 10,
        '#maxlength'     => 10,
        '#required'      => true
    ),
    'prompt' => array(
        '#type'          => 'textfield',
        '#default_value' => '#00FF00',
        '#size'          => 10,
        '#maxlength'     => 10,
        '#required'      => true
    ),
    'history' => array(
        '#type'          => 'checkbox',
        '#default_value' => '1'
    ),
    'exec_command' => array(
        '#type'          => 'select',
        '#options'       => 'proc_open,exec,shell_exec,system,passthru,popen',
        '#default_value' => 'proc_open'
    ),
    'disallow_commands' => array(
        '#type' => 'textfield',
        '#size' => 100
    ),
    'allow_commands' => array(
        '#type' => 'textfield',
        '#size' => 100
    )
);
