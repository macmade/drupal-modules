<?php

$formConf = array(
    'number_of_blocks' => array(
        '#type'          => 'textfield',
        '#default_value' => '1',
        '#size'          => 5,
        '#maxlength'     => 5,
        '#required'      => true
    ),
    'css_file' => array(
        '#type'          => 'textfield',
        '#default_value' => '',
        '#size'          => 50
    )
);
