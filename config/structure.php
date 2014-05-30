<?php

defined('SYSPATH') or die('No direct script access.');

return array
    (
    'routePath' => "admin/structure",
    'bootstrap' => FALSE,
    'jquery'    => TRUE,
    "kohana"    => array(
        'auth' => array(
            "enabled" => TRUE,
            //login, admin, etc
            "roles"   => "admin, super",
        )
    )
);
