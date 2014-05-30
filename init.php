<?php

defined('SYSPATH') or die('No direct script access.');

$config = Kohana::$config->load("structure");

Route::set('structure/upload', $config->routePath . '/upload(/<file>)', array('file' => '.+'))
        ->defaults(array(
            'controller' => 'structure',
            'action'     => 'upload',
            'file'       => NULL,
        ));

Route::set('structure/vendor', $config->routePath . '/vendor(/<file>)', array('file' => '.+'))
        ->defaults(array(
            'controller' => 'structure',
            'action'     => 'vendor',
            'file'       => NULL,
        ));

Route::set('structure/media', $config->routePath . '/media(/<file>)', array('file' => '.+'))
        ->defaults(array(
            'controller' => 'structure',
            'action'     => 'media',
            'file'       => NULL,
        ));

Route::set('structure', $config->routePath . '(/<action>(/<id>(/<id2>)))')
        ->defaults(array(
            'controller' => 'structure',
            'action'     => 'index',
        ));
