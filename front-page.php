<?php
/**
 * Front Page Template
 *
 * @package ZlobkiPolska
 */

get_header();

// Stats
$total    = wp_count_posts( 'zlobek' )->publish ?? 0;
$woj_count = wp_count_terms( [ 'taxonomy' => 'wojewodztwo', 'hide_empty' => true ] );
$zlobki_count = get_terms( [ 'taxonomy' => 'typ_instytucji', 'slug' => 'zlobek', 'fields' => 'count' ] );

// Get taxonomies for search
$wojewodztwa = get_terms( [ 'taxonomy' => 'wojewodztwo', 'hide_empty' => true, 'orderby' => 'name' ] );
$typy        = get_terms( [ 'taxonomy' => 'typ_instytucji', 'hide_empty' => true ] );
?>

<!-- ====================================================
     HERO
     ==================================================== -->
<section class="hero">
	<div class="container">
		<div class="hero__inner">

			<div class="hero__badge">
				<?php echo esc_html( get_theme_mod( 'zlobki_hero_badge', '🇵🇱 Oficjalny rejestr MRiPS' ) ); ?>
			</div>

			<h1><?php echo esc_html( get_theme_mod( 'zlobki_hero_title', 'Znajdź idealny żłobek dla swojego' ) ); ?> <em><?php echo esc_html( get_theme_mod( 'zlobki_hero_title_em', 'maluszka' ) ); ?></em></h1>

			<p class="hero__subtitle">
				<?php
				$default_subtitle = sprintf(
					'Przeszukaj bazę ponad %s żłobków i klubów dziecięcych w całej Polsce. Sprawdź ceny, dostępność miejsc i dane kontaktowe.',
					number_format( $total, 0, ',', ' ' )
				);
				echo wp_kses_post( get_theme_mod( 'zlobki_hero_subtitle', $default_subtitle ) );
				?>
			</p>

			<div class="hero__stats">
				<div class="hero__stat">
					<span class="hero__stat-num"><?php echo number_format( $total, 0, ',', ' ' ); ?>+</span>
					<span class="hero__stat-label">Instytucji</span>
				</div>
				<div class="hero__stat">
					<span class="hero__stat-num">16</span>
					<span class="hero__stat-label">Województw</span>
				</div>
				<div class="hero__stat">
					<span class="hero__stat-num">5 397</span>
					<span class="hero__stat-label">Żłobków</span>
				</div>
				<div class="hero__stat">
					<span class="hero__stat-num">1 160</span>
					<span class="hero__stat-label">Klubów dziecięcych</span>
				</div>
			</div>

			<!-- Search Box -->
			<div class="search-box" role="search">
				<div class="search-box__row">
					<div class="search-box__field">
						<label class="search-box__label" for="hero-search">Nazwa lub miejscowość</label>
						<input type="text"
						       id="hero-search"
						       class="search-box__input"
						       placeholder="<?php echo esc_attr( get_theme_mod( 'zlobki_hero_search_ph', 'np. Warszawa, Kraków, Żłobek Słoneczko...' ) ); ?>"
						       autocomplete="off">
					</div>
					<div class="search-box__field">
						<label class="search-box__label" for="hero-woj">Województwo</label>
						<select id="hero-woj" class="search-box__select">
							<option value="">— Wszystkie województwa —</option>
							<?php if ( ! is_wp_error( $wojewodztwa ) ) : foreach ( $wojewodztwa as $term ) : ?>
								<option value="<?php echo esc_attr( $term->slug ); ?>">
									<?php echo esc_html( zlobki_ucwords_pl( $term->name ) ); ?>
									(<?php echo esc_html( $term->count ); ?>)
								</option>
							<?php endforeach; endif; ?>
						</select>
					</div>
					<button class="search-box__btn" id="hero-search-btn">
						<?php echo esc_html( get_theme_mod( 'zlobki_hero_btn', '🔍 Szukaj' ) ); ?>
					</button>
				</div>
				<div class="search-box__filters">
					<label class="search-box__filter-chip" id="chip-niepelnosp">
						<input type="checkbox" value="1"><?php echo esc_html( get_theme_mod( 'zlobki_hero_chip_np', '♿ Dla niepełnosprawnych' ) ); ?>
					</label>
					<label class="search-box__filter-chip" id="chip-typ-klub">
						<input type="checkbox" value="klub-dzieciecy"><?php echo esc_html( get_theme_mod( 'zlobki_hero_chip_klub', '🎯 Tylko kluby dziecięce' ) ); ?>
					</label>
					<label class="search-box__filter-chip" id="chip-typ-zlobek">
						<input type="checkbox" value="zlobek"><?php echo esc_html( get_theme_mod( 'zlobki_hero_chip_zlobek', '🍼 Tylko żłobki' ) ); ?>
					</label>
				</div>
			</div>

		</div>
	</div>
</section>

<!-- ====================================================
     STATS BAR
     ==================================================== -->
<section class="stats-section">
	<div class="container">
		<div class="stats-grid">
			<div class="stat-item">
				<span class="stat-item__num">6 557</span>
				<span class="stat-item__label">Instytucji w bazie</span>
			</div>
			<div class="stat-item">
				<span class="stat-item__num">~350k</span>
				<span class="stat-item__label">Miejsc łącznie</span>
			</div>
			<div class="stat-item">
				<span class="stat-item__num">16</span>
				<span class="stat-item__label">Województw</span>
			</div>
			<div class="stat-item">
				<span class="stat-item__num">100%</span>
				<span class="stat-item__label">Dane z rejestru MRiPS</span>
			</div>
		</div>
	</div>
