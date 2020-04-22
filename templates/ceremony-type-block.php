<?php
/** @noinspection PhpUndefinedVariableInspection */

if (!empty($block['anchor'])) {
    $block['anchor'] = 'ceremony-type-block' . $block['id'];
}

$term = get_field('block_ceremony_type');
if (!($term instanceof WP_Term)) {
    echo 'Select the Ceremony Type to which you wish to display a link.';
} else {
    $link = get_term_link($term, 'ceremony_type');
    $term = array_merge((array) $term, get_fields($term)); ?>

    <section id="ceremony-term-<?= $term['term_id'] ?>" class="aligncenter" aria-labelledby="ceremony-term-title-<?= $term['term_id'] ?>">
        <header class="aligncenter" style="text-align: center">
            <a href="<?= $link ?>">
                <h2 id="ceremony-term-title-<?= $term['term_id'] ?>"><?= $term['name'] ?></h2>
            </a>
        </header>
        <figure class="aligncenter size-medium wp-block-image">
            <a href="<?= $link ?>">
                <img class="wp-image-<?= $term['ceremony_term_photo']['ID'] ?>"
                     src="<?= $term['ceremony_term_photo']['sizes']['medium'] ?>"
                     width="<?= $term['ceremony_term_photo']['sizes']['medium_width'] ?>"
                     height="<?= $term['ceremony_term_photo']['sizes']['medium_height'] ?>"
                     alt="<?= $term['ceremony_term_photo']['alt'] ?>">
            </a>

            <figcaption class="aligncenter">
                Photo credit:
                <a href="<?= $term['ceremony_term_photo_credit']['ceremony_term_photo_credit_photographer_link'] ?>">
                    <?= $term['ceremony_term_photo_credit']['ceremony_term_photo_credit_photographer'] ?>
                </a>
                
                <?php if ($term['ceremony_term_photo_credit']['ceremony_term_photo_credit_pixabay']) { ?>
                    (<a href="https://pixabay.com">Pixabay.com</a>)
                <?php } ?>
            </figcaption>
        </figure>
        <p class="aligncenter" style="text-align: center"><?= $term['description'] ?></p>
    </section>
<?php }
