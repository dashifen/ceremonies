<?php

/**
 * Plugin Name: Ceremonies
 * Description: A WordPress custom post type for the ceremonies on https://memoriam.services.
 * Author URI: mailto:dashifen@dashifen.com
 * Author: David Dashifen Kees
 * Version: 1.0.0
 *
 * @noinspection PhpStatementHasEmptyBodyInspection
 * @noinspection PhpIncludeInspection
 */

if (file_exists($autoloader = dirname(ABSPATH) . '/deps/vendor/autoload.php'));
elseif ($autoloader = file_exists(dirname(ABSPATH) . '/vendor/autoload.php'));
elseif ($autoloader = file_exists(ABSPATH . 'vendor/autoload.php'));
else $autoloader = 'vendor/autoload.php';
require_once $autoloader;

(function() {
    try {
        $antiBruteSquad = new AntiBruteSquad();
        $antiBruteSquad->initialize();
    } catch (HandlerException $e) {
        wp_die($e->getMessage());
    }
})();