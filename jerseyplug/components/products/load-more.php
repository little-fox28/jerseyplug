<?php
/**
 * Pagination component for the products page.
 *
 * Renders standard WooCommerce-style pagination using paginate_links().
 * The container is swapped by the AJAX filter JS module.
 *
 * @package JerseyPlug
 */

global $wp_query;

$total_pages  = isset( $wp_query->max_num_pages ) ? (int) $wp_query->max_num_pages : 1;
$current_page = max( 1, get_query_var( 'paged', 1 ) );

if ( $total_pages <= 1 ) {
	return;
}
?>

<div id="pagination-container" class="container mx-auto px-4">
	<nav class="mt-16 flex items-center justify-center gap-2" aria-label="<?php echo esc_attr__( 'Product pagination', 'jerseyplug' ); ?>">
		<?php
		echo wp_kses_post(
			paginate_links( [
				'total'     => $total_pages,
				'current'   => $current_page,
				'format'    => '?paged=%#%',
				'prev_text' => '<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>',
				'next_text' => '<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>',
				'type'      => 'plain',
				'before_page_number' => '',
				'after_page_number'  => '',
			] )
		);
		?>
	</nav>

	<style>
		#pagination-container .page-numbers {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			min-width: 2.5rem;
			height: 2.5rem;
			padding: 0 0.5rem;
			border-radius: 0.5rem;
			font-size: 0.875rem;
			font-weight: 600;
			color: #374151;
			transition: all 0.2s;
		}
		#pagination-container .page-numbers:hover {
			background-color: #f3f4f6;
		}
		#pagination-container .page-numbers.current {
			background-color: var(--color-primary, #163300);
			color: #ffffff;
		}
		#pagination-container .page-numbers.prev,
		#pagination-container .page-numbers.next {
			min-width: 2.5rem;
		}
		#pagination-container .page-numbers.dots {
			pointer-events: none;
			color: #9ca3af;
		}
	</style>
</div>
