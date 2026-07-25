<?php
/**
 * Sticky Add to Cart Bar for Mobile
 */
global $product;
?>
<div x-show="showStickyBar" x-cloak
	 x-transition:enter="transition ease-out duration-300"
	 x-transition:enter-start="opacity-0 translate-y-full"
	 x-transition:enter-end="opacity-100 translate-y-0"
	 x-transition:leave="transition ease-in duration-200"
	 x-transition:leave-start="opacity-100 translate-y-0"
	 x-transition:leave-end="opacity-0 translate-y-full"
	 class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200 p-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] lg:hidden">
	
	<div class="flex items-center justify-between gap-4">
		<div class="flex-1 truncate">
			<div class="font-bold text-sm text-gray-900 truncate"><?php echo wp_kses_post($product->get_name()); ?></div>
			<div class="text-primary font-bold text-sm" x-html="'R ' + singleProductPrice.toFixed(2)"></div>
		</div>
		<button type="button" 
				@click="submitForm()"
				:disabled="!isVariationSelected || isAddingToCart"
				class="bg-primary text-white font-bold uppercase tracking-wider text-xs px-6 py-3 rounded-full hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed min-h-[44px] flex-shrink-0">
			<span x-show="!isAddingToCart"><?php echo esc_html( jerseyplug_pll( 'Add to Cart' ) ); ?></span>
			<span x-show="isAddingToCart"><?php echo esc_html( jerseyplug_pll( 'Adding...' ) ); ?></span>
		</button>
	</div>
</div>
