<?php

namespace Dashifen\Ceremonies;

use WP_Term;
use WP_Query;
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
            $this->registerActivationHook('registrations');
            $this->addAction('init', 'registrations');
            $this->addAction('acf/init', 'registerCeremonyTypeBlock');
            $this->addAction('template_redirect', 'redirectOnSingleCeremony');
            $this->addFilter('template_include', 'includeCeremonyTemplates');
            $this->addFilter('pre_get_posts', 'reorderCeremonies');
        }
    }
    
    /**
     * registrations
     *
     * Registers our post type and taxonomy.
     *
     * @return void
     */
    protected function registrations (): void
    {
        $this->registerPostType();
        $this->registerTaxonomy();
        
        if (self::isDebug() || current_action() !== 'init') {
            
            // either if we're debugging or if this action isn't the init
            // action, we flush our rules.  the latter condition covers the
            // activation of the plugin since it's only then and the init
            // hook that we do this.
            
            flush_rewrite_rules();
        }
    }
    
    /**
     * registerPostType
     *
     * Registers the ceremony post type.
     *
     * @return void
     */
    private function registerPostType (): void
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
            'add_new'               => 'Add New ' . $singular,
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
            'supports'            => ['title', 'editor', 'thumbnail', 'revisions'],
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
        
        register_post_type('ceremony', $args);
    }
    
    /**
     * registerTaxonomy
     *
     * Registers the ceremony_type taxonomy.
     *
     * @return void
     */
    private function registerTaxonomy (): void
    {
        $singular = 'Ceremony Type';
        $plural = 'Ceremony Types';
        $labels = [
            'name'                       => $plural,
            'singular_name'              => $singular,
            'menu_name'                  => $plural,
            'all_items'                  => 'All ' . $plural,
            'parent_item'                => 'Parent ' . $singular,
            'parent_item_colon'          => 'Parent ' . $singular . ':',
            'new_item_name'              => 'New ' . $singular,
            'add_new_item'               => 'Add New ' . $singular,
            'edit_item'                  => 'Edit ' . $singular,
            'update_item'                => 'Update ' . $singular,
            'view_item'                  => 'View ' . $singular,
            'separate_items_with_commas' => 'Separate ' . strtolower($plural) . ' with commas',
            'add_or_remove_items'        => 'Add or remove ' . $plural,
            'choose_from_most_used'      => 'Choose from the most used',
            'popular_items'              => 'Popular ' . $plural,
            'search_items'               => 'Search ' . $plural,
            'not_found'                  => 'Not Found',
            'no_terms'                   => 'No ' . $plural,
            'items_list'                 => $plural . ' list',
            'items_list_navigation'      => $plural . ' list navigation',
        ];
        
        $args = [
            'labels'            => $labels,
            'show_tagcloud'     => false,
            'rewrite'           => [
                'slug' => 'ceremonies'
            ],
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_in_rest'      => true,
        ];
        
        register_taxonomy('ceremony_type', ['ceremony'], $args);
    }
    
    /**
     * registerCeremonyTypeBlock
     *
     * Registers a block for ceremony types.
     *
     * @return void
     */
    protected function registerCeremonyTypeBlock (): void
    {
        acf_register_block_type([
            'title'           => 'Ceremony Type',
            'name'            => 'ceremony_type_block',
            'description'     => 'Displays a link to a ceremony type archive.',
            'category'        => 'common',
            'icon'            => '<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="tag" class="svg-inline--fa fa-tag fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M497.941 225.941L286.059 14.059A48 48 0 0 0 252.118 0H48C21.49 0 0 21.49 0 48v204.118a47.998 47.998 0 0 0 14.059 33.941l211.882 211.882c18.745 18.745 49.137 18.746 67.882 0l204.118-204.118c18.745-18.745 18.745-49.137 0-67.882zM259.886 463.996L48 252.118V48h204.118L464 259.882 259.886 463.996zM192 144c0 26.51-21.49 48-48 48s-48-21.49-48-48 21.49-48 48-48 48 21.49 48 48z"></path></svg>',
            'render_template' => untrailingslashit($this->getPluginDir()) . '/templates/ceremony-type-block.php'
        ]);
    }
    
    /**
     * redirectOnSingleCeremony
     *
     * If this is a ceremony type taxonomy page but there's only one ceremony
     * within that type, then we redirect to that ceremony's URL instead of
     * showing the tax page with one link on it.
     *
     * @return void
     * @noinspection SqlNoDataSourceInspection
     * @noinspection SqlCheckUsingColumns
     * @noinspection SqlResolve
     */
    protected function redirectOnSingleCeremony (): void
    {
        global $wpdb;
        if (is_tax('ceremony_type')) {
            /** @var WP_Term $term */
            
            // if this is the taxonomy archive for a ceremony_type term,
            // the get_queried_object will return a WP_Term object.  with
            // that we can see how many ceremonies are linked to this term.
            // if that count is one, then we redirect to that single ceremony
            // instead of staying here.
            
            $term = get_queried_object();
            $sql = <<< SQL
                SELECT ID
                FROM $wpdb->posts
                WHERE ID IN (
                    SELECT object_id
                    FROM $wpdb->term_relationships
                    INNER JOIN $wpdb->term_taxonomy  USING (term_taxonomy_id)
                    WHERE taxonomy = 'ceremony_type'
                    AND term_taxonomy_id = $term->term_taxonomy_id
                )
SQL;
            
            $statement = $wpdb->prepare($sql, );
            $posts = $wpdb->get_col($statement);
            
            // now, if the size of our selected posts is exactly one, we'll
            // get it's permalink and try to redirect to it.  as long as we
            // can do so, we halt the execution of this request and do the
            // redirection.  we redirect with a 303 See Other status which
            // is a temporary redirection that is never cached according to
            // the spec.
            
            if (sizeof($posts) === 1) {
                $permalink = get_permalink($posts[0]);
                $success = wp_safe_redirect($permalink, 303);
                if ($success) {
                    die;
                }
            }
        }
    }
    
    /**
     * includeCeremonyTemplates
     *
     * Identifies single ceremonies and ceremony_type archives and includes
     * our custom templates for them.  These templates are based on the Neve
     * theme which we use on the Memoriam Services site.
     *
     * @param string $template
     *
     * @return string
     */
    protected function includeCeremonyTemplates (string $template): string
    {
        $pluginDir = untrailingslashit($this->getPluginDir()) . '/templates';
        
        if (is_tax('ceremony_type')) {
            $template = $pluginDir . '/taxonomy-ceremony_type.php';
        }
        
        if (is_singular('ceremony')) {
            $template = $pluginDir . '/single-ceremony.php';
        }
        
        return $template;
    }
    
    protected function reorderCeremonies (WP_Query $query): WP_Query
    {
        if ($query->is_tax('ceremony_type')) {
            $query->set('orderby', 'title');
            $query->set('order', 'ASC');
        }
        
        return $query;
    }
}
