<?php
/**
 * Żłobki Polska Theme — functions.php
 *
 * @package ZlobkiPolska
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

define( 'ZLOBKI_VERSION', '1.0.0' );
define( 'ZLOBKI_DIR',     get_template_directory() );
define( 'ZLOBKI_URI',     get_template_directory_uri() );
define( 'ZLOBKI_SLUG',    'zlobek' );  // CPT slug

/* ============================================================
   1. THEME SETUP
   ============================================================ */
add_action( 'after_setup_theme', function () {
	load_theme_textdomain( 'zlobki-polska', ZLOBKI_DIR . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'gallery', 'caption', 'style', 'script' ] );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );

	register_nav_menus( [
		'primary' => __( 'Menu główne', 'zlobki-polska' ),
		'footer'  => __( 'Menu w stopce', 'zlobki-polska' ),
	] );
} );

/* ============================================================
   2. ENQUEUE ASSETS
   ============================================================ */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'zlobki-main',
		ZLOBKI_URI . '/assets/css/main.css',
		[],
		ZLOBKI_VERSION
	);
	wp_enqueue_script(
		'zlobki-main',
		ZLOBKI_URI . '/assets/js/main.js',
		[],
		ZLOBKI_VERSION,
		true
	);
	// Pass AJAX URL + nonce to JS
	wp_localize_script( 'zlobki-main', 'ZlobkiAjax', [
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'zlobki_search' ),
	] );
} );

/* ============================================================
   3. CUSTOM POST TYPE — ŻŁOBEK / KLUB DZIECIĘCY
   ============================================================ */
add_action( 'init', function () {

	register_post_type( ZLOBKI_SLUG, [
		'labels' => [
			'name'               => __( 'Żłobki i Kluby', 'zlobki-polska' ),
			'singular_name'      => __( 'Instytucja', 'zlobki-polska' ),
			'menu_name'          => __( 'Żłobki', 'zlobki-polska' ),
			'add_new'            => __( 'Dodaj nową', 'zlobki-polska' ),
			'add_new_item'       => __( 'Dodaj nową instytucję', 'zlobki-polska' ),
			'edit_item'          => __( 'Edytuj instytucję', 'zlobki-polska' ),
			'search_items'       => __( 'Szukaj instytucji', 'zlobki-polska' ),
			'not_found'          => __( 'Nie znaleziono instytucji', 'zlobki-polska' ),
		],
		'public'              => true,
		'has_archive'         => true,
		'rewrite'             => [ 'slug' => 'zlobki' ],
		'supports'            => [ 'title', 'editor', 'custom-fields', 'thumbnail' ],
		'menu_icon'           => 'dashicons-building',
		'show_in_rest'        => true,
		'menu_position'       => 5,
	] );

	/* Taxonomy: Województwo */
	register_taxonomy( 'wojewodztwo', ZLOBKI_SLUG, [
		'labels' => [
			'name'          => __( 'Województwa', 'zlobki-polska' ),
			'singular_name' => __( 'Województwo', 'zlobki-polska' ),
			'menu_name'     => __( 'Województwa', 'zlobki-polska' ),
		],
		'hierarchical'      => true,
		'public'            => true,
		'show_in_rest'      => true,
		'rewrite'           => [ 'slug' => 'wojewodztwo' ],
		'show_admin_column' => true,
	] );

	/* Taxonomy: Typ instytucji */
	register_taxonomy( 'typ_instytucji', ZLOBKI_SLUG, [
		'labels' => [
			'name'          => __( 'Typy instytucji', 'zlobki-polska' ),
			'singular_name' => __( 'Typ instytucji', 'zlobki-polska' ),
			'menu_name'     => __( 'Typy', 'zlobki-polska' ),
		],
		'hierarchical'      => true,
		'public'            => true,
		'show_in_rest'      => true,
		'rewrite'           => [ 'slug' => 'typ' ],
		'show_admin_column' => true,
	] );

	/* Taxonomy: Powiat */
	register_taxonomy( 'powiat', ZLOBKI_SLUG, [
		'labels' => [
			'name'          => __( 'Powiaty', 'zlobki-polska' ),
			'singular_name' => __( 'Powiat', 'zlobki-polska' ),
		],
		'hierarchical'      => true,
		'public'            => true,
		'show_in_rest'      => true,
		'rewrite'           => [ 'slug' => 'powiat' ],
		'show_admin_column' => false,
	] );
} );

