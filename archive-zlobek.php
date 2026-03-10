<?php
/**
 * Archive Template — Żłobki
 * Obsługuje: archiwum CPT, taksonomie (województwo, typ_instytucji, powiat)
 * oraz filtrowanie przez GET-parametry.
 *
 * @package ZlobkiPolska
 */

get_header();

/* ============================================================
   1. WYKRYJ KONTEKST TAKSONOMII — pre-wypełnij filtry
   ============================================================ */

$ctx_term     = null;
$ctx_taxonomy = '';

if ( is_tax( 'wojewodztwo' ) ) {
	$ctx_term     = get_queried_object();
	$ctx_taxonomy = 'wojewodztwo';
} elseif ( is_tax( 'typ_instytucji' ) ) {
	$ctx_term     = get_queried_object();
	$ctx_taxonomy = 'typ_instytucji';
} elseif ( is_tax( 'powiat' ) ) {
	$ctx_term     = get_queried_object();
	$ctx_taxonomy = 'powiat';
}

/* ============================================================
   2. FILTRY Z URL — nadpisują kontekst taksonomii
   ============================================================ */

if ( ! empty( $_GET['woj'] ) ) {
	$current_woj = sanitize_text_field( $_GET['woj'] );
} elseif ( $ctx_taxonomy === 'wojewodztwo' && $ctx_term ) {
	$current_woj = $ctx_term->slug;
} else {
	$current_woj = '';
}

if ( ! empty( $_GET['typ'] ) ) {
	$current_typ = sanitize_text_field( $_GET['typ'] );
} elseif ( $ctx_taxonomy === 'typ_instytucji' && $ctx_term ) {
	$current_typ = $ctx_term->slug;
} else {
	$current_typ = '';
}

if ( ! empty( $_GET['powiat'] ) ) {
	$current_powiat = sanitize_text_field( $_GET['powiat'] );
} elseif ( $ctx_taxonomy === 'powiat' && $ctx_term ) {
	$current_powiat = $ctx_term->slug;
} else {
	$current_powiat = '';
}

$current_s   = sanitize_text_field( $_GET['s'] ?? '' );
$max_price   = absint( $_GET['max_price'] ?? 0 );
$niepelnosp  = ! empty( $_GET['niepelnosprawni'] );
$show_zawies = ! empty( $_GET['zawieszone'] );
$sort_val    = sanitize_text_field( $_GET['sort'] ?? 'title' );

/* ============================================================
   3. BUDUJ WP_Query
   ============================================================ */

$paged = max( 1, get_query_var( 'paged' ) ?: absint( $_GET['paged'] ?? 1 ) );

$args = [
	'post_type'      => ZLOBKI_SLUG,
	'post_status'    => 'publish',
	'posts_per_page' => 12,
	'paged'          => $paged,
];

$tax_q  = [];
$meta_q = [];

if ( $current_s )      { $args['s'] = $current_s; }
if ( $current_woj )    { $tax_q[] = [ 'taxonomy' => 'wojewodztwo',    'field' => 'slug', 'terms' => $current_woj ]; }
if ( $current_typ )    { $tax_q[] = [ 'taxonomy' => 'typ_instytucji', 'field' => 'slug', 'terms' => $current_typ ]; }
if ( $current_powiat ) { $tax_q[] = [ 'taxonomy' => 'powiat',         'field' => 'slug', 'terms' => $current_powiat ]; }
if ( $max_price > 0 )  { $meta_q[] = [ 'key' => 'opłata_miesięczna', 'value' => $max_price, 'compare' => '<=', 'type' => 'DECIMAL' ]; }
if ( $niepelnosp )     { $meta_q[] = [ 'key' => 'dostosowany_niepelnosp', 'value' => '1' ]; }
if ( ! $show_zawies )  { $meta_q[] = [ 'key' => 'zawieszona_dzialalnosc', 'value' => '0' ]; }

if ( $tax_q )  { $args['tax_query']  = array_merge( [ 'relation' => 'AND' ], $tax_q ); }
if ( $meta_q ) { $args['meta_query'] = array_merge( [ 'relation' => 'AND' ], $meta_q ); }

