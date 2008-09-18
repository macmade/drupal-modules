<?php

$formConf = array(
    'html_format' => array(
        '#type'          => 'checkbox',
        '#default_value' => 1
    ),
    'email_crypt_symbol' => array(
        '#type'          => 'textfield',
        '#default_value' => '(at)',
        '#size'          => 5,
        '#maxlength'     => 5,
        '#required'      => true
    ),
    'default_language' => array(
        '#type'          => 'textfield',
        '#default_value' => 'en',
        '#size'          => 5,
        '#maxlength'     => 5,
        '#required'      => true
    )
);