</section>

<!-- ====================================================
     LATEST NURSERIES
     ==================================================== -->
<section class="nurseries-section section">
	<div class="container">
		<div class="section-header">
			<h2>Ostatnio dodane</h2>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'zlobek' ) ); ?>" class="btn--details">
				Przeglądaj wszystkie →
			</a>
		</div>

		<div class="nurseries-grid" id="latest-grid">
			<?php
			$latest = new WP_Query( [
				'post_type'      => 'zlobek',
				'posts_per_page' => 6,
				'post_status'    => 'publish',
				'orderby'        => 'date',
				'order'          => 'DESC',
			] );
			if ( $latest->have_posts() ) :
				while ( $latest->have_posts() ) :
					$latest->the_post();
					get_template_part( 'template-parts/card', 'nursery' );
				endwhile;
				wp_reset_postdata();
			endif;
			?>
		</div>
	</div>
</section>

<!-- ====================================================
     WOJEWÓDZTWA
     ==================================================== -->
<section class="section section--sm" style="background: var(--color-bg-alt);">
	<div class="container">
		<div class="section-header">
			<h2>Przeglądaj według województwa</h2>
		</div>
		<div class="regions-grid">
			<?php
			if ( ! is_wp_error( $wojewodztwa ) ) :
				foreach ( $wojewodztwa as $term ) :
					// Link do archiwum z filtrem województwa — gwarantuje działanie filtrów
					$region_url = add_query_arg( 'woj', $term->slug, get_post_type_archive_link( 'zlobek' ) );
			?>
				<a href="<?php echo esc_url( $region_url ); ?>" class="region-card">
					<span class="region-card__name"><?php echo esc_html( zlobki_ucwords_pl( $term->name ) ); ?></span>
					<span class="region-card__count"><?php echo esc_html( $term->count ); ?></span>
				</a>
			<?php endforeach; endif; ?>
		</div>
	</div>
</section>

<!-- ====================================================
     FEATURES
     ==================================================== -->
<section class="features-section section">
	<div class="container">
		<div class="section-header text-center" style="flex-direction: column; align-items: center;">
			<h2>Dlaczego warto korzystać z naszej bazy?</h2>
			<p style="color: var(--color-text-muted); max-width: 520px; margin-top: 0.5rem;">
				Dane aktualizowane z oficjalnego rejestru Ministerstwa Rodziny i Polityki Społecznej.
			</p>
		</div>
		<div class="features-grid">
			<div class="feature-card">
				<span class="feature-card__icon">🏛️</span>
				<h3>Oficjalne dane</h3>
				<p>Wszystkie informacje pochodzą bezpośrednio z rejestru żłobków i klubów dziecięcych prowadzonego przez MRiPS.</p>
			</div>
			<div class="feature-card">
				<span class="feature-card__icon">🔍</span>
				<h3>Zaawansowane filtry</h3>
				<p>Filtruj po województwie, cenie, dostępności miejsc, dostosowaniu dla niepełnosprawnych i wielu innych kryteriach.</p>
			</div>
			<div class="feature-card">
				<span class="feature-card__icon">📊</span>
				<h3>Pełne informacje</h3>
				<p>Sprawdź ceny, godziny otwarcia, dane kontaktowe, zniżki i dostępność miejsc w jednym miejscu.</p>
			</div>
			<div class="feature-card">
				<span class="feature-card__icon">📱</span>
				<h3>Responsywny design</h3>
				<p>Serwis działa doskonale na telefonie, tablecie i komputerze — szukaj żłobka w dowolnym miejscu.</p>
			</div>
			<div class="feature-card">
				<span class="feature-card__icon">🗺️</span>
				<h3>Mapa instytucji</h3>
				<p>Znajdź żłobek najbliżej Twojego domu korzystając z interaktywnej mapy całej Polski.</p>
			</div>
			<div class="feature-card">
				<span class="feature-card__icon">🔄</span>
				<h3>Regularne aktualizacje</h3>
				<p>Baza jest regularnie aktualizowana na podstawie nowych eksportów z rejestru MRiPS.</p>
			</div>
		</div>
	</div>
</section>

<script>
document.getElementById('hero-search-btn').addEventListener('click', function() {
	const q   = document.getElementById('hero-search').value.trim();
	const woj = document.getElementById('hero-woj').value;
	const url = new URL('<?php echo esc_url( get_post_type_archive_link( "zlobek" ) ); ?>');
	if (q)   url.searchParams.set('s', q);
	if (woj) url.searchParams.set('woj', woj);
	window.location.href = url.toString();
});
document.getElementById('hero-search').addEventListener('keypress', function(e) {
	if (e.key === 'Enter') document.getElementById('hero-search-btn').click();
});
// Toggle chip active state
document.querySelectorAll('.search-box__filter-chip').forEach(chip => {
	chip.querySelector('input').addEventListener('change', function() {
		chip.classList.toggle('active', this.checked);
	});
});
</script>

<?php get_footer(); ?>
