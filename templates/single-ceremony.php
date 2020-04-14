<?php

$post = get_post(get_the_ID());
$acfFields = get_fields($post->ID);
$container_class = apply_filters('neve_container_class_filter', 'container', 'single-post');
get_header();
?>

  <div class="<?php echo esc_attr($container_class); ?> single-post-container">
    <div class="row">
      <article id="post-<?php echo esc_attr(get_the_ID()); ?>" class="<?php echo esc_attr(join(' ', get_post_class('nv-single-post-wrap col nv-content-wrap'))); ?>">
          <?php do_action('neve_before_post_content'); ?>

        <h1><?php the_title() ?></h1>
        <p><small>By: <?= $acfFields['ceremony_author']['display_name'] ?></small></p>
          
          <?php if (has_post_thumbnail()) {
              $thId = get_post_thumbnail_id();
              $thUrl = get_the_post_thumbnail_url(null, 'medium');
              $thAlt = get_post_meta($thId, '_wp_attachment_image_alt', true); ?>

            <figure class="alignright size-medium">
              <img class="wp-image-<?= $thId ?>" src="<?= $thUrl ?>" alt="<?= $thAlt ?>">
              <figcaption class="aligncenter"><?= $acfFields['ceremony_photo_caption'] ?></figcaption>
            </figure>
          <?php } ?>
          
          <?= apply_filters('the_content', $post->post_content) ?>

        <h2>Download Ceremony</h2>
        <p><a href="<?= $acfFields['ceremony'] ?>">Download <?= get_the_title() ?></a></p>
          
          <?php do_action('neve_after_post_content'); ?>
      </article>
    </div>
  </div>

<?php get_footer();
