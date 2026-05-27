<?php
/**
 * Homepage data helpers.
 *
 * @package JerseyPlug
 */

if ( ! function_exists( 'jerseyplug_get_homepage_shop_url' ) ) {
	function jerseyplug_get_homepage_shop_url(): string {
		if ( function_exists( 'wc_get_page_permalink' ) ) {
			$shop_url = wc_get_page_permalink( 'shop' );
			if ( is_string( $shop_url ) && $shop_url !== '' ) {
				return $shop_url;
			}
		}

		return home_url( '/' );
	}
}

if ( ! function_exists( 'jerseyplug_get_homepage_term_logo_url' ) ) {
	function jerseyplug_get_homepage_term_logo_url( int $term_id ): string {
		$thumbnail_id = (int) get_term_meta( $term_id, 'thumbnail_id', true );
		if ( $thumbnail_id > 0 ) {
			$thumbnail_url = wp_get_attachment_image_url( $thumbnail_id, 'thumbnail' );
			if ( $thumbnail_url ) {
				return (string) $thumbnail_url;
			}
		}

		$external_logo = (string) get_term_meta( $term_id, 'external_logo_url', true );
		if ( $external_logo !== '' ) {
			return $external_logo;
		}

		return function_exists( 'wc_placeholder_img_src' )
			? (string) wc_placeholder_img_src( 'woocommerce_thumbnail' )
			: get_theme_file_uri( '/resources/images/placeholder-category.png' );
	}
}

if ( ! function_exists( 'jerseyplug_get_homepage_hero_slides' ) ) {
	function jerseyplug_get_homepage_hero_slides( int $page_id = 0 ): array {
		$page_id = $page_id > 0 ? $page_id : get_queried_object_id();
		$page    = $page_id > 0 ? get_post( $page_id ) : null;

		$page_title   = $page instanceof WP_Post ? get_the_title( $page ) : get_bloginfo( 'name' );
		$page_excerpt = $page instanceof WP_Post ? trim( wp_strip_all_tags( (string) get_the_excerpt( $page ) ) ) : '';
		$page_image   = $page_id > 0 ? (string) get_the_post_thumbnail_url( $page_id, 'full' ) : '';

		if ( $page_excerpt === '' ) {
			$page_excerpt = (string) get_bloginfo( 'description' );
		}

		$slides = [
			[
				'id'            => 'home',
				'image'         => $page_image,
				'title'         => $page_title,
				'desc'          => $page_excerpt,
				'badge'         => function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Official Badge' ) : __( 'Official Badge', 'jerseyplug' ),
				'button_label'  => function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Shop Now' ) : __( 'Shop Now', 'jerseyplug' ),
				'button_url'    => jerseyplug_get_homepage_shop_url(),
				'secondary_label' => function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'View All' ) : __( 'View All', 'jerseyplug' ),
				'secondary_url' => jerseyplug_get_homepage_shop_url(),
			],
		];

		$categories = jerseyplug_get_homepage_featured_categories();
		if ( ! empty( $categories ) ) {
			$slide_category = $categories[0];
			$slides[]       = [
				'id'              => 'category',
				'image'           => $slide_category['image'] ?? '',
				'title'           => $slide_category['name'] ?? $page_title,
				'desc'            => $slide_category['description'] ?? '',
				'badge'           => function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Featured Category' ) : __( 'Featured Category', 'jerseyplug' ),
				'button_label'    => function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Discover' ) : __( 'Discover', 'jerseyplug' ),
				'button_url'      => $slide_category['url'] ?? jerseyplug_get_homepage_shop_url(),
				'secondary_label' => function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'View All' ) : __( 'View All', 'jerseyplug' ),
				'secondary_url'   => jerseyplug_get_homepage_shop_url(),
			];
		}

		$products = jerseyplug_get_homepage_products( 1, 'featured' );
		if ( ! empty( $products ) ) {
			$product = $products[0];
			$slides[] = [
				'id'              => 'product',
				'image'           => $product['image'] ?? $page_image,
				'title'           => $product['name'] ?? $page_title,
				'desc'            => $product['category'] ?? '',
				'badge'           => function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Trending Now' ) : __( 'Trending Now', 'jerseyplug' ),
				'button_label'    => function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'View Details' ) : __( 'View Details', 'jerseyplug' ),
				'button_url'      => $product['url'] ?? jerseyplug_get_homepage_shop_url(),
				'secondary_label' => function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Shop Now' ) : __( 'Shop Now', 'jerseyplug' ),
				'secondary_url'   => jerseyplug_get_homepage_shop_url(),
			];
		}

		return (array) apply_filters( 'jerseyplug_home_hero_slides', $slides, $page_id );
	}
}