/* ============================================================
   4. CUSTOM META FIELDS REGISTRATION
   ============================================================ */
add_action( 'init', function () {
	$meta_fields = [
		'zlobek_id'               => 'string',
		'typ_instytucji_label'    => 'string',
		'wojewodztwo_label'       => 'string',
		'powiat_label'            => 'string',
		'gmina'                   => 'string',
		'miejscowosc'             => 'string',
		'ulica'                   => 'string',
		'nr_domu'                 => 'string',
		'nr_lokalu'               => 'string',
		'kod_pocztowy'            => 'string',
		'lat'                     => 'string',
		'lng'                     => 'string',
		'www'                     => 'string',
		'email'                   => 'string',
		'telefon'                 => 'string',
		'liczba_miejsc'           => 'integer',
		'liczba_dzieci'           => 'integer',
		'opłata_miesięczna'       => 'number',
		'opłata_godzinowa'        => 'number',
		'opłata_wyżywienie_m'     => 'number',
		'opłata_wyżywienie_d'     => 'number',
		'znizki'                  => 'string',
		'godziny_otwarcia'        => 'string',
		'dostosowany_niepelnosp'  => 'boolean',
		'zawieszona_dzialalnosc'  => 'boolean',
		'podmiot_nazwa'           => 'string',
		'podmiot_miejscowosc'     => 'string',
		'podmiot_nip'             => 'string',
		'podmiot_regon'           => 'string',
		'podmiot_www'             => 'string',
		'numer_rejestru'          => 'string',
	];

	foreach ( $meta_fields as $key => $type ) {
		register_post_meta( ZLOBKI_SLUG, $key, [
			'type'         => $type,
			'single'       => true,
			'show_in_rest' => true,
		] );
	}
} );

/* ============================================================
   5. AJAX SEARCH HANDLER
   ============================================================ */
add_action( 'wp_ajax_nopriv_zlobki_search', 'zlobki_ajax_search' );
add_action( 'wp_ajax_zlobki_search',        'zlobki_ajax_search' );

