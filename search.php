<?php get_header(); ?>
<main id="conteudo" class="shell search-page">
    <header class="search-page__heading">
        <span class="kicker">SEARCH</span>
        <h1>Results for: <?php echo esc_html(get_search_query()); ?></h1>
    </header>

    <?php if (have_posts()) : ?>
        <section class="jnt-search-results" aria-label="Search results">
            <?php while (have_posts()) : the_post(); ?>
                <article class="search-result">
                    <span class="kicker"><?php echo esc_html(jnt_category()); ?></span>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 28)); ?></p>
                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(jnt_post_date()); ?></time>
                </article>
            <?php endwhile; ?>
        </section>
        <?php
        $links = paginate_links([
            'type'      => 'array',
            'prev_text' => '← Previous',
            'next_text' => 'Next →',
        ]);
        if ($links) :
        ?>
            <nav class="pagination search-pagination" aria-label="Search-results pagination">
                <?php foreach ($links as $link) { echo wp_kses_post($link); } ?>
            </nav>
        <?php endif; ?>
    <?php else : ?>
        <section class="search-empty">
            <span class="kicker">NO RESULTS</span>
            <h2>We could not find posts for “<?php echo esc_html(get_search_query()); ?>”.</h2>
            <p>Try another name, place, topic or a broader expression.</p>
        </section>
    <?php endif; ?>
</main>
<?php get_footer(); ?>

