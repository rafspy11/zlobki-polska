<?php
/**
 * The main template file — fallback.
 *
 * @package ZlobkiPolska
 */

get_header();
?>

<main class="site-main" role="main">
	<section class="section">
		<div class="container">
			<?php if ( have_posts() ) : ?>
				<div class="nurseries-grid">
					<?php while ( have_posts() ) : the_post(); ?>
						<?php get_template_part( 'template-parts/card', 'nursery' ); ?>
					<?php endwhile; ?>
				</div>
				<?php the_posts_pagination(); ?>
			<?php else : ?>
				<div class="empty-state">
					<div class="empty-state__icon">🔍</div>
					<h2>Nie znaleziono treści</h2>
					<p>Spróbuj skorzystać z wyszukiwarki powyżej.</p>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php get_footer(); ?>