if ( ! function_exists( 'jerseyplug_get_homepage_featured_categories' ) ) {
	function jerseyplug_get_homepage_featured_categories(): array {
		$slugs = apply_filters(
			'jerseyplug_homepage_featured_category_slugs',
			[ 'club-kits', 'national-teams', 'accessories', 'protective-gear' ]
		);

		$terms = [];
		foreach ( $slugs as $slug ) {
			$term = get_term_by( 'slug', sanitize_title( (string) $slug ), 'product_cat' );
			if ( $term instanceof WP_Term ) {
				$terms[] = $term;
			}
		}

		if ( empty( $terms ) && taxonomy_exists( 'product_cat' ) ) {
			$terms = get_terms(
				[
					'taxonomy'               => 'product_cat',
					'hide_empty'             => false,
					'orderby'                => 'menu_order',
					'order'                  => 'ASC',
					'number'                 => 4,
					'update_term_meta_cache' => true,
				]
			);

			if ( is_wp_error( $terms ) ) {
				$terms = [];
			}
		}

		$cards = [];
		$variants = [ 'large', 'medium', 'small', 'small' ];
		foreach ( array_values( $terms ) as $index => $term ) {
			if ( ! $term instanceof WP_Term ) {
				continue;
			}

			$link = get_term_link( $term );
			$term_description = function_exists( 'get_term_field' )
				? (string) get_term_field( 'description', $term, 'product_cat' )
				: '';
			$cards[] = [
				'term_id'     => (int) $term->term_id,
				'name'        => (string) $term->name,
				'description' => wp_trim_words( wp_strip_all_tags( $term_description ), 14, '...' ),
				'url'         => is_wp_error( $link ) ? '' : (string) $link,
				'image'       => jerseyplug_get_homepage_term_logo_url( (int) $term->term_id ),
				'variant'     => $variants[ $index ] ?? 'small',
			];
		}

		return (array) apply_filters( 'jerseyplug_homepage_featured_categories', $cards );
	}
}

if ( ! function_exists( 'jerseyplug_get_homepage_featured_leagues' ) ) {
	function jerseyplug_get_homepage_featured_leagues(): array {
		$slugs = apply_filters(
			'jerseyplug_homepage_league_slugs',
			[ 'premier-league', 'la-liga', 'serie-a', 'bundesliga', 'ligue-1', 'championship' ]
		);

		$terms = [];
		foreach ( $slugs as $slug ) {
			$term = get_term_by( 'slug', sanitize_title( (string) $slug ), 'product_cat' );
			if ( $term instanceof WP_Term ) {
				$terms[] = $term;
			}
		}

		if ( empty( $terms ) && taxonomy_exists( 'product_cat' ) ) {
			$terms = get_terms(
				[
					'taxonomy'               => 'product_cat',
					'hide_empty'             => false,
					'orderby'                => 'name',
					'order'                  => 'ASC',
					'number'                 => 6,
					'parent'                 => 0,
					'update_term_meta_cache' => true,
				]
			);

			if ( is_wp_error( $terms ) ) {
				$terms = [];
			}
		}

		$cards = [];
		foreach ( array_values( $terms ) as $term ) {
			if ( ! $term instanceof WP_Term ) {
				continue;
			}

			$link = get_term_link( $term );
			$cards[] = [
				'term_id' => (int) $term->term_id,
				'name'    => (string) $term->name,
				'url'     => is_wp_error( $link ) ? '' : (string) $link,
				'logo'    => jerseyplug_get_homepage_term_logo_url( (int) $term->term_id ),
			];
		}

		return (array) apply_filters( 'jerseyplug_homepage_featured_leagues', $cards );
	}
}