switch ( $sort_val ) {
	case 'price_asc':  $args['meta_key'] = 'opłata_miesięczna'; $args['orderby'] = 'meta_value_num'; $args['order'] = 'ASC';  break;
	case 'price_desc': $args['meta_key'] = 'opłata_miesięczna'; $args['orderby'] = 'meta_value_num'; $args['order'] = 'DESC'; break;
	case 'capacity':   $args['meta_key'] = 'liczba_miejsc';     $args['orderby'] = 'meta_value_num'; $args['order'] = 'DESC'; break;
	case 'newest':     $args['orderby'] = 'date'; $args['order'] = 'DESC'; break;
	default:           $args['orderby'] = 'title'; $args['order'] = 'ASC';
}

$query = new WP_Query( $args );

/* ============================================================
   4. OPCJE FILTRÓW
   ============================================================ */

$wojewodztwa = get_terms( [ 'taxonomy' => 'wojewodztwo',    'hide_empty' => true, 'orderby' => 'name' ] );
$typy        = get_terms( [ 'taxonomy' => 'typ_instytucji', 'hide_empty' => true, 'orderby' => 'name' ] );
$powiaty     = $current_woj
	? get_terms( [ 'taxonomy' => 'powiat', 'hide_empty' => true, 'orderby' => 'name' ] )
	: [];

/* ============================================================
   5. URLS
   ============================================================ */

$archive_url = get_post_type_archive_link( ZLOBKI_SLUG );
$form_action = $archive_url; // formularz zawsze do archiwum

/* ============================================================
   6. NAGŁÓWEK KONTEKSTOWY
   ============================================================ */

$page_title    = 'Wyszukiwarka żłobków i klubów dziecięcych';
$page_subtitle = '';

if ( $ctx_term ) {
	switch ( $ctx_taxonomy ) {
		case 'wojewodztwo':
			$woj_name      = zlobki_ucwords_pl( $ctx_term->name );
			$page_title    = 'Żłobki i kluby dziecięce — woj. ' . $woj_name;
			$page_subtitle = 'Wszystkie instytucji opieki nad dzieckiem w województwie ' . $woj_name;
			break;
		case 'typ_instytucji':
			$icon          = str_contains( $ctx_term->slug, 'klub' ) ? '🎯' : '🍼';
			$page_title    = $icon . ' ' . $ctx_term->name . ' w Polsce';
			$page_subtitle = 'Pełna lista: ' . $ctx_term->count . ' placówek w Polsce';
			break;
		case 'powiat':
			$page_title    = 'Żłobki — powiat ' . $ctx_term->name;
			$page_subtitle = $ctx_term->count . ' instytucji w powiecie ' . $ctx_term->name;
			break;
	}
} elseif ( $current_woj ) {
	$t = get_term_by( 'slug', $current_woj, 'wojewodztwo' );
	if ( $t ) { $page_title = 'Żłobki — woj. ' . zlobki_ucwords_pl( $t->name ); }
} elseif ( $current_typ ) {
	$t = get_term_by( 'slug', $current_typ, 'typ_instytucji' );
	if ( $t ) { $page_title = $t->name . ' w Polsce'; }
} elseif ( $current_s ) {
	$page_title    = 'Wyniki: „' . esc_html( $current_s ) . '"';
	$page_subtitle = 'Wyszukiwarka żłobków i klubów dziecięcych';
}

/* ============================================================
   7. AKTYWNE FILTRY (tagi do usunięcia)
   ============================================================ */

$active_filters = [];
if ( $current_woj ) {
	$t = get_term_by( 'slug', $current_woj, 'wojewodztwo' );
	$active_filters[] = [ 'label' => '📍 ' . ( $t ? zlobki_ucwords_pl( $t->name ) : $current_woj ), 'param' => 'woj' ];
}
if ( $current_typ ) {
	$t = get_term_by( 'slug', $current_typ, 'typ_instytucji' );
	$active_filters[] = [ 'label' => ( $t ? $t->name : $current_typ ), 'param' => 'typ' ];
}
if ( $current_powiat ) {
	$t = get_term_by( 'slug', $current_powiat, 'powiat' );
	$active_filters[] = [ 'label' => '🏙️ pow. ' . ( $t ? $t->name : $current_powiat ), 'param' => 'powiat' ];
}
if ( $current_s )    { $active_filters[] = [ 'label' => '🔍 „' . $current_s . '"', 'param' => 's' ]; }
if ( $max_price > 0 ){ $active_filters[] = [ 'label' => '💰 max ' . number_format( $max_price, 0, ',', ' ' ) . ' zł', 'param' => 'max_price' ]; }
if ( $niepelnosp )   { $active_filters[] = [ 'label' => '♿ Dla niepełnosprawnych', 'param' => 'niepelnosprawni' ]; }

