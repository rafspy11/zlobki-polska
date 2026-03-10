<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" role="banner">
	<div class="container">
		<div class="header-inner">

			<!-- Logo -->
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo" rel="home">
				<div class="site-logo__icon" aria-hidden="true">🍼</div>
				<div class="site-logo__text">
					<span class="site-logo__name">
						<?php
						$custom_logo_id = get_theme_mod( 'custom_logo' );
						if ( $custom_logo_id ) {
							echo wp_get_attachment_image( $custom_logo_id, 'full' );
						} else {
							bloginfo( 'name' );
						}
						?>
					</span>
					<span class="site-logo__tagline">Wyszukiwarka żłobków i klubów dziecięcych</span>
				</div>
			</a>

			<!-- Navigation -->
			<nav class="site-nav" id="site-nav" role="navigation" aria-label="<?php esc_attr_e( 'Menu główne', 'zlobki-polska' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Strona główna', 'zlobki-polska' ); ?></a>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'zlobek' ) ); ?>"><?php esc_html_e( 'Wyszukiwarka', 'zlobki-polska' ); ?></a>
				<a href="<?php echo esc_url( add_query_arg( 'typ', 'zlobek', get_post_type_archive_link( 'zlobek' ) ) ); ?>"><?php esc_html_e( 'Żłobki', 'zlobki-polska' ); ?></a>
				<a href="<?php echo esc_url( add_query_arg( 'typ', 'klub-dzieciecy', get_post_type_archive_link( 'zlobek' ) ) ); ?>"><?php esc_html_e( 'Kluby dziecięce', 'zlobki-polska' ); ?></a>
				<?php
				wp_nav_menu( [
					'theme_location' => 'primary',
					'container'      => false,
					'items_wrap'     => '%3$s',
					'fallback_cb'    => false,
				] );
				?>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'zlobek' ) ); ?>" class="header-cta"><?php esc_html_e( 'Znajdź żłobek', 'zlobki-polska' ); ?></a>
			</nav>

			<button class="menu-toggle" id="menu-toggle" aria-label="<?php esc_attr_e( 'Menu', 'zlobki-polska' ); ?>" aria-controls="site-nav" aria-expanded="false">
				<span class="dashicons dashicons-menu-alt3" aria-hidden="true">☰</span>
			</button>

		</div>
	</div>
</header>
