<?php

$container_class = apply_filters('neve_container_class_filter', 'container', 'blog-archive');
$term = get_queried_object();
get_header();
?>
  <div class="<?php echo esc_attr($container_class); ?> archive-container">
    <div class="row">
        <?php do_action('neve_do_sidebar', 'blog-archive', 'left'); ?>
      <div class="nv-index-posts blog col">
          <?php
          do_action('neve_before_loop');
          do_action('neve_page_header', 'index');
          do_action('neve_before_posts_loop');
          
          $description = term_description();
          echo str_replace(
              'CC-BY-NC-SA',
              '<a href="https://creativecommons.org/licenses/by-nc-sa/4.0/">CC-BY-NC-SA</a>',
              $description
          );
          
          if (have_posts()) { ?>
            <section class="ceremonies">
                
                <?php while (have_posts()) {
                    the_post();
                    $post = get_post(get_the_ID());
                    $acfFields = get_fields($post->ID);
                    $thId = get_post_thumbnail_id();
                    $thUrl = get_the_post_thumbnail_url(null, 'medium');
                    $thAlt = get_post_meta($thId, '_wp_attachment_image_alt', true); ?>

                  <article class="ceremony aligncenter" aria-describedby="ceremony-title-<?= $post->ID ?>">
                    <header>
                      <a href="<?= $acfFields['ceremony'] ?>">
                        <h2 id="ceremony-title-<?= $post->ID ?>"><?php the_title() ?></h2>
                      </a>
                    </header>
                    <figure class="aligncenter size-medium">
                      <a href="<?= $acfFields['ceremony'] ?>">
                        <img class="wp-image-<?= $thId ?>" src="<?= $thUrl ?>" alt="<?= $thAlt ?>">
                      </a>
                      <figcaption class="aligncenter">
                          <?= $acfFields['ceremony_photo_caption'] ?>
                      </figcaption>
                    </figure>
                  </article>
                
                <?php } ?>
            </section>
          <?php } ?>
        <div class="w-100"></div>
          <?php do_action('neve_after_posts_loop'); ?>
      </div>
        <?php do_action('neve_do_sidebar', 'blog-archive', 'right'); ?>
    </div>
  </div>

<style>
    .ceremonies {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }
</style>

<?php
get_footer();
