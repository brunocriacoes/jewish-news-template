<?php
get_header();
?>

<main id="conteudo" class="single-page">
    <?php while (have_posts()) : the_post(); ?>
        <?php
        $categories       = get_the_category();
        $primary_category = !empty($categories) ? $categories[0] : null;
        $category_link    = $primary_category ? get_category_link($primary_category->term_id) : home_url('/');
        $caption          = get_the_post_thumbnail_caption();
        $related_args     = array('post_type' => 'post', 'posts_per_page' => 3, 'post__not_in' => array(get_the_ID()), 'post_status' => 'publish', 'ignore_sticky_posts' => true);

        if (!empty($categories)) {
            $related_args['category__in'] = wp_list_pluck($categories, 'term_id');
        }

        $related_posts = new WP_Query($related_args);
        $share_url     = 'mailto:?subject=' . rawurlencode(get_the_title()) . '&body=' . rawurlencode(get_permalink());
        ?>

        <nav class="shell breadcrumb" aria-label="<?php esc_attr_e('Navegação estrutural', 'jewish-news-template'); ?>">
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'jewish-news-template'); ?></a><span aria-hidden="true">›</span>
            <?php if ($primary_category) : ?>
                <a href="<?php echo esc_url($category_link); ?>"><?php echo esc_html($primary_category->name); ?></a><span aria-hidden="true">›</span>
            <?php endif; ?>
            <span><?php echo esc_html(wp_trim_words(get_the_title(), 8, '…')); ?></span>
        </nav>

        <article <?php post_class('article'); ?>>
            <header class="article-header shell narrow">
                <span class="kicker"><?php echo esc_html(jnt_category()); ?></span>
                <h1><?php the_title(); ?></h1>
                <?php if (has_excerpt()) : ?><p class="standfirst"><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
                <div class="article-meta">
                    <span><?php printf(esc_html__('Por %1$s', 'jewish-news-template'), esc_html(get_the_author())); ?></span><span aria-hidden="true">•</span>
                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                    <a class="article-share" href="<?php echo esc_url($share_url); ?>"><?php esc_html_e('Partilhar', 'jewish-news-template'); ?> <span aria-hidden="true">↗</span></a>
                </div>
            </header>

            <?php if (has_post_thumbnail()) : ?>
                <figure class="lead-image shell">
                    <?php the_post_thumbnail('full', array('loading' => 'eager')); ?>
                    <?php if ($caption) : ?><figcaption><?php echo esc_html($caption); ?></figcaption><?php endif; ?>
                </figure>
            <?php endif; ?>

            <div class="article-layout shell narrow">
                <aside class="article-tools" aria-label="<?php esc_attr_e('Partilhar artigo', 'jewish-news-template'); ?>">
                    <span><?php esc_html_e('Partilhar', 'jewish-news-template'); ?></span>
                    <a href="<?php echo esc_url($share_url); ?>" aria-label="<?php esc_attr_e('Partilhar por e-mail', 'jewish-news-template'); ?>">✉</a>
                </aside>
                <div class="article-body entry-content">
                    <?php the_content(); ?>
                    <?php $tags = get_the_tags(); if ($tags) : ?>
                        <footer class="article-tags"><span><?php esc_html_e('Temas', 'jewish-news-template'); ?></span>
                            <?php foreach ($tags as $tag) : ?><a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>"><?php echo esc_html($tag->name); ?></a><?php endforeach; ?>
                        </footer>
                    <?php endif; ?>
                </div>
            </div>
        </article>

        <?php if ($related_posts->have_posts()) : ?>
            <section class="related shell" aria-labelledby="related-heading">
                <div class="section-heading"><h2 id="related-heading"><?php esc_html_e('Mais notícias', 'jewish-news-template'); ?></h2><a href="<?php echo esc_url($category_link); ?>"><?php esc_html_e('Ver todas', 'jewish-news-template'); ?> <span aria-hidden="true">→</span></a></div>
                <div class="related-grid">
                    <?php while ($related_posts->have_posts()) : $related_posts->the_post(); ?>
                        <article class="related-story">
                            <a class="related-story__image" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php jnt_story_image('medium_large', 'https://images.unsplash.com/photo-1519817650390-64a93db51149?auto=format&fit=crop&w=900&q=80'); ?></a>
                            <span class="kicker"><?php echo esc_html(jnt_category()); ?></span><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <p><?php echo esc_html(jnt_excerpt_chars(get_the_excerpt(), 130)); ?></p><time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                        </article>
                    <?php endwhile; ?>
                </div>
            </section>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
    <?php endwhile; ?>
</main>

<?php get_footer();