function zlobki_ajax_search() {
	check_ajax_referer( 'zlobki_search', 'nonce' );

	$args = [
		'post_type'      => ZLOBKI_SLUG,
		'post_status'    => 'publish',
		'posts_per_page' => 12,
		'paged'          => absint( $_POST['page'] ?? 1 ),
	];

	$tax_query = [];
	$meta_query = [];

	// Search text
	if ( ! empty( $_POST['search'] ) ) {
		$args['s'] = sanitize_text_field( $_POST['search'] );
	}

	// Województwo
	if ( ! empty( $_POST['wojewodztwo'] ) ) {
		$tax_query[] = [
			'taxonomy' => 'wojewodztwo',
			'field'    => 'slug',
			'terms'    => sanitize_text_field( $_POST['wojewodztwo'] ),
		];
	}

	// Typ instytucji
	if ( ! empty( $_POST['typ'] ) ) {
		$tax_query[] = [
			'taxonomy' => 'typ_instytucji',
			'field'    => 'slug',
			'terms'    => sanitize_text_field( $_POST['typ'] ),
		];
	}

	// Max price
	if ( ! empty( $_POST['max_price'] ) ) {
		$meta_query[] = [
			'key'     => 'opłata_miesięczna',
			'value'   => floatval( $_POST['max_price'] ),
			'compare' => '<=',
			'type'    => 'DECIMAL',
		];
	}

	// Dostosowany dla niepełnosprawnych
	if ( ! empty( $_POST['niepelnosprawni'] ) ) {
		$meta_query[] = [
			'key'   => 'dostosowany_niepelnosp',
			'value' => '1',
		];
	}

	// Wolne miejsca
	if ( ! empty( $_POST['wolne_miejsca'] ) ) {
		$meta_query[] = [
			'key'     => 'liczba_miejsccalc',
			'compare' => 'EXISTS',
		];
	}

	if ( $tax_query ) {
		$args['tax_query'] = array_merge( [ 'relation' => 'AND' ], $tax_query );
	}
	if ( $meta_query ) {
		$args['meta_query'] = array_merge( [ 'relation' => 'AND' ], $meta_query );
	}

	// Sorting
	$sort = sanitize_text_field( $_POST['sort'] ?? 'title' );
	switch ( $sort ) {
		case 'price_asc':
			$args['meta_key']  = 'opłata_miesięczna';
			$args['orderby']   = 'meta_value_num';
			$args['order']     = 'ASC';
			break;
		case 'price_desc':
			$args['meta_key']  = 'opłata_miesięczna';
			$args['orderby']   = 'meta_value_num';
			$args['order']     = 'DESC';
			break;
		case 'capacity':
			$args['meta_key']  = 'liczba_miejsc';
			$args['orderby']   = 'meta_value_num';
			$args['order']     = 'DESC';
			break;
		default:
			$args['orderby'] = 'title';
			$args['order']   = 'ASC';
	}

	$query = new WP_Query( $args );

	ob_start();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'template-parts/card', 'nursery' );
		}
	} else {
		echo '<div class="empty-state"><div class="empty-state__icon">🔍</div><h3>Nie znaleziono instytucji</h3><p>Spróbuj zmienić kryteria wyszukiwania.</p></div>';
	}
	$html = ob_get_clean();
	wp_reset_postdata();

	wp_send_json_success( [
		'html'      => $html,
		'found'     => $query->found_posts,
		'max_pages' => $query->max_num_pages,
		'paged'     => $args['paged'],
	] );
}

/* ============================================================
   6. TEMPLATE OVERRIDE — SINGLE ZLOBEK
   ============================================================ */
add_filter( 'single_template', function ( $template ) {
	global $post;
	if ( $post && $post->post_type === ZLOBKI_SLUG ) {
		$custom = ZLOBKI_DIR . '/single-zlobek.php';
		if ( file_exists( $custom ) ) {
			return $custom;
		}
	}
	return $template;
} );

add_filter( 'archive_template', function ( $template ) {
	if ( is_post_type_archive( ZLOBKI_SLUG ) || is_tax( [ 'wojewodztwo', 'typ_instytucji', 'powiat' ] ) ) {
		$custom = ZLOBKI_DIR . '/archive-zlobek.php';
		if ( file_exists( $custom ) ) {
			return $custom;
		}
	}
	return $template;
} );

/* ============================================================
   7. HELPER FUNCTIONS
   ============================================================ */

/**
 * Poprawna wersja ucwords() dla języka polskiego (multibyte-safe).
 *
 * PHP's ucfirst( strtolower() ) nie obsługuje polskich znaków UTF-8:
 * strtolower("ŁÓDZKIE") daje "Łódzkie" (Ł zostaje), a ucfirst operuje
 * na bajtach, więc "ł" → "Ł" nie działa. Rozwiązanie: mb_ funkcje.
 *
 * @param string $str  np. "KUJAWSKO-POMORSKIE" lub "ŁÓDZKIE"
 * @return string      np. "Kujawsko-Pomorskie" lub "Łódzkie"
 */
function zlobki_ucwords_pl( string $str ): string {
	$str = mb_strtolower( $str, 'UTF-8' );
	return preg_replace_callback(
		'/(?:^|[\s\-])./u',
		fn( $m ) => mb_strtoupper( $m[0], 'UTF-8' ),
		$str
	);
}

/**
 * Get a formatted full address for a nursery post.
 */
