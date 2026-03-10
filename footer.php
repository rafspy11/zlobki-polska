<?php
$year = date( 'Y' );
$total_zlobki = wp_count_posts( 'zlobek' )->publish ?? 0;
?>

<footer class="site-footer" role="contentinfo">
	<div class="container">
		<div class="footer-grid">

			<div class="footer-brand">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-brand__logo">
					<div class="footer-brand__logo-icon" aria-hidden="true">🍼</div>
					<span class="footer-brand__logo-name"><?php bloginfo( 'name' ); ?></span>
				</a>
				<p><?php esc_html_e( 'Największa baza żłobków i klubów dziecięcych w Polsce. Dane oparte na oficjalnym rejestrze Ministerstwa Rodziny i Polityki Społecznej.', 'zlobki-polska' ); ?></p>
			</div>

			<div class="footer-col">
				<div class="footer-col__title"><?php esc_html_e( 'Nawigacja', 'zlobki-polska' ); ?></div>
				<div class="footer-links">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Strona główna', 'zlobki-polska' ); ?></a>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'zlobek' ) ); ?>"><?php esc_html_e( 'Wyszukiwarka żłobków', 'zlobki-polska' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/mapa/' ) ); ?>"><?php esc_html_e( 'Mapa instytucji', 'zlobki-polska' ); ?></a>
					<a href="<?php echo esc_url( get_term_link( 'zlobek', 'typ_instytucji' ) ?: '#' ); ?>"><?php esc_html_e( 'Żłobki', 'zlobki-polska' ); ?></a>
					<a href="<?php echo esc_url( get_term_link( 'klub-dzieciecy', 'typ_instytucji' ) ?: '#' ); ?>"><?php esc_html_e( 'Kluby dziecięce', 'zlobki-polska' ); ?></a>
				</div>
			</div>

			<div class="footer-col">
				<div class="footer-col__title"><?php esc_html_e( 'Województwa', 'zlobki-polska' ); ?></div>
				<div class="footer-links">
					<?php
					$woj_terms = get_terms( [
						'taxonomy' => 'wojewodztwo',
						'number'   => 8,
						'orderby'  => 'count',
						'order'    => 'DESC',
						'hide_empty' => true,
					] );
					if ( ! is_wp_error( $woj_terms ) ) {
						foreach ( $woj_terms as $term ) {
							$url = add_query_arg( 'woj', $term->slug, get_post_type_archive_link( 'zlobek' ) );
							printf(
								'<a href="%s">%s</a>',
								esc_url( $url ),
								esc_html( zlobki_ucwords_pl( $term->name ) )
							);
						}
					}
					?>
				</div>
			</div>

			<div class="footer-col">
				<div class="footer-col__title"><?php esc_html_e( 'Informacje', 'zlobki-polska' ); ?></div>
				<div class="footer-links">
					<a href="<?php echo esc_url( home_url( '/o-serwisie/' ) ); ?>"><?php esc_html_e( 'O serwisie', 'zlobki-polska' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/polityka-prywatnosci/' ) ); ?>"><?php esc_html_e( 'Polityka prywatności', 'zlobki-polska' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>"><?php esc_html_e( 'Kontakt', 'zlobki-polska' ); ?></a>
					<a href="https://www.gov.pl/web/rodzina/rejestr-zlobkow-i-klubow-dzieciecych" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Źródło danych', 'zlobki-polska' ); ?></a>
				</div>
			</div>

		</div>

		<div class="footer-bottom">
			<span>
				&copy; <?php echo esc_html( $year ); ?> <?php bloginfo( 'name' ); ?>.
				<?php esc_html_e( 'Dane z rejestru MRiPS.', 'zlobki-polska' ); ?>
			</span>
			<span>
				<?php
				/* translators: %d: number of institutions */
				printf( esc_html__( 'Baza zawiera %d instytucji', 'zlobki-polska' ), esc_html( $total_zlobki ) );
				?>
			</span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
