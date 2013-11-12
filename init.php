<?php

defined('SYSPATH') or die('No direct script access.');

Route::set('structure/media', 'structure/media(/<file>)', array('file' => '.+'))
        ->defaults(array(
            'controller' => 'structure',
            'action'     => 'media',
            'file'       => NULL,
        ));

Route::set('structure', 'structure(/<action>(/<id>))', array('file' => '.+'))
        ->defaults(array(
            'controller' => 'structure',
            'action'     => 'index',
            'file'       => NULL,
//            'directory'  => 'admin/structure',
        ));
