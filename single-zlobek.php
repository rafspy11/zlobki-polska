<?php
/**
 * Single Nursery Template
 *
 * @package ZlobkiPolska
 */

get_header();
the_post();

$post_id     = get_the_ID();
$typ         = get_post_meta( $post_id, 'typ_instytucji_label', true );
$woj         = get_post_meta( $post_id, 'wojewodztwo_label', true );
$powiat      = get_post_meta( $post_id, 'powiat_label', true );
$gmina       = get_post_meta( $post_id, 'gmina', true );
$miejscowosc = get_post_meta( $post_id, 'miejscowosc', true );
$ulica       = get_post_meta( $post_id, 'ulica', true );
$nr_domu     = get_post_meta( $post_id, 'nr_domu', true );
$nr_lok      = get_post_meta( $post_id, 'nr_lokalu', true );
$kod         = get_post_meta( $post_id, 'kod_pocztowy', true );
$lat         = get_post_meta( $post_id, 'lat', true );
$lng         = get_post_meta( $post_id, 'lng', true );
$www         = get_post_meta( $post_id, 'www', true );
$email       = get_post_meta( $post_id, 'email', true );
$phone       = get_post_meta( $post_id, 'telefon', true );
$places      = (int) get_post_meta( $post_id, 'liczba_miejsc', true );
$enrolled    = (int) get_post_meta( $post_id, 'liczba_dzieci', true );
$price_m     = get_post_meta( $post_id, 'opłata_miesięczna', true );
$price_h     = get_post_meta( $post_id, 'opłata_godzinowa', true );
$price_food_m= get_post_meta( $post_id, 'opłata_wyżywienie_m', true );
$price_food_d= get_post_meta( $post_id, 'opłata_wyżywienie_d', true );
$znizki      = get_post_meta( $post_id, 'znizki', true );
$godziny     = get_post_meta( $post_id, 'godziny_otwarcia', true );
$niepelnosp  = get_post_meta( $post_id, 'dostosowany_niepelnosp', true );
$zawieszona  = get_post_meta( $post_id, 'zawieszona_dzialalnosc', true );
$podmiot     = get_post_meta( $post_id, 'podmiot_nazwa', true );
$podmiot_m   = get_post_meta( $post_id, 'podmiot_miejscowosc', true );
$nip         = get_post_meta( $post_id, 'podmiot_nip', true );
$regon       = get_post_meta( $post_id, 'podmiot_regon', true );
$podmiot_www = get_post_meta( $post_id, 'podmiot_www', true );
$rej         = get_post_meta( $post_id, 'numer_rejestru', true );
$zlobek_id   = get_post_meta( $post_id, 'zlobek_id', true );

$free        = max( 0, $places - $enrolled );
$avail_pct   = zlobki_availability( $post_id );
$address     = zlobki_get_address( $post_id );
$icon        = zlobki_type_icon( $typ );
$is_klub     = ( $typ === 'Klub dziecięcy' );
?>

