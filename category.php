<?php
get_header();

$category = get_queried_object();
$has_posts = have_posts();
?>
<main id="conteudo" class="shell category-page">
    <?php if ($has_posts) : the_post(); ?>
        <section class="category-lead" aria-label="Postagem em destaque">
            <a class="lead-image" href="<?php the_permalink(); ?>">
                <?php jnt_story_image('large', 'https://images.unsplash.com/photo-1519817650390-64a93db51149?auto=format&fit=crop&w=1200&q=85'); ?>
            </a>
            <article>
                <span class="kicker"><?php echo esc_html(jnt_category()); ?></span>
                <h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 26)); ?></p>
                <a class="gold-button" href="<?php the_permalink(); ?>">Ler artigo <span>→</span></a>
                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
            </article>
        </section>

        <?php if (have_posts()) : ?>
            <section class="article-grid category-article-grid" aria-label="Mais publicações em <?php echo esc_attr($category->name ?? 'categoria'); ?>">
                <?php while (have_posts()) : the_post(); ?>
                    <article>
                        <a class="category-card-image" href="<?php the_permalink(); ?>">
                            <?php jnt_story_image('medium_large', 'https://images.unsplash.com/photo-1495020689067-958852a7765e?auto=format&fit=crop&w=700&q=80'); ?>
                        </a>
                        <span class="kicker"><?php echo esc_html(jnt_category()); ?></span>
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 20)); ?></p>
                        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                    </article>
                <?php endwhile; ?>
            </section>
        <?php endif; ?>

        <?php
        $links = paginate_links([
            'type'      => 'array',
            'prev_text' => '← Anterior',
            'next_text' => 'Próxima →',
        ]);
        ?>
            <nav class="pagination" aria-label="Paginação de <?php echo esc_attr($category->name ?? 'categoria'); ?>">
                <?php if ($links) : ?>
                    <?php foreach ($links as $link) { echo wp_kses_post($link); } ?>
                <?php else : ?>
                    <span class="page-numbers prev is-disabled" aria-disabled="true">← Anterior</span>
                    <span class="page-numbers current" aria-current="page">1</span>
                    <span class="page-numbers next is-disabled" aria-disabled="true">Próxima →</span>
                <?php endif; ?>
            </nav>
    <?php else : ?>
        <section class="category-empty">
            <span class="kicker"><?php echo esc_html($category->name ?? 'Categoria'); ?></span>
            <h1>Não há publicações nesta categoria.</h1>
        </section>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