if ( ! function_exists( 'jerseyplug_get_homepage_products' ) ) {
	function jerseyplug_get_homepage_products( int $limit = 4, string $context = 'featured' ): array {
		if ( ! function_exists( 'wc_get_products' ) ) {
			return [];
		}

		$args = [
			'status' => 'publish',
			'limit'  => $limit,
			'return' => 'objects',
		];

		if ( $context === 'featured' ) {
			$args['featured'] = true;
			$args['orderby']   = 'date';
			$args['order']     = 'DESC';
		} else {
			$args['orderby'] = 'date';
			$args['order']   = 'DESC';
		}

		if ( 'featured' === $context ) {
			$args = apply_filters( 'jerseyplug_home_featured_products_args', $args );
		} else {
			$args = apply_filters( 'jerseyplug_home_new_products_args', $args );
		}

		$products = wc_get_products( $args );
		if ( empty( $products ) ) {
			return [];
		}

		$cards = [];
		foreach ( $products as $product ) {
			if ( ! $product instanceof WC_Product ) {
				continue;
			}

			$image_id = $product->get_image_id();
			$image    = $image_id > 0 ? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' ) : '';
			$terms    = get_the_terms( $product->get_id(), 'product_cat' );
			$category = '';
			if ( is_array( $terms ) && ! empty( $terms ) && $terms[0] instanceof WP_Term ) {
				$category = (string) $terms[0]->name;
			}

			$cards[] = [
				'id'       => (int) $product->get_id(),
				'slug'     => (string) $product->get_slug(),
				'name'     => (string) $product->get_name(),
				'url'      => (string) get_permalink( $product->get_id() ),
				'image'    => $image ?: ( function_exists( 'wc_placeholder_img_src' ) ? (string) wc_placeholder_img_src( 'woocommerce_thumbnail' ) : '' ),
				'category' => $category,
				'price'    => wp_strip_all_tags( $product->get_price_html() ),
				'rating_label' => number_format_i18n( (float) $product->get_average_rating(), 1 ),
				'tag'      => $context === 'featured' ? ( function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Trending Now' ) : __( 'Trending Now', 'jerseyplug' ) ) : ( function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'New' ) : __( 'New', 'jerseyplug' ) ),
			];
		}

		return $cards;
	}
}

if ( ! function_exists( 'jerseyplug_get_homepage_feature_icon' ) ) {
	function jerseyplug_get_homepage_feature_icon( string $icon ): string {
		$icon = sanitize_key( $icon );

		$icons = [
			'truck'   => '<svg aria-hidden="true" viewBox="0 0 24 24" class="h-5 w-5 text-primary md:h-8 md:w-8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 17h4V5H2v12h3"></path><path d="M14 8h4l4 4v5h-2"></path><circle cx="7.5" cy="17.5" r="2.5"></circle><circle cx="17.5" cy="17.5" r="2.5"></circle></svg>',
			'shield'  => '<svg aria-hidden="true" viewBox="0 0 24 24" class="h-5 w-5 text-primary md:h-8 md:w-8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"></path><path d="m9 12 2 2 4-4"></path></svg>',
			'refresh' => '<svg aria-hidden="true" viewBox="0 0 24 24" class="h-5 w-5 text-primary md:h-8 md:w-8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 0 1 15-6.7L21 8"></path><path d="M21 3v5h-5"></path><path d="M21 12a9 9 0 0 1-15 6.7L3 16"></path><path d="M3 21v-5h5"></path></svg>',
			'support' => '<svg aria-hidden="true" viewBox="0 0 24 24" class="h-5 w-5 text-primary md:h-8 md:w-8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0v4"></path><path d="M4 10v5a3 3 0 0 0 3 3h1"></path><path d="M20 10v5a3 3 0 0 1-3 3h-1"></path><path d="M8 18h3"></path></svg>',
		];

		return $icons[ $icon ] ?? $icons['support'];
	}
}

if ( ! function_exists( 'jerseyplug_get_homepage_features' ) ) {
	function jerseyplug_get_homepage_features(): array {
		$features = [
			[
				'icon'  => 'truck',
				'title' => function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Fast Delivery' ) : __( 'Fast Delivery', 'jerseyplug' ),
				'desc'  => function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Reliable shipping across South Africa.' ) : __( 'Reliable shipping across South Africa.', 'jerseyplug' ),
			],
			[
				'icon'  => 'shield',
				'title' => function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Secure Checkout' ) : __( 'Secure Checkout', 'jerseyplug' ),
				'desc'  => function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Safe payments and trusted service.' ) : __( 'Safe payments and trusted service.', 'jerseyplug' ),
			],
			[
				'icon'  => 'refresh',
				'title' => function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Easy Returns' ) : __( 'Easy Returns', 'jerseyplug' ),
				'desc'  => function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Flexible support when plans change.' ) : __( 'Flexible support when plans change.', 'jerseyplug' ),
			],
			[
				'icon'  => 'support',
				'title' => function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Friendly Support' ) : __( 'Friendly Support', 'jerseyplug' ),
				'desc'  => function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Quick help from a team that knows sport.' ) : __( 'Quick help from a team that knows sport.', 'jerseyplug' ),
			],
		];

		return (array) apply_filters( 'jerseyplug_homepage_features', $features );
	}
}

if ( ! function_exists( 'jerseyplug_get_homepage_testimonials' ) ) {
	function jerseyplug_get_homepage_testimonials(): array {
		$items = [
			[
				'name'  => 'Thando M.',
				'quote' => function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Great quality and fast delivery.' ) : __( 'Great quality and fast delivery.', 'jerseyplug' ),
			],
			[
				'name'  => 'Johan P.',
				'quote' => function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'The fit and print detail are excellent.' ) : __( 'The fit and print detail are excellent.', 'jerseyplug' ),
			],
			[
				'name'  => 'Ayesha K.',
				'quote' => function_exists( 'jerseyplug_pll' ) ? jerseyplug_pll( 'Easy to order and the support team was helpful.' ) : __( 'Easy to order and the support team was helpful.', 'jerseyplug' ),
			],
		];

		return (array) apply_filters( 'jerseyplug_homepage_testimonials', $items );
	}
}

if ( ! function_exists( 'jerseyplug_get_homepage_flag_items' ) ) {
	function jerseyplug_get_homepage_flag_items(): array {
		$items = [
			'Premier League',
			'La Liga',
			'Serie A',
			'Bundesliga',
			'Ligue 1',
			'Champions League',
			'World Cup',
			'South Africa',
		];

		return (array) apply_filters( 'jerseyplug_homepage_flag_items', $items );
	}
}