function zlobki_get_address( int $post_id ): string {
	$parts = array_filter( [
		get_post_meta( $post_id, 'ulica', true )
			? trim( get_post_meta( $post_id, 'ulica', true ) . ' ' . get_post_meta( $post_id, 'nr_domu', true ) )
			: '',
		get_post_meta( $post_id, 'miejscowosc', true ),
		get_post_meta( $post_id, 'kod_pocztowy', true ),
	] );
	return implode( ', ', $parts );
}

/**
 * Format price in PLN.
 */
function zlobki_format_price( $value ): string {
	if ( $value === '' || $value === false || $value === null ) {
		return '—';
	}
	return number_format( (float) $value, 0, ',', ' ' ) . ' zł';
}

/**
 * Return availability percentage.
 */
function zlobki_availability( int $post_id ): int {
	$places   = (int) get_post_meta( $post_id, 'liczba_miejsc', true );
	$enrolled = (int) get_post_meta( $post_id, 'liczba_dzieci', true );
	if ( $places <= 0 ) return 0;
	return min( 100, (int) round( $enrolled / $places * 100 ) );
}

/**
 * Parse geolocation string "lat;lng".
 */
function zlobki_parse_geo( string $geo ): array {
	$parts = explode( ';', $geo );
	if ( count( $parts ) === 2 ) {
		return [ 'lat' => trim( $parts[1] ), 'lng' => trim( $parts[0] ) ];
	}
	return [ 'lat' => '', 'lng' => '' ];
}

/**
 * Sanitize and return working hours nicely.
 */
function zlobki_format_hours( string $raw ): string {
	$clean = str_replace( 'Godziny pracy: ', '', $raw );
	return esc_html( trim( $clean ) );
}

/**
 * Return nursery icon emoji based on type.
 */
function zlobki_type_icon( string $type ): string {
	return $type === 'Klub dziecięcy' ? '🎯' : '🍼';
}

/* ============================================================
   8. TITLE FILTER
   ============================================================ */
add_filter( 'wp_title', function ( $title ) {
	return $title . get_bloginfo( 'name' );
} );

/* ============================================================
   9. FLUSH REWRITE ON ACTIVATION
   ============================================================ */
add_action( 'after_switch_theme', function () {
	flush_rewrite_rules();
} );

/* ============================================================
   10. ADD ADMIN COLUMNS FOR ZLOBEK CPT
   ============================================================ */
add_filter( 'manage_' . ZLOBKI_SLUG . '_posts_columns', function ( $cols ) {
	$cols['typ']      = __( 'Typ', 'zlobki-polska' );
	$cols['woj']      = __( 'Województwo', 'zlobki-polska' );
	$cols['miejsca']  = __( 'Miejsca', 'zlobki-polska' );
	$cols['oplata']   = __( 'Opłata/mc', 'zlobki-polska' );
	return $cols;
} );

add_action( 'manage_' . ZLOBKI_SLUG . '_posts_custom_column', function ( $col, $post_id ) {
	switch ( $col ) {
		case 'typ':
			echo esc_html( get_post_meta( $post_id, 'typ_instytucji_label', true ) );
			break;
		case 'woj':
			echo esc_html( get_post_meta( $post_id, 'wojewodztwo_label', true ) );
			break;
		case 'miejsca':
			$m = get_post_meta( $post_id, 'liczba_miejsc', true );
			$d = get_post_meta( $post_id, 'liczba_dzieci', true );
			echo esc_html( $m ) . ' (' . esc_html( $d ) . ' zapisanych)';
			break;
		case 'oplata':
			echo esc_html( zlobki_format_price( get_post_meta( $post_id, 'opłata_miesięczna', true ) ) );
			break;
	}
}, 10, 2 );

/* ============================================================
   11. CUSTOMIZER — HERO
   Wygląd → Dostosuj → Sekcja Hero
   ============================================================ */
