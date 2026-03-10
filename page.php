<?php
/**
 * Szablon strony statycznej (page.php)
 *
 * Używany automatycznie przez WordPress dla każdej strony
 * tworzonej przez: Strony → Dodaj nową.
 *
 * Treść edytowana przez edytor Gutenberg w panelu WP.
 * Wygląd (header, footer, kolory, typografia) spójny z całym serwisem.
 *
 * @package ZlobkiPolska
 */

get_header();

// Pobierz tytuł i dane strony do breadcrumba
$page_title    = get_the_title();
$parent_id     = wp_get_post_parent_id( get_the_ID() );
$parent_title  = $parent_id ? get_the_title( $parent_id ) : '';
$parent_url    = $parent_id ? get_permalink( $parent_id ) : '';
?>

<main class="site-main page-main" role="main" id="main-content">
<div class="container">

	<!-- ── Breadcrumb ───────────────────────────────────────── -->
	<nav class="page-breadcrumb" aria-label="Breadcrumb">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Strona główna</a>
		<span aria-hidden="true">›</span>
		<?php if ( $parent_id ) : ?>
			<a href="<?php echo esc_url( $parent_url ); ?>"><?php echo esc_html( $parent_title ); ?></a>
			<span aria-hidden="true">›</span>
		<?php endif; ?>
		<span aria-current="page"><?php echo esc_html( $page_title ); ?></span>
	</nav>

	<!-- ── Układ: treść + sidebar ───────────────────────────── -->
	<div class="page-layout">

		<!-- TREŚĆ GŁÓWNA -->
		<article class="page-content" id="page-<?php the_ID(); ?>" <?php post_class(); ?>>

			<!-- Nagłówek strony -->
			<header class="page-header">
				<h1 class="page-title"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="page-lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</header>

			<!-- Treść z edytora Gutenberg -->
			<div class="page-body entry-content">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();

					// Paginacja treści (jeśli użyto <!--nextpage-->)
					wp_link_pages( [
						'before'    => '<nav class="page-links"><span>' . __( 'Strony:', 'zlobki-polska' ) . '</span>',
						'after'     => '</nav>',
						'link_before' => '<span>',
						'link_after'  => '</span>',
					] );
				endwhile;
				?>
			</div>

			<!-- Data aktualizacji (opcjonalnie) -->
			<?php if ( get_the_modified_date() !== get_the_date() ) : ?>
				<footer class="page-footer">
					<p class="page-updated">
						Ostatnia aktualizacja: <time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>">
							<?php echo esc_html( get_the_modified_date( 'd.m.Y' ) ); ?>
						</time>
					</p>
				</footer>
			<?php endif; ?>

		</article>

		<!-- SIDEBAR: Nawigacja kontekstowa + CTA -->
		<aside class="page-sidebar" aria-label="Informacje dodatkowe">

			<!-- Podstrony (jeśli istnieją) -->
			<?php
			$children = get_pages( [
				'child_of'    => $parent_id ?: get_the_ID(),
				'sort_column' => 'menu_order',
			] );
			$siblings = $parent_id ? get_pages( [ 'child_of' => $parent_id, 'sort_column' => 'menu_order' ] ) : [];
			$nav_pages = $siblings ?: $children;

			if ( $nav_pages ) : ?>
			<div class="sidebar-card">
				<div class="sidebar-card__title">
					📄 <?php echo $siblings ? esc_html( $parent_title ) : 'Podstrony'; ?>
				</div>
				<nav class="page-subnav" aria-label="Nawigacja podstron">
					<?php if ( $parent_id ) : ?>
						<a href="<?php echo esc_url( $parent_url ); ?>" class="page-subnav__item">
							← <?php echo esc_html( $parent_title ); ?>
						</a>
					<?php endif; ?>
					<?php foreach ( $nav_pages as $p ) : ?>
						<a href="<?php echo esc_url( get_permalink( $p->ID ) ); ?>"
						   class="page-subnav__item <?php echo $p->ID === get_the_ID() ? 'page-subnav__item--active' : ''; ?>">
							<?php echo esc_html( $p->post_title ); ?>
						</a>
					<?php endforeach; ?>
				</nav>
			</div>
			<?php endif; ?>

			<!-- CTA: Wyszukiwarka -->
			<div class="sidebar-card sidebar-card--cta">
				<div class="sidebar-cta__icon">🍼</div>
				<div class="sidebar-cta__title">Szukasz żłobka?</div>
				<p class="sidebar-cta__text">Przeszukaj bazę ponad 6 500 żłobków i klubów dziecięcych w całej Polsce.</p>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'zlobek' ) ); ?>"
				   class="sidebar-cta__btn">
					🔍 Przejdź do wyszukiwarki
				</a>
			</div>

			<!-- Kontakt (wyświetl na wszystkich stronach oprócz samej strony kontakt) -->
			<?php if ( ! is_page( 'kontakt' ) ) : ?>
			<div class="sidebar-card">
				<div class="sidebar-card__title">✉️ Kontakt</div>
				<p style="font-size: .86rem; color: var(--color-text-muted); margin-bottom: .85rem; line-height: 1.6;">
					Masz pytania dotyczące danych lub serwisu?
				</p>
				<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'kontakt' ) ) ); ?>"
				   class="sidebar-cta__btn" style="background: var(--color-primary);">
					Napisz do nas
				</a>
			</div>
			<?php endif; ?>

		</aside>
	</div><!-- .page-layout -->

</div><!-- .container -->
</main>

<?php get_footer(); ?>
