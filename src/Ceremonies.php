<?php

namespace Dashifen\Ceremonies;

use Dashifen\WPHandler\Handlers\HandlerException;
use Dashifen\WPHandler\Handlers\Plugins\AbstractPluginHandler;

class Ceremonies extends AbstractPluginHandler
{
    /**
     * initialize
     *
     * Uses addAction() and addFilter() to connect WordPress to the methods
     * of this object's child which are intended to be protected.
     *
     * @return void
     * @throws HandlerException
     */
    public function initialize (): void
    {
        if (!$this->isInitialized()) {
            $this->registerActivationHook('registerPostType');
            $this->addAction('init', 'registerPostType');
        }
    }
    
    protected function registerPostType ()
    {
        $singular = 'Ceremony';
        $plural = 'Ceremonies';
        
        $labels = [
            'name'                  => $plural,
            'singular_name'         => $singular,
            'menu_name'             => $plural,
            'name_admin_bar'        => $singular,
            'archives'              => $singular . ' Archives',
            'attributes'            => $singular . ' Attributes',
            'parent_item_colon'     => 'Parent ' . $singular,
            'all_items'             => 'All ' . $plural,
            'add_new_item'          => 'Add New ' . $singular,
            'add_new'               => 'Add New',
            'new_item'              => 'New ' . $singular,
            'edit_item'             => 'Edit ' . $singular,
            'update_item'           => 'Update ' . $singular,
            'view_item'             => 'View ' . $singular,
            'view_items'            => 'View ' . $plural,
            'search_items'          => 'Search ' . $singular,
            'not_found'             => 'Not found',
            'not_found_in_trash'    => 'Not found in Trash',
            'featured_image'        => 'Featured Image',
            'set_featured_image'    => 'Set featured image',
            'remove_featured_image' => 'Remove featured image',
            'use_featured_image'    => 'Use as featured image',
            'insert_into_item'      => 'Add to ' . $singular,
            'uploaded_to_this_item' => 'Uploaded to this ' . $singular,
            'items_list'            => $plural . ' list',
            'items_list_navigation' => $plural . ' list navigation',
            'filter_items_list'     => 'Filter ' . strtolower($plural) . ' list',
        ];
        
        $args = [
            'labels'              => $labels,
            'label'               => $singular,
            'menu_icon'           => 'dashicons-groups',
            'description'         => 'A Memoriam Services ' . $singular,
            'supports'            => ['title', 'editor', 'thumbnail', 'revisions', 'page-attributes'],
            'taxonomies'          => ['category'],
            'capability_type'     => 'page',
            'hierarchical'        => false,
            'exclude_from_search' => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => true,
            'publicly_queryable'  => true,
            'show_in_rest'        => true,
            'menu_position'       => 5,
        ];
        
        register_post_type('post_type', $args);
        if (self::isDebug() || current_action() !== 'init') {
            
            // either if we're debugging or if this action isn't the init
            // action, we flush our rules.  the latter condition covers the
            // activation of the plugin since it's only then and the init
            // hook that we do this.
            
            flush_rewrite_rules();
        }
    }
}