// Helper: usuń parametr z aktualnego URL
function zlobki_remove_param( string $param ): string {
	$params = $_GET;
	unset( $params[ $param ] );
	$base = get_post_type_archive_link( ZLOBKI_SLUG );
	return $params ? $base . '?' . http_build_query( $params ) : $base;
}

?>

<main class="site-main" role="main">
<section class="section" style="padding-top: 2rem;">
<div class="container">

	<!-- PAGE HEADER -->
	<div style="margin-bottom: 1.75rem;">
		<!-- Breadcrumb -->
		<nav aria-label="Breadcrumb" style="font-size: 0.8rem; color: var(--color-text-muted); margin-bottom: 0.85rem; display: flex; align-items: center; flex-wrap: wrap; gap: 0.35rem;">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:var(--color-text-muted);font-weight:600;">Strona główna</a>
			<span>›</span>
			<?php if ( $ctx_term || $current_woj || $current_typ || $current_s ) : ?>
				<a href="<?php echo esc_url( $archive_url ); ?>" style="color:var(--color-text-muted);font-weight:600;">Wyszukiwarka</a>
				<span>›</span>
				<span style="color:var(--color-text);font-weight:700;"><?php echo esc_html( $page_title ); ?></span>
			<?php else : ?>
				<span style="color:var(--color-text);font-weight:700;">Wyszukiwarka</span>
			<?php endif; ?>
		</nav>

		<h1 style="font-size: clamp(1.4rem, 3vw, 1.9rem); margin-bottom: 0.3rem;">
			<?php echo esc_html( $page_title ); ?>
		</h1>
		<?php if ( $page_subtitle ) : ?>
			<p style="color:var(--color-text-muted);font-size:.9rem;margin:0;"><?php echo esc_html( $page_subtitle ); ?></p>
		<?php endif; ?>
	</div>

	<!-- TOP SEARCH BAR -->
	<form method="get" action="<?php echo esc_url( $form_action ); ?>" class="search-box" style="margin-bottom: 1.75rem; max-width: none;">
		<?php // Zachowaj aktywne filtry taksonomii jako hidden przy szukaniu tekstu ?>
		<?php if ( $current_woj )    : ?><input type="hidden" name="woj"    value="<?php echo esc_attr( $current_woj ); ?>"><?php endif; ?>
		<?php if ( $current_typ )    : ?><input type="hidden" name="typ"    value="<?php echo esc_attr( $current_typ ); ?>"><?php endif; ?>
		<?php if ( $current_powiat ) : ?><input type="hidden" name="powiat" value="<?php echo esc_attr( $current_powiat ); ?>"><?php endif; ?>

		<div class="search-box__row">
			<div class="search-box__field">
				<label class="search-box__label" for="top-s">Szukaj po nazwie lub miejscowości</label>
				<input type="text" id="top-s" name="s" class="search-box__input"
				       value="<?php echo esc_attr( $current_s ); ?>" placeholder="Nazwa, miejscowość...">
			</div>
			<div class="search-box__field">
				<label class="search-box__label" for="top-woj">Województwo</label>
				<select id="top-woj" name="woj" class="search-box__select">
					<option value="">— Wszystkie —</option>
					<?php if ( ! is_wp_error( $wojewodztwa ) ) foreach ( $wojewodztwa as $t ) : ?>
						<option value="<?php echo esc_attr( $t->slug ); ?>" <?php selected( $current_woj, $t->slug ); ?>>
							<?php echo esc_html( zlobki_ucwords_pl( $t->name ) ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<button type="submit" class="search-box__btn">🔍 Szukaj</button>
		</div>
	</form>

	<div class="search-layout">

		<!-- SIDEBAR FILTERS -->
		<aside class="filters-panel" id="filters-panel">
			<div class="filters-panel__title">
				<span>🎛️ Filtry</span>
				<a href="<?php echo esc_url( $archive_url ); ?>" class="filters-clear">Wyczyść</a>
			</div>

			<form method="get" action="<?php echo esc_url( $form_action ); ?>" id="filters-form">

				<div class="filter-group">
					<label class="filter-group__label" for="f-woj">Województwo</label>
					<select name="woj" id="f-woj" onchange="this.form.submit()">
						<option value="">— Wszystkie —</option>
						<?php if ( ! is_wp_error( $wojewodztwa ) ) foreach ( $wojewodztwa as $t ) : ?>
							<option value="<?php echo esc_attr( $t->slug ); ?>" <?php selected( $current_woj, $t->slug ); ?>>
								<?php echo esc_html( zlobki_ucwords_pl( $t->name ) ); ?> (<?php echo esc_html( $t->count ); ?>)
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<?php if ( $current_woj && ! is_wp_error( $powiaty ) && ! empty( $powiaty ) ) : ?>
				<div class="filter-group">
					<label class="filter-group__label" for="f-powiat">Powiat</label>
					<select name="powiat" id="f-powiat" onchange="this.form.submit()">
						<option value="">— Wszystkie powiaty —</option>
						<?php foreach ( $powiaty as $t ) : ?>
							<option value="<?php echo esc_attr( $t->slug ); ?>" <?php selected( $current_powiat, $t->slug ); ?>>
								<?php echo esc_html( $t->name ); ?> (<?php echo esc_html( $t->count ); ?>)
							</option>
						<?php endforeach; ?>
					</select>
				</div>
				<?php endif; ?>

				<div class="filter-group">
					<label class="filter-group__label" for="f-typ">Typ instytucji</label>
					<select name="typ" id="f-typ" onchange="this.form.submit()">
						<option value="">— Wszystkie —</option>
						<?php if ( ! is_wp_error( $typy ) ) foreach ( $typy as $t ) : ?>
							<option value="<?php echo esc_attr( $t->slug ); ?>" <?php selected( $current_typ, $t->slug ); ?>>
								<?php echo esc_html( $t->name ); ?> (<?php echo esc_html( $t->count ); ?>)
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="filter-group">
					<label class="filter-group__label" for="f-search">Szukaj po nazwie</label>
					<input type="text" name="s" id="f-search"
					       value="<?php echo esc_attr( $current_s ); ?>" placeholder="Wpisz nazwę...">
				</div>

				<div class="filter-group">
					<label class="filter-group__label" for="f-sort">Sortowanie</label>
					<select name="sort" id="f-sort" onchange="this.form.submit()">
						<option value="title"      <?php selected( $sort_val, 'title' ); ?>>Nazwa A–Z</option>
						<option value="price_asc"  <?php selected( $sort_val, 'price_asc' ); ?>>Cena rosnąco</option>
						<option value="price_desc" <?php selected( $sort_val, 'price_desc' ); ?>>Cena malejąco</option>
						<option value="capacity"   <?php selected( $sort_val, 'capacity' ); ?>>Liczba miejsc</option>
						<option value="newest"     <?php selected( $sort_val, 'newest' ); ?>>Najnowsze</option>
					</select>
				</div>

				<div class="filter-group">
					<label class="filter-group__label">
						Maks. opłata: <strong id="price-val" style="color:var(--color-accent)">
							<?php echo $max_price > 0 ? number_format( $max_price, 0, ',', ' ' ) . ' zł' : 'Brak limitu'; ?>
						</strong>
					</label>
					<div class="price-range">
						<input type="range" id="f-price"
						       min="0" max="5000" step="100"
						       value="<?php echo esc_attr( $max_price ?: 5000 ); ?>"
						       <?php echo $max_price > 0 ? 'name="max_price"' : ''; ?>>
					</div>
				</div>

				<div class="filter-group">
					<label class="filter-group__label">Dodatkowe</label>
					<div class="filter-checkboxes">
						<label class="filter-checkbox">
							<input type="checkbox" name="niepelnosprawni" value="1" <?php checked( $niepelnosp ); ?>>
							♿ Dla niepełnosprawnych
						</label>
						<label class="filter-checkbox">
							<input type="checkbox" name="zawieszone" value="1" <?php checked( $show_zawies ); ?>>
							🔔 Pokaż zawieszone
						</label>
					</div>
				</div>

				<button type="submit" class="search-box__btn" style="width:100%;justify-content:center;">
					Zastosuj filtry
				</button>
			</form>
		</aside>

		<!-- RESULTS -->
		<div class="nurseries-results">

			<div class="section-header" style="flex-direction:column;align-items:flex-start;gap:.65rem;">
				<div style="display:flex;align-items:center;justify-content:space-between;width:100%;flex-wrap:wrap;gap:.5rem;">
					<div class="results-count">
						Znaleziono <strong><?php echo esc_html( $query->found_posts ); ?></strong>
						<?php echo $query->found_posts === 1 ? 'instytucję' : 'instytucji'; ?>
					</div>
					<button class="search-box__filter-chip" id="toggle-filters-btn">🎛️ Filtry</button>
				</div>

				<?php if ( $active_filters ) : ?>
				<div style="display:flex;flex-wrap:wrap;gap:.4rem;align-items:center;">
					<span style="font-size:.73rem;color:var(--color-text-muted);font-weight:700;">Aktywne:</span>
					<?php foreach ( $active_filters as $f ) : ?>
						<a href="<?php echo esc_url( zlobki_remove_param( $f['param'] ) ); ?>"
						   style="display:inline-flex;align-items:center;gap:.3rem;background:rgba(45,125,123,.1);color:var(--color-primary);border-radius:2rem;padding:.2rem .65rem;font-size:.75rem;font-weight:700;text-decoration:none;border:1px solid rgba(45,125,123,.2);">
							<?php echo esc_html( $f['label'] ); ?> ✕
						</a>
					<?php endforeach; ?>
					<a href="<?php echo esc_url( $archive_url ); ?>"
					   style="font-size:.73rem;color:var(--color-accent);font-weight:700;margin-left:.2rem;">
						Wyczyść wszystkie
					</a>
				</div>
				<?php endif; ?>
			</div>

			<div class="nurseries-grid" id="results-grid" style="margin-top:1.25rem;">
				<?php
				if ( $query->have_posts() ) :
					while ( $query->have_posts() ) :
						$query->the_post();
						get_template_part( 'template-parts/card', 'nursery' );
					endwhile;
					wp_reset_postdata();
				else :
					echo '<div class="empty-state" style="grid-column:1/-1">
						<div class="empty-state__icon">🔍</div>
						<h3>Nie znaleziono instytucji</h3>
						<p>Spróbuj zmienić kryteria lub <a href="' . esc_url( $archive_url ) . '">wyczyść filtry</a>.</p>
					</div>';
				endif;
				?>
			</div>

			<?php if ( $query->max_num_pages > 1 ) : ?>
			<nav class="pagination" aria-label="Paginacja" style="margin-top:2.5rem;">
				<?php
				$paginate_base = $ctx_term
					? add_query_arg( array_filter( [
						'woj'             => $current_woj    ?: null,
						'typ'             => $current_typ    ?: null,
						'powiat'          => $current_powiat ?: null,
						's'               => $current_s      ?: null,
						'max_price'       => $max_price      ?: null,
						'niepelnosprawni' => $niepelnosp  ? '1' : null,
						'zawieszone'      => $show_zawies ? '1' : null,
						'sort'            => $sort_val !== 'title' ? $sort_val : null,
						'paged'           => '%#%',
					] ), $archive_url )
					: add_query_arg( 'paged', '%#%' );

				echo paginate_links( [
					'base'      => $paginate_base,
					'format'    => '',
					'current'   => $paged,
					'total'     => $query->max_num_pages,
					'prev_text' => '← Poprzednia',
					'next_text' => 'Następna →',
					'mid_size'  => 2,
					'type'      => 'list',
				] );
				?>
			</nav>
			<?php endif; ?>

		</div>
	</div>
</div>
</section>
</main>

<script>
document.getElementById('toggle-filters-btn').addEventListener('click', function () {
	const panel = document.getElementById('filters-panel');
	panel.classList.toggle('open');
	this.textContent = panel.classList.contains('open') ? '✕ Zamknij' : '🎛️ Filtry';
});

const priceRange = document.getElementById('f-price');
const priceVal   = document.getElementById('price-val');
if ( priceRange && priceVal ) {
	priceRange.addEventListener('input', function () {
		const v = parseInt( this.value );
		if ( v >= 5000 ) {
			priceVal.textContent = 'Brak limitu';
			this.removeAttribute('name');
		} else {
			priceVal.textContent = v.toLocaleString('pl-PL') + ' zł';
			this.setAttribute('name', 'max_price');
		}
	});
}
</script>

<?php get_footer(); ?>