add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ) {

	// ── Sekcja ──────────────────────────────────────────────
	$wp_customize->add_section( 'zlobki_hero', [
		'title'       => '🏠 Sekcja Hero (strona główna)',
		'description' => 'Edytuj teksty widoczne w głównym boksie na stronie głównej.',
		'priority'    => 30,
	] );

	// ── Helper: zarejestruj pole ─────────────────────────────
	$add = function (
		string $id,
		string $label,
		string $default,
		string $type = 'text',
		int $priority = 10
	) use ( $wp_customize ) {
		$wp_customize->add_setting( $id, [
			'default'           => $default,
			'sanitize_callback' => $type === 'textarea' ? 'wp_kses_post' : 'sanitize_text_field',
			'transport'         => 'postMessage', // podgląd na żywo bez przeładowania
		] );
		$wp_customize->add_control( $id, [
			'label'    => $label,
			'section'  => 'zlobki_hero',
			'type'     => $type,
			'priority' => $priority,
		] );
	};

	// ── Pola ────────────────────────────────────────────────
	$add( 'zlobki_hero_badge',       '🏷️ Tekst odznaki (nad tytułem)',   '🇵🇱 Oficjalny rejestr MRiPS',                   'text',     10 );
	$add( 'zlobki_hero_title',       '✏️ Tytuł (pierwsza linia)',         'Znajdź idealny żłobek',                          'text',     20 );
	$add( 'zlobki_hero_title_em',    '✏️ Tytuł (wyróżnione słowo/fraza)', 'maluszka',                                       'text',     30 );
	$add( 'zlobki_hero_subtitle',    '📝 Podtytuł / opis',                'Przeszukaj bazę żłobków i klubów dziecięcych w całej Polsce. Sprawdź ceny, dostępność miejsc i dane kontaktowe.', 'textarea', 40 );
	$add( 'zlobki_hero_btn',         '🔍 Tekst przycisku Szukaj',         '🔍 Szukaj',                                      'text',     50 );
	$add( 'zlobki_hero_search_ph',   '🔤 Placeholder pola wyszukiwania',  'np. Warszawa, Kraków, Żłobek Słoneczko...',      'text',     60 );
	$add( 'zlobki_hero_chip_np',     '♿ Tekst chipa: dla niepełnosp.',   '♿ Dla niepełnosprawnych',                        'text',     70 );
	$add( 'zlobki_hero_chip_klub',   '🎯 Tekst chipa: kluby',             '🎯 Tylko kluby dziecięce',                       'text',     80 );
	$add( 'zlobki_hero_chip_zlobek', '🍼 Tekst chipa: żłobki',            '🍼 Tylko żłobki',                                'text',     90 );

} );

