<?php
/**
 * Template Part: Nursery Card
 *
 * @package ZlobkiPolska
 */

$post_id    = get_the_ID();
$typ        = get_post_meta( $post_id, 'typ_instytucji_label', true );
$woj        = get_post_meta( $post_id, 'wojewodztwo_label', true );
$miejscowosc = get_post_meta( $post_id, 'miejscowosc', true );
$powiat     = get_post_meta( $post_id, 'powiat_label', true );
$places     = (int) get_post_meta( $post_id, 'liczba_miejsc', true );
$enrolled   = (int) get_post_meta( $post_id, 'liczba_dzieci', true );
$price_m    = get_post_meta( $post_id, 'opłata_miesięczna', true );
$www        = get_post_meta( $post_id, 'www', true );
$email      = get_post_meta( $post_id, 'email', true );
$phone      = get_post_meta( $post_id, 'telefon', true );
$niepelnosp = get_post_meta( $post_id, 'dostosowany_niepelnosp', true );
$zawieszona = get_post_meta( $post_id, 'zawieszona_dzialalnosc', true );
$icon       = zlobki_type_icon( $typ );
$free       = max( 0, $places - $enrolled );
$avail_pct  = zlobki_availability( $post_id );
$is_klub    = ( $typ === 'Klub dziecięcy' );
?>

<article class="nursery-card" data-id="<?php echo esc_attr( $post_id ); ?>">

	<div class="nursery-card__header">
		<div class="nursery-card__icon" aria-hidden="true"><?php echo $icon; ?></div>
		<div class="nursery-card__badges">
			<span class="badge <?php echo $is_klub ? 'badge--klub' : 'badge--zlobek'; ?>">
				<?php echo esc_html( $typ ?: 'Żłobek' ); ?>
			</span>
			<?php if ( $niepelnosp ) : ?>
				<span class="badge badge--niepelnosprawni" title="<?php esc_attr_e( 'Dostosowany dla dzieci niepełnosprawnych', 'zlobki-polska' ); ?>">
					♿ Dostępny
				</span>
			<?php endif; ?>
			<?php if ( $zawieszona ) : ?>
				<span class="badge badge--zawieszona"><?php esc_html_e( 'Zawieszona', 'zlobki-polska' ); ?></span>
			<?php endif; ?>
		</div>
	</div>

	<div class="nursery-card__body">
		<h3 class="nursery-card__name">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

		<div class="nursery-card__location">
			📍
			<?php echo esc_html( implode( ', ', array_filter( [ $miejscowosc, $powiat, zlobki_ucwords_pl( $woj ) ] ) ) ); ?>
		</div>

		<div class="nursery-card__meta">
			<div class="nursery-card__meta-item">
				<span class="nursery-card__meta-label">Miejsc</span>
				<span class="nursery-card__meta-value"><?php echo $places > 0 ? esc_html( $places ) : '—'; ?></span>
			</div>
			<div class="nursery-card__meta-item">
				<span class="nursery-card__meta-label">Wolnych</span>
				<span class="nursery-card__meta-value <?php echo $free > 0 ? 'nursery-card__meta-value--available' : ''; ?>">
					<?php echo $places > 0 ? esc_html( $free ) : '—'; ?>
				</span>
			</div>
			<div class="nursery-card__meta-item">
				<span class="nursery-card__meta-label">Opłata/mc</span>
				<span class="nursery-card__meta-value nursery-card__meta-value--price">
					<?php echo esc_html( zlobki_format_price( $price_m ) ); ?>
				</span>
			</div>
			<div class="nursery-card__meta-item">
				<span class="nursery-card__meta-label">Zapełnienie</span>
				<span class="nursery-card__meta-value"><?php echo $places > 0 ? esc_html( $avail_pct ) . '%' : '—'; ?></span>
			</div>
		</div>

		<?php if ( $places > 0 ) : ?>
			<div class="availability-bar" aria-label="<?php printf( esc_attr__( 'Zapełnienie %d%%', 'zlobki-polska' ), $avail_pct ); ?>">
				<div class="availability-bar__fill" style="width: <?php echo esc_attr( $avail_pct ); ?>%"></div>
			</div>
		<?php endif; ?>
	</div>

	<div class="nursery-card__footer">
		<div class="nursery-card__contacts">
			<?php if ( $phone ) : ?>
				<a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>"
				   class="btn-icon" title="<?php esc_attr_e( 'Zadzwoń', 'zlobki-polska' ); ?>">📞</a>
			<?php endif; ?>
			<?php if ( $email ) : ?>
				<a href="mailto:<?php echo esc_attr( $email ); ?>"
				   class="btn-icon" title="<?php esc_attr_e( 'Napisz e-mail', 'zlobki-polska' ); ?>">✉️</a>
			<?php endif; ?>
			<?php if ( $www ) : ?>
				<a href="<?php echo esc_url( strpos( $www, 'http' ) === 0 ? $www : 'https://' . $www ); ?>"
				   class="btn-icon" title="<?php esc_attr_e( 'Odwiedź stronę', 'zlobki-polska' ); ?>"
				   target="_blank" rel="noopener noreferrer">🌐</a>
			<?php endif; ?>
		</div>
		<a href="<?php the_permalink(); ?>" class="btn--details">
			<?php esc_html_e( 'Szczegóły →', 'zlobki-polska' ); ?>
		</a>
	</div>

</article>
