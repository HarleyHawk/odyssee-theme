<?php get_header(); ?>

<main class="main-content">
    <section class="page-section" style="padding-top: 120px; padding-bottom: 60px;">
        <?php while ( have_posts() ) : the_post(); ?>
            <h1 class="section-title" style="text-align: center; margin-bottom: 40px;"><?php the_title(); ?></h1>
            <div class="page-content" style="max-width: 800px; margin: 0 auto; color: var(--color-text); line-height: 1.8; font-size: 1.1rem; padding: 0 20px;">
                <?php the_content(); ?>
            </div>
        <?php endwhile; ?>
    </section>
</main> 

<?php get_footer(); ?>
