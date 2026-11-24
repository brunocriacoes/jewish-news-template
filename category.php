<?php
get_header();

$category = get_queried_object();
$has_posts = have_posts();
$is_text_only_category = is_category('articles');
$is_communities_category = is_category('communities');
$featured_community_post = null;

if ($is_communities_category) {
    $candidate = get_page_by_path('the-jewish-communities-of-portugal', OBJECT, ['post', 'page']);
    if ($candidate instanceof WP_Post) {
        $featured_community_post = $candidate;
    }
}
?>
<main id="conteudo" class="shell category-page">
    <?php if ($featured_community_post) : setup_postdata($featured_community_post); ?>
        <section class="category-lead" aria-label="Featured post">
            <a class="lead-image" href="<?php the_permalink(); ?>">
                <?php jnt_story_image('large', 'https://images.unsplash.com/photo-1519817650390-64a93db51149?auto=format&fit=crop&w=1200&q=85'); ?>
            </a>
            <article>
                <span class="kicker"><?php echo esc_html(jnt_category()); ?></span>
                <h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 26)); ?></p>
                <a class="gold-button" href="<?php the_permalink(); ?>">Read article <span>→</span></a>
                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(jnt_post_date()); ?></time>
            </article>
        </section>
        <?php wp_reset_postdata(); ?>
    <?php elseif ($has_posts) : the_post(); ?>
        <section class="category-lead<?php echo $is_text_only_category ? ' category-lead--text-only' : ''; ?>" aria-label="Postagem em destaque">
            <?php if (!$is_text_only_category) : ?>
                <a class="lead-image" href="<?php the_permalink(); ?>">
                    <?php jnt_story_image('large', 'https://images.unsplash.com/photo-1519817650390-64a93db51149?auto=format&fit=crop&w=1200&q=85'); ?>
                </a>
            <?php endif; ?>
            <article>
                <span class="kicker"><?php echo esc_html(jnt_category()); ?></span>
                <h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 26)); ?></p>
                <a class="gold-button" href="<?php the_permalink(); ?>">Read article <span>→</span></a>
                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(jnt_post_date()); ?></time>
            </article>
        </section>

    <?php endif; ?>

        <?php if (have_posts()) : ?>
            <section class="article-grid category-article-grid" aria-label="More posts in <?php echo esc_attr($category->name ?? 'category'); ?>">
                <?php while (have_posts()) : the_post(); ?>
                    <article>
                        <?php if (!$is_text_only_category) : ?>
                            <a class="category-card-image" href="<?php the_permalink(); ?>">
                                <?php jnt_story_image('medium_large', 'https://images.unsplash.com/photo-1495020689067-958852a7765e?auto=format&fit=crop&w=700&q=80'); ?>
                            </a>
                        <?php endif; ?>
                        <span class="kicker"><?php echo esc_html(jnt_category()); ?></span>
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 20)); ?></p>
                        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(jnt_post_date()); ?></time>
                    </article>
                <?php endwhile; ?>
            </section>
        <?php endif; ?>

        <?php
        $links = paginate_links([
            'type'      => 'array',
            'prev_text' => '← Previous',
            'next_text' => 'Next →',
        ]);
        ?>
            <nav class="pagination" aria-label="Pagination for <?php echo esc_attr($category->name ?? 'category'); ?>">
                <?php if ($links) : ?>
                    <?php foreach ($links as $link) { echo wp_kses_post($link); } ?>
                <?php else : ?>
                    <span class="page-numbers prev is-disabled" aria-disabled="true">← Previous</span>
                    <span class="page-numbers current" aria-current="page">1</span>
                    <span class="page-numbers next is-disabled" aria-disabled="true">Next →</span>
                <?php endif; ?>
            </nav>
    <?php if (!$featured_community_post && !$has_posts) : ?>
        <section class="category-empty">
            <span class="kicker"><?php echo esc_html($category->name ?? 'Category'); ?></span>
            <h1>There are no posts in this category.</h1>
        </section>
    <?php endif; ?>
</main>
<?php get_footer(); ?>

