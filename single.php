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
        $featured_id      = get_post_thumbnail_id();
        $featured_url     = $featured_id ? wp_get_attachment_url($featured_id) : '';
        $post_content     = get_post_field('post_content', get_the_ID());
        $image_in_content = $featured_id && (false !== strpos($post_content, 'wp-image-' . $featured_id) || ($featured_url && false !== strpos($post_content, $featured_url)));
        $related_args     = array('post_type' => 'post', 'posts_per_page' => 3, 'post__not_in' => array(get_the_ID()), 'post_status' => 'publish', 'ignore_sticky_posts' => true);

        if (!empty($categories)) {
            $related_args['category__in'] = wp_list_pluck($categories, 'term_id');
        }

        $related_posts = new WP_Query($related_args);
        $permalink     = get_permalink();
        $share_title   = rawurlencode(get_the_title());
        $share_link    = rawurlencode($permalink);
        $share_url     = 'mailto:?subject=' . $share_title . '&body=' . $share_link;
        $facebook_url  = 'https://www.facebook.com/sharer/sharer.php?u=' . $share_link;
        $twitter_url   = 'https://twitter.com/intent/tweet?text=' . $share_title . '&url=' . $share_link;
        $linkedin_url  = 'https://www.linkedin.com/sharing/share-offsite/?url=' . $share_link;
        $whatsapp_url  = 'https://wa.me/?text=' . rawurlencode(get_the_title() . ' ' . $permalink);
        ?>

        <nav class="shell breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'jewish-news-template'); ?>">
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
                    <span><?php printf(esc_html__('By %1$s', 'jewish-news-template'), esc_html(get_the_author())); ?></span><span aria-hidden="true">•</span>
                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                    <a class="article-share" href="<?php echo esc_url($share_url); ?>"><?php esc_html_e('Share', 'jewish-news-template'); ?> <span aria-hidden="true">↗</span></a>
                </div>
            </header>

            <?php if (has_post_thumbnail() && !$image_in_content) : ?>
                <figure class="lead-image shell">
                    <?php the_post_thumbnail('full', array('loading' => 'eager')); ?>
                    <?php if ($caption) : ?><figcaption><?php echo esc_html($caption); ?></figcaption><?php endif; ?>
                </figure>
            <?php endif; ?>

            <div class="article-layout shell narrow">
                <aside class="article-tools" aria-label="<?php esc_attr_e('Share article', 'jewish-news-template'); ?>">
                    <span><?php esc_html_e('Share', 'jewish-news-template'); ?></span>
                    <div class="share-links">
                        <a class="share-link" href="<?php echo esc_url($facebook_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Share on Facebook', 'jewish-news-template'); ?>"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a>
                        <a class="share-link" href="<?php echo esc_url($twitter_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Share on X', 'jewish-news-template'); ?>"><i class="fa-brands fa-x-twitter" aria-hidden="true"></i></a>
                        <a class="share-link" href="<?php echo esc_url($share_url); ?>" aria-label="<?php esc_attr_e('Share by email', 'jewish-news-template'); ?>"><i class="fa-solid fa-envelope" aria-hidden="true"></i></a>
                        <a class="share-link" href="<?php echo esc_url($linkedin_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Share on LinkedIn', 'jewish-news-template'); ?>"><i class="fa-brands fa-linkedin-in" aria-hidden="true"></i></a>
                        <a class="share-link" href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Share on WhatsApp', 'jewish-news-template'); ?>"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></a>
                        <button class="share-copy" type="button" data-share-url="<?php echo esc_url($permalink); ?>" aria-label="<?php esc_attr_e('Copy article link', 'jewish-news-template'); ?>"><i class="fa-solid fa-link" aria-hidden="true"></i></button>
                    </div>
                </aside>
                <div class="article-body entry-content">
                    <?php the_content(); ?>
                    <footer class="article-end-actions">
                        <?php if (!empty($categories)) : ?>
                            <div class="article-categories" aria-label="<?php esc_attr_e('Categories', 'jewish-news-template'); ?>">
                                <span><i class="fa-solid fa-tag" aria-hidden="true"></i> <?php esc_html_e('Categories', 'jewish-news-template'); ?></span>
                                <div class="article-categories__links">
                                    <?php foreach ($categories as $category) : ?><a href="<?php echo esc_url(get_category_link($category->term_id)); ?>"><?php echo esc_html($category->name); ?></a><?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="article-end-share" aria-label="<?php esc_attr_e('Share article', 'jewish-news-template'); ?>">
                            <a class="share-link" href="<?php echo esc_url($facebook_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Share on Facebook', 'jewish-news-template'); ?>"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a>
                            <a class="share-link" href="<?php echo esc_url($twitter_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Share on X', 'jewish-news-template'); ?>"><i class="fa-brands fa-x-twitter" aria-hidden="true"></i></a>
                            <a class="share-link" href="<?php echo esc_url($share_url); ?>" aria-label="<?php esc_attr_e('Share by email', 'jewish-news-template'); ?>"><i class="fa-solid fa-envelope" aria-hidden="true"></i></a>
                            <a class="share-link" href="<?php echo esc_url($linkedin_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Share on LinkedIn', 'jewish-news-template'); ?>"><i class="fa-brands fa-linkedin-in" aria-hidden="true"></i></a>
                            <a class="share-link" href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Share on WhatsApp', 'jewish-news-template'); ?>"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></a>
                            <button class="share-copy" type="button" data-share-url="<?php echo esc_url($permalink); ?>" aria-label="<?php esc_attr_e('Copy article link', 'jewish-news-template'); ?>"><i class="fa-solid fa-link" aria-hidden="true"></i></button>
                        </div>
                    </footer>
                </div>
            </div>
        </article>

        <?php if ($related_posts->have_posts()) : ?>
            <section class="related shell" aria-labelledby="related-heading">
                <div class="section-heading"><h2 id="related-heading"><?php esc_html_e('More news', 'jewish-news-template'); ?></h2><a href="<?php echo esc_url($category_link); ?>"><?php esc_html_e('View all', 'jewish-news-template'); ?> <span aria-hidden="true">→</span></a></div>
                <div class="related-grid">
                    <?php while ($related_posts->have_posts()) : $related_posts->the_post(); ?>
                        <article class="related-story">
                            <a class="related-story__image" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php jnt_story_image('medium_large', 'https://images.unsplash.com/photo-1519817650390-64a93db51149?auto=format&fit=crop&w=900&q=80'); ?></a>
                            <span class="kicker"><?php echo esc_html(jnt_category()); ?></span><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        </article>
                    <?php endwhile; ?>
                </div>
            </section>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
    <?php endwhile; ?>
</main>

<?php get_footer();