<main class="site-main" role="main">
<div class="single-nursery">
<div class="container">

	<a href="<?php echo esc_url( get_post_type_archive_link( 'zlobek' ) ); ?>" class="single-nursery__back">
		← Powrót do listy
	</a>

	<?php if ( $zawieszona ) : ?>
		<div class="notice notice--warning">
			⚠️ <strong>Uwaga:</strong> Podmiot prowadzący zawiesił działalność tej instytucji.
		</div>
	<?php endif; ?>

	<div class="single-nursery__grid">

		<!-- ==================== MAIN CONTENT ==================== -->
		<div class="single-nursery__main">

			<!-- Hero block -->
			<div class="single-nursery__hero">
				<div class="single-nursery__hero-type">
					<?php echo $icon; ?> <?php echo esc_html( $typ ?: 'Żłobek' ); ?>
					<?php if ( $niepelnosp ) echo ' &nbsp;♿ Dostosowany dla niepełnosprawnych'; ?>
				</div>
				<h1><?php the_title(); ?></h1>
				<div class="single-nursery__hero-address">
					📍 <?php echo esc_html( implode( ', ', array_filter( [ $ulica ? trim( $ulica . ' ' . $nr_domu ) : '', $miejscowosc, $kod ] ) ) ); ?>
				</div>
			</div>

			<!-- Availability -->
			<?php if ( $places > 0 ) : ?>
			<div class="info-block">
				<div class="info-block__title">📊 Dostępność miejsc</div>
				<div class="info-grid">
					<div class="info-item">
						<span class="info-item__label">Łączna liczba miejsc</span>
						<span class="info-item__value info-item__value--primary"><?php echo esc_html( $places ); ?></span>
					</div>
					<div class="info-item">
						<span class="info-item__label">Dzieci zapisanych</span>
						<span class="info-item__value"><?php echo esc_html( $enrolled ); ?></span>
					</div>
					<div class="info-item">
						<span class="info-item__label">Wolne miejsca</span>
						<span class="info-item__value" style="color: <?php echo $free > 0 ? 'var(--color-success)' : 'var(--color-accent)'; ?>">
							<?php echo esc_html( $free ); ?>
						</span>
					</div>
					<div class="info-item">
						<span class="info-item__label">Zapełnienie</span>
						<span class="info-item__value"><?php echo esc_html( $avail_pct ); ?>%</span>
					</div>
				</div>
				<div class="availability-bar" style="margin-top: 1rem; height: 8px;">
					<div class="availability-bar__fill" style="width: <?php echo esc_attr( $avail_pct ); ?>%"></div>
				</div>
			</div>
			<?php endif; ?>

			<!-- Prices -->
			<div class="info-block">
				<div class="info-block__title">💰 Opłaty</div>
				<div class="info-grid">
					<div class="info-item">
						<span class="info-item__label">Opłata miesięczna</span>
						<span class="info-item__value info-item__value--accent"><?php echo esc_html( zlobki_format_price( $price_m ) ); ?></span>
					</div>
					<?php if ( $price_h ) : ?>
					<div class="info-item">
						<span class="info-item__label">Opłata godzinowa (>10h)</span>
						<span class="info-item__value"><?php echo esc_html( zlobki_format_price( $price_h ) ); ?>/h</span>
					</div>
					<?php endif; ?>
					<?php if ( $price_food_m ) : ?>
					<div class="info-item">
						<span class="info-item__label">Wyżywienie miesięcznie</span>
						<span class="info-item__value"><?php echo esc_html( zlobki_format_price( $price_food_m ) ); ?></span>
					</div>
					<?php endif; ?>
					<?php if ( $price_food_d ) : ?>
					<div class="info-item">
						<span class="info-item__label">Wyżywienie dziennie</span>
						<span class="info-item__value"><?php echo esc_html( zlobki_format_price( $price_food_d ) ); ?></span>
					</div>
					<?php endif; ?>
				</div>
				<?php if ( $znizki ) : ?>
					<div style="margin-top: 1rem; padding: 0.85rem 1rem; background: rgba(76,175,118,.08); border-radius: var(--radius-sm); font-size: 0.88rem;">
						<strong>🏷️ Dostępne zniżki:</strong><br>
						<?php echo nl2br( esc_html( $znizki ) ); ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- Hours -->
			<?php if ( $godziny ) : ?>
			<div class="info-block">
				<div class="info-block__title">🕐 Godziny otwarcia</div>
				<div style="font-size: 0.95rem; font-weight: 500; color: var(--color-text);">
					<?php echo esc_html( zlobki_format_hours( $godziny ) ); ?>
				</div>
			</div>
			<?php endif; ?>

			<!-- Location details -->
			<div class="info-block">
				<div class="info-block__title">📍 Lokalizacja</div>
				<div class="info-grid">
					<div class="info-item">
						<span class="info-item__label">Województwo</span>
						<span class="info-item__value"><?php echo esc_html( zlobki_ucwords_pl( $woj ) ); ?></span>
					</div>
					<div class="info-item">
						<span class="info-item__label">Powiat</span>
						<span class="info-item__value"><?php echo esc_html( $powiat ); ?></span>
					</div>
					<div class="info-item">
						<span class="info-item__label">Gmina</span>
						<span class="info-item__value"><?php echo esc_html( $gmina ); ?></span>
					</div>
					<div class="info-item">
						<span class="info-item__label">Miejscowość</span>
						<span class="info-item__value"><?php echo esc_html( $miejscowosc ); ?></span>
					</div>
					<div class="info-item" style="grid-column: span 2;">
						<span class="info-item__label">Adres</span>
						<span class="info-item__value">
							<?php echo esc_html( trim( ( $ulica ? $ulica . ' ' : '' ) . $nr_domu . ( $nr_lok ? '/' . $nr_lok : '' ) ) ); ?>,
							<?php echo esc_html( $kod . ' ' . $miejscowosc ); ?>
						</span>
					</div>
				</div>
			</div>

			<!-- Operator info -->
			<?php if ( $podmiot ) : ?>
			<div class="info-block">
				<div class="info-block__title">🏢 Podmiot prowadzący</div>
				<div class="info-grid">
					<div class="info-item" style="grid-column: span 2;">
						<span class="info-item__label">Nazwa</span>
						<span class="info-item__value" style="font-size: 0.95rem;"><?php echo esc_html( $podmiot ); ?></span>
					</div>
					<?php if ( $nip ) : ?>
					<div class="info-item">
						<span class="info-item__label">NIP</span>
						<span class="info-item__value"><?php echo esc_html( $nip ); ?></span>
					</div>
					<?php endif; ?>
					<?php if ( $regon ) : ?>
					<div class="info-item">
						<span class="info-item__label">REGON</span>
						<span class="info-item__value"><?php echo esc_html( $regon ); ?></span>
					</div>
					<?php endif; ?>
					<?php if ( $rej ) : ?>
					<div class="info-item">
						<span class="info-item__label">Nr w rejestrze</span>
						<span class="info-item__value"><?php echo esc_html( $rej ); ?></span>
					</div>
					<?php endif; ?>
				</div>
				<?php if ( $podmiot_www ) : ?>
					<div style="margin-top: 0.75rem;">
						<a href="<?php echo esc_url( strpos( $podmiot_www, 'http' ) === 0 ? $podmiot_www : 'https://' . $podmiot_www ); ?>"
						   target="_blank" rel="noopener noreferrer"
						   class="btn--details">🌐 Strona podmiotu prowadzącego</a>
					</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>

		</div>

		<!-- ==================== SIDEBAR ==================== -->
		<aside class="single-nursery__sidebar">

			<!-- Map -->
			<?php if ( $lat && $lng ) : ?>
			<div class="map-container" style="margin-bottom: 1.25rem;">
				<iframe
					loading="lazy"
					allowfullscreen
					referrerpolicy="no-referrer-when-downgrade"
					src="https://maps.google.com/maps?q=<?php echo esc_attr( $lat ); ?>,<?php echo esc_attr( $lng ); ?>&z=15&output=embed"
					title="<?php esc_attr_e( 'Mapa lokalizacji', 'zlobki-polska' ); ?>">
				</iframe>
			</div>
			<?php endif; ?>

			<!-- Contact -->
			<div class="sidebar-card">
				<div class="sidebar-card__title">📞 Kontakt</div>
				<div class="contact-list">
					<?php if ( $phone ) : ?>
					<div class="contact-item">
						<div class="contact-item__icon">📞</div>
						<div class="contact-item__text">
							<span class="contact-item__label">Telefon</span>
							<a href="tel:<?php echo esc_attr( preg_replace('/\s+/', '', $phone ) ); ?>" class="contact-item__val">
								<?php echo esc_html( $phone ); ?>
							</a>
						</div>
					</div>
					<?php endif; ?>
					<?php if ( $email ) : ?>
					<div class="contact-item">
						<div class="contact-item__icon">✉️</div>
						<div class="contact-item__text">
							<span class="contact-item__label">E-mail</span>
							<a href="mailto:<?php echo esc_attr( $email ); ?>" class="contact-item__val">
								<?php echo esc_html( $email ); ?>
							</a>
						</div>
					</div>
					<?php endif; ?>
					<?php if ( $www ) : ?>
					<div class="contact-item">
						<div class="contact-item__icon">🌐</div>
						<div class="contact-item__text">
							<span class="contact-item__label">Strona WWW</span>
							<a href="<?php echo esc_url( strpos( $www, 'http' ) === 0 ? $www : 'https://' . $www ); ?>"
							   target="_blank" rel="noopener noreferrer" class="contact-item__val">
								<?php echo esc_html( $www ); ?>
							</a>
						</div>
					</div>
					<?php endif; ?>
					<?php if ( $address ) : ?>
					<div class="contact-item">
						<div class="contact-item__icon">📍</div>
						<div class="contact-item__text">
							<span class="contact-item__label">Adres</span>
							<span class="contact-item__val"><?php echo esc_html( $address ); ?></span>
						</div>
					</div>
					<?php endif; ?>
				</div>

				<?php if ( $lat && $lng ) : ?>
				<div style="margin-top: 1rem;">
					<a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo esc_attr( $lat ); ?>,<?php echo esc_attr( $lng ); ?>"
					   target="_blank" rel="noopener noreferrer"
					   class="search-box__btn" style="width: 100%; justify-content: center; display: flex;">
						🗺️ Wyznacz trasę
					</a>
				</div>
				<?php endif; ?>
			</div>

			<!-- Quick info -->
			<div class="sidebar-card">
				<div class="sidebar-card__title">ℹ️ Skrót informacji</div>
				<div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.88rem;">
					<div style="display: flex; justify-content: space-between;">
						<span style="color: var(--color-text-muted); font-weight: 600;">Typ:</span>
						<span style="font-weight: 700;"><?php echo esc_html( $typ ?: '—' ); ?></span>
					</div>
					<div style="display: flex; justify-content: space-between;">
						<span style="color: var(--color-text-muted); font-weight: 600;">Miejsca:</span>
						<span style="font-weight: 700;"><?php echo $places > 0 ? esc_html( $places ) : '—'; ?></span>
					</div>
					<div style="display: flex; justify-content: space-between;">
						<span style="color: var(--color-text-muted); font-weight: 600;">Opłata/mc:</span>
						<span style="font-weight: 700; color: var(--color-accent);"><?php echo esc_html( zlobki_format_price( $price_m ) ); ?></span>
					</div>
					<div style="display: flex; justify-content: space-between;">
						<span style="color: var(--color-text-muted); font-weight: 600;">Dla niepełnosp.:</span>
						<span style="font-weight: 700; color: <?php echo $niepelnosp ? 'var(--color-success)' : 'var(--color-text-muted)'; ?>">
							<?php echo $niepelnosp ? '✅ Tak' : '❌ Nie'; ?>
						</span>
					</div>
					<?php if ( $zlobek_id ) : ?>
					<div style="display: flex; justify-content: space-between;">
						<span style="color: var(--color-text-muted); font-weight: 600;">ID rejestru:</span>
						<span style="font-weight: 700; font-size: 0.82rem;"><?php echo esc_html( $zlobek_id ); ?></span>
					</div>
					<?php endif; ?>
				</div>
			</div>

			<!-- Taxonomy links -->
			<div class="sidebar-card">
				<div class="sidebar-card__title">🔗 Powiązane</div>
				<?php $woj_terms = get_the_terms( $post_id, 'wojewodztwo' ); ?>
				<?php if ( $woj_terms && ! is_wp_error( $woj_terms ) ) : ?>
					<?php foreach ( $woj_terms as $t ) : ?>
					<a href="<?php echo esc_url( get_term_link( $t ) ); ?>"
					   style="display: inline-block; margin: 0.25rem 0.25rem 0 0; padding: 0.35rem 0.85rem; background: var(--color-bg-alt); border-radius: 2rem; font-size: 0.8rem; font-weight: 600; color: var(--color-primary);">
						📍 <?php echo esc_html( zlobki_ucwords_pl( $t->name ) ); ?>
					</a>
					<?php endforeach; ?>
				<?php endif; ?>
				<?php $typ_terms = get_the_terms( $post_id, 'typ_instytucji' ); ?>
				<?php if ( $typ_terms && ! is_wp_error( $typ_terms ) ) : ?>
					<?php foreach ( $typ_terms as $t ) : ?>
					<a href="<?php echo esc_url( get_term_link( $t ) ); ?>"
					   style="display: inline-block; margin: 0.25rem 0.25rem 0 0; padding: 0.35rem 0.85rem; background: rgba(244,132,95,.1); border-radius: 2rem; font-size: 0.8rem; font-weight: 600; color: var(--color-accent);">
						<?php echo esc_html( $t->name ); ?>
					</a>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>

		</aside>
	</div>
</div>
</div>
</main>

<?php get_footer(); ?>