// ── Live preview (postMessage) — aktualizuje DOM bez przeładowania ──
add_action( 'customize_preview_init', function () {
	wp_add_inline_script( 'customize-preview', "
(function($, api) {
	var bind = function(id, selector, attr) {
		api(id, function(setting) {
			setting.bind(function(val) {
				if (attr === 'html') $(selector).html(val);
				else if (attr === 'placeholder') $(selector).attr('placeholder', val);
				else $(selector).text(val);
			});
		});
	};
	bind('zlobki_hero_badge',       '.hero__badge',         'text');
	bind('zlobki_hero_title',       '.hero h1',             'html');
	bind('zlobki_hero_title_em',    '.hero h1 em',          'text');
	bind('zlobki_hero_subtitle',    '.hero__subtitle',      'html');
	bind('zlobki_hero_btn',         '#hero-search-btn',     'text');
	bind('zlobki_hero_search_ph',   '#hero-search',         'placeholder');
	bind('zlobki_hero_chip_np',     '#chip-niepelnosp',     'text');
	bind('zlobki_hero_chip_klub',   '#chip-typ-klub',       'text');
	bind('zlobki_hero_chip_zlobek', '#chip-typ-zlobek',     'text');
})(jQuery, wp.customize);
" );
} );

/* ============================================================
   12. SEO — Schema.org JSON-LD
   Rank Math obsługuje meta/og/twitter/sitemap/canonical.
   My dodajemy tylko to czego Rank Math nie generuje:
   - ChildCare schema na stronach placówek
   - BreadcrumbList na archiwum i stronach taksonomii
   - WebSite + SearchAction na stronie głównej
   ============================================================ */

/**
 * Schema: WebSite + SearchAction — tylko strona główna.
 * Umożliwia Google wyświetlenie pola wyszukiwania w wynikach (Sitelinks Searchbox).
 */
add_action( 'wp_head', function () {
	if ( ! is_front_page() ) return;
	$archive = get_post_type_archive_link( ZLOBKI_SLUG );
	$schema  = [
		'@context'        => 'https://schema.org',
		'@type'           => 'WebSite',
		'name'            => get_bloginfo( 'name' ),
		'url'             => home_url( '/' ),
		'description'     => get_bloginfo( 'description' ),
		'potentialAction' => [
			'@type'       => 'SearchAction',
			'target'      => [
				'@type'       => 'EntryPoint',
				'urlTemplate' => $archive . '?s={search_term_string}',
			],
			'query-input' => 'required name=search_term_string',
		],
	];
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}, 5 );

/**
 * Schema: ChildCare (lub ChildCareService) — strony pojedynczych placówek.
 * Rank Math generuje ogólny "Article" — my nadpisujemy właściwym typem.
 */
add_action( 'wp_head', function () {
	if ( ! is_singular( ZLOBKI_SLUG ) ) return;

	$id       = get_the_ID();
	$typ      = get_post_meta( $id, 'typ_instytucji_label', true );
	$geo_raw  = get_post_meta( $id, 'geolokalizacja', true );
	$geo      = zlobki_parse_geo( (string) $geo_raw );
	$places   = (int) get_post_meta( $id, 'liczba_miejsc', true );
	$price    = (float) get_post_meta( $id, 'opłata_miesięczna', true );
	$tel      = get_post_meta( $id, 'telefon', true );
	$email    = get_post_meta( $id, 'email', true );
	$www      = get_post_meta( $id, 'www', true );
	$ulica    = get_post_meta( $id, 'ulica', true );
	$nr       = get_post_meta( $id, 'nr_domu', true );
	$kod      = get_post_meta( $id, 'kod_pocztowy', true );
	$miasto   = get_post_meta( $id, 'miejscowosc', true );

	// Województwo z taksonomii
	$woj_terms = get_the_terms( $id, 'wojewodztwo' );
	$woj_name  = ( $woj_terms && ! is_wp_error( $woj_terms ) )
		? zlobki_ucwords_pl( $woj_terms[0]->name )
		: '';

	$schema = [
		'@context' => 'https://schema.org',
		'@type'    => 'ChildCare',
		'name'     => get_the_title(),
		'url'      => get_permalink(),
		'address'  => array_filter( [
			'@type'           => 'PostalAddress',
			'streetAddress'   => trim( $ulica . ' ' . $nr ),
			'addressLocality' => $miasto,
			'postalCode'      => $kod,
			'addressRegion'   => $woj_name,
			'addressCountry'  => 'PL',
		] ),
	];

	// Geo
	if ( ! empty( $geo['lat'] ) && ! empty( $geo['lng'] ) ) {
		$schema['geo'] = [
			'@type'     => 'GeoCoordinates',
			'latitude'  => (float) $geo['lat'],
			'longitude' => (float) $geo['lng'],
		];
	}

	// Kontakt
	if ( $tel )   { $schema['telephone'] = preg_replace( '/\s+/', '', $tel ); }
	if ( $email ) { $schema['email']     = sanitize_email( $email ); }
	if ( $www )   { $schema['sameAs']    = ( strpos( $www, 'http' ) === 0 ? $www : 'https://' . $www ); }

	// Liczba miejsc
	if ( $places > 0 ) {
		$schema['numberOfRooms'] = $places; // przybliżenie — brak lepszego pola w ChildCare
	}

	// Cena — oferta
	if ( $price > 0 ) {
		$schema['offers'] = [
			'@type'         => 'Offer',
			'price'         => $price,
			'priceCurrency' => 'PLN',
			'priceSpecification' => [
				'@type'           => 'UnitPriceSpecification',
				'price'           => $price,
				'priceCurrency'   => 'PLN',
				'referenceQuantity' => [
					'@type'    => 'QuantitativeValue',
					'value'    => 1,
					'unitCode' => 'MON', // miesięcznie
				],
			],
		];
	}

	// Typ placówki jako opis
	if ( $typ ) { $schema['description'] = $typ . ' — ' . $miasto . ', woj. ' . $woj_name; }

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}, 5 );

/**
 * Schema: BreadcrumbList — archiwum, taksonomie, strony statyczne.
 * Rank Math generuje breadcrumbs tylko dla postów — my pokrywamy resztę.
 */
add_action( 'wp_head', function () {
	// Nie duplikuj na single placówki (Rank Math to ogarnie)
	if ( is_singular() ) return;

	$items   = [];
	$pos     = 1;

	// Zawsze: strona główna
	$items[] = [
		'@type'    => 'ListItem',
		'position' => $pos++,
		'name'     => get_bloginfo( 'name' ),
		'item'     => home_url( '/' ),
	];

	$archive_url = get_post_type_archive_link( ZLOBKI_SLUG );

	if ( is_post_type_archive( ZLOBKI_SLUG ) ) {
		$items[] = [
			'@type'    => 'ListItem',
			'position' => $pos++,
			'name'     => 'Wyszukiwarka żłobków',
			'item'     => $archive_url,
		];
	} elseif ( is_tax() ) {
		$items[] = [
			'@type'    => 'ListItem',
			'position' => $pos++,
			'name'     => 'Wyszukiwarka żłobków',
			'item'     => $archive_url,
		];
		$term    = get_queried_object();
		$items[] = [
			'@type'    => 'ListItem',
			'position' => $pos++,
			'name'     => zlobki_ucwords_pl( $term->name ),
			'item'     => get_term_link( $term ),
		];
	} elseif ( is_page() ) {
		$ancestors = array_reverse( get_ancestors( get_the_ID(), 'page' ) );
		foreach ( $ancestors as $anc_id ) {
			$items[] = [
				'@type'    => 'ListItem',
				'position' => $pos++,
				'name'     => get_the_title( $anc_id ),
				'item'     => get_permalink( $anc_id ),
			];
		}
		$items[] = [
			'@type'    => 'ListItem',
			'position' => $pos++,
			'name'     => get_the_title(),
			'item'     => get_permalink(),
		];
	} else {
		return; // Inne konteksty — Rank Math poradzi sobie
	}

	if ( count( $items ) < 2 ) return;

	$schema = [
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $items,
	];

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}, 5 );

/**
 * Rejestracja CPT i taksonomii w Rank Math — żeby Rank Math wiedział
 * o naszym custom post type i mógł zarządzać jego meta/sitemap.
 */
add_filter( 'rank_math/sitemap/post_type', function ( $post_types ) {
	$post_types[] = ZLOBKI_SLUG;
	return $post_types;
} );

add_filter( 'rank_math/sitemap/taxonomies', function ( $taxonomies ) {
	$taxonomies[] = 'wojewodztwo';
	$taxonomies[] = 'typ_instytucji';
	$taxonomies[] = 'powiat';
	return $taxonomies;
} );

/**
 * Podpowiedź dla Rank Math: domyślny title pattern dla placówek.
 * Rank Math użyje tego jako punkt startowy w swoim panelu.
 */
add_filter( 'rank_math/titles/pt_' . ZLOBKI_SLUG . '_title', function () {
	return '%title% — %term% | %sitename%';
} );

add_filter( 'rank_math/titles/pt_' . ZLOBKI_SLUG . '_description', function () {
	return 'Sprawdź szczegóły, godziny otwarcia, ceny i dostępność miejsc. Oficjalne dane z rejestru MRiPS.';
} );
