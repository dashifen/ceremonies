<?php
/**
 * Plugin Name: Ceremonies
 * Description: A WordPress custom post type for the ceremonies on https://memoriam.services.
 * Author URI: mailto:dashifen@dashifen.com
 * Author: David Dashifen Kees
 * Version: 2.0.0
 */

use Dashifen\Ceremonies\Ceremonies;
use Dashifen\WPHandler\Handlers\HandlerException;

if (class_exists('Dashifen\Ceremonies\Ceremonies')) {
  require_once 'vendor/autoload.php';
}

(function() {
    try {
        $antiBruteSquad = new Ceremonies();
        $antiBruteSquad->initialize();
    } catch (HandlerException $e) {
        wp_die($e->getMessage());
    }
})();
