<?php

chdir('..');

require_once 'setting.php';
require_once 'core/any/gf_get.php';
require_once 'core/any/gf_view.php';
require_once 'core/admin/gf_connector.php';
require_once 'core/admin/gf_controller.php';

(new GFoogController($user))->run();