<?php

/**
 * Product detail info component.
 *
 * @package JerseyPlug
 */

$args    = wp_parse_args($args ?? [], ['product' => null]);
$product = $args['product'];

if (! $product instanceof WC_Product) {
	return;
}

$product_id = (int) $product->get_id();
$base_price = (float) ($product->is_on_sale() ? $product->get_sale_price() : $product->get_regular_price());
$old_price  = $product->is_on_sale() ? (float) $product->get_regular_price() : null;

// Personalization details
$allow_print = get_post_meta($product_id, '_allow_print', true) === 'yes' || true; // Enable by default or support custom meta
$print_price = (float) get_post_meta($product_id, '_print_price', true);
if ($print_price <= 0) {
	$print_price = 150.0; // Default print fee if empty
}

$patches_meta = get_post_meta($product_id, '_patches', true);
$patches = is_array($patches_meta) ? $patches_meta : [
	['name' => 'Champions League', 'price' => 120],
	['name' => 'Premier League', 'price' => 100],
	['name' => 'World Cup', 'price' => 150],
];

// Variations (Sizes)
$is_variable = $product->is_type('variable');
$available_variations = ($is_variable && $product instanceof WC_Product_Variable) ? $product->get_available_variations() : [];
$variation_attributes = ($is_variable && $product instanceof WC_Product_Variable) ? $product->get_variation_attributes() : [];
$sizes = $variation_attributes['pa_size'] ?? ['S', 'M', 'L', 'XL', '2XL', '3XL'];

// Average rating & reviews
$rating_data  = jerseyplug_get_random_rating_and_reviews($product_id);
$rating       = $rating_data['rating'];
$review_count = $rating_data['reviews'];
?>

<div
	class="space-y-6"
	x-data="{
		basePrice: <?php echo esc_attr((string) $base_price); ?>,
		printPrice: <?php echo esc_attr((string) $print_price); ?>,
		quantity: 1,
		selectedSize: '',
		variationId: '',
		customName: '',
		customNumber: '',
		selectedPatch: null,
		sizeError: '',
		isAddingToCart: false,
		variations: <?php echo esc_attr(wp_json_encode($available_variations)); ?>,

		get singleProductPrice() {
			let price = this.basePrice;
			if (this.customName.trim() !== '' || this.customNumber.trim() !== '') {
				price += this.printPrice;
			}
			if (this.selectedPatch) {
				price += parseFloat(this.selectedPatch.price);
			}
			return price;
		},
		get totalCalculatedPrice() {
			return this.singleProductPrice * this.quantity;
		},
		selectSize(size) {
			this.selectedSize = size;
			this.sizeError = '';
			if (this.variations && this.variations.length > 0) {
				const found = this.variations.find(v => {
					const attrs = Object.values(v.attributes);
					return attrs.includes(size) || attrs.includes(size.toLowerCase());
				});
				this.variationId = found ? found.variation_id : '';
			}
		},
		formatCurrency(amount) {
			return 'R ' + amount;
		},
		submitForm(e) {
			if (<?php echo $is_variable ? 'true' : 'false'; ?> && !this.selectedSize) {
				e.preventDefault();
				this.sizeError = '<?php echo esc_attr(jerseyplug_pll('Please select a size')); ?>';
				return false;
			}
			this.isAddingToCart = true;
		}
	}">
	<!-- Product Header Info -->
	<div>
		<h1 class="text-2xl font-black text-gray-900 md:text-3xl lg:text-4xl uppercase leading-tight">
			<?php echo esc_html($product->get_name()); ?>
		</h1>

		<div class="mt-3 flex items-center gap-3">
			<!-- Price -->
			<div class="flex items-baseline gap-2">
				<span class="text-2xl font-black text-primary" x-text="formatCurrency(singleProductPrice)"></span>
				<?php if ($old_price) : ?>
					<span class="text-sm text-gray-400 line-through">
						<?php echo esc_html(sprintf('R %s', $old_price)); ?>
					</span>
				<?php endif; ?>
			</div>

			<!-- Star ratings -->
			<div class="flex items-center gap-1 border-l border-gray-200 pl-3 text-sm text-yellow-500">
				<svg aria-hidden="true" viewBox="0 0 20 20" class="h-4 w-4 fill-current">
					<path d="m10 15.27 5.18 3.13-1.45-5.88L18.5 8.5l-6.06-.48L10 2.5 7.56 8.02 1.5 8.5l4.77 4.02-1.45 5.88L10 15.27Z"></path>
				</svg>
				<span class="font-bold text-gray-900"><?php echo esc_html($rating); ?></span>
				<span class="text-gray-400">(<?php echo esc_html($review_count); ?> <?php echo esc_html(jerseyplug_pll('Reviews')); ?>)</span>
			</div>
		</div>
	</div>

	<!-- WooCommerce Cart Form wrapper -->
	<form method="post" action="" enctype="multipart/form-data" @submit="submitForm($event)" class="space-y-6">
		<input type="hidden" name="add-to-cart" value="<?php echo esc_attr((string) $product_id); ?>" />
		<?php if ($is_variable) : ?>
			<input type="hidden" name="variation_id" :value="variationId" />
			<input type="hidden" name="attribute_pa_size" :value="selectedSize" />
		<?php endif; ?>

		<!-- Variant Selector (Sizes) -->
		<div class="space-y-3">
			<div class="flex items-center justify-between">
				<span class="text-xs font-bold uppercase tracking-wider text-gray-500">
					<?php echo esc_html(jerseyplug_pll('Select Size')); ?>
				</span>
				<button
					type="button"
					@click="$dispatch('open-size-guide')"
					class="text-xs font-bold text-primary underline hover:text-accent">
					<?php echo esc_html(jerseyplug_pll('Size Guide')); ?>
				</button>
			</div>

			<div class="flex flex-wrap gap-2">
				<?php foreach ($sizes as $size) : ?>
					<button
						type="button"
						@click="selectSize('<?php echo esc_attr($size); ?>')"
						class="min-w-12 h-12 rounded-xl border-2 px-3 text-sm font-black uppercase transition-all duration-200"
						:class="selectedSize === '<?php echo esc_attr($size); ?>' 
							? 'border-primary bg-primary text-white shadow-md' 
							: 'border-gray-200 bg-white text-gray-900 hover:border-gray-400'">
						<?php echo esc_html($size); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<div x-show="sizeError" class="text-xs font-bold text-red-500 mt-1" x-text="sizeError"></div>
		</div>

		<!-- Personalization Builder -->
		<?php if ($allow_print) : ?>
			<div class="rounded-2xl border border-gray-100 bg-zinc-50 p-4 space-y-4">
				<div class="flex items-center justify-between">
					<h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">
						<?php echo esc_html(jerseyplug_pll('Personalization')); ?>
					</h3>
					<span class="text-[10px] font-bold text-primary bg-accent/20 px-2 py-0.5 rounded-full">
						+R <?php echo esc_html(number_format($print_price, 0)); ?>
					</span>
				</div>

				<div class="grid grid-cols-3 gap-3">
					<div class="col-span-2">
						<label class="block text-[10px] font-bold uppercase text-gray-400 mb-1"><?php echo esc_html( jerseyplug_pll( 'Custom Name' ) ); ?></label>
						<input
							type="text"
							name="custom_name"
							x-model="customName"
							maxlength="12"
							placeholder="MESSI"
							class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-xs font-bold uppercase tracking-wider text-gray-900 placeholder:text-gray-300 focus:border-primary focus:outline-none" />
					</div>
					<div>
						<label class="block text-[10px] font-bold uppercase text-gray-400 mb-1"><?php echo esc_html( jerseyplug_pll( 'Custom Number' ) ); ?></label>
						<input
							type="text"
							name="custom_number"
							x-model="customNumber"
							maxlength="2"
							placeholder="10"
							class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-xs font-bold text-gray-900 placeholder:text-gray-300 focus:border-primary focus:outline-none" />
					</div>
				</div>

				<!-- Patches selector -->
				<?php if (! empty($patches)) : ?>
					<div class="space-y-2">
						<span class="block text-[10px] font-bold uppercase text-gray-400"><?php echo esc_html( jerseyplug_pll( 'Patch' ) ); ?></span>
						<input type="hidden" name="selected_patch" :value="selectedPatch ? JSON.stringify(selectedPatch) : ''" />
						<div class="grid grid-cols-2 gap-2">
							<?php foreach ($patches as $patch) : ?>
								<button
									type="button"
									@click="selectedPatch = (selectedPatch && selectedPatch.name === '<?php echo esc_attr($patch['name']); ?>') ? null : { name: '<?php echo esc_attr($patch['name']); ?>', price: <?php echo esc_attr((string) $patch['price']); ?> }"
									class="flex items-center justify-between rounded-xl border p-2.5 text-left transition-all duration-200"
									:class="selectedPatch && selectedPatch.name === '<?php echo esc_attr($patch['name']); ?>' 
										? 'border-primary bg-primary/5 shadow-sm' 
										: 'border-gray-200 bg-white hover:border-gray-400'">
									<span class="text-[11px] font-bold text-gray-900 leading-tight"><?php echo esc_html($patch['name']); ?></span>
									<span class="text-[10px] font-bold text-gray-400 shrink-0 pl-1">+R <?php echo esc_html(number_format($patch['price'], 0)); ?></span>
								</button>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<!-- Actions (Quantity & Add to Cart) -->
		<div class="flex items-center gap-3">
			<div class="flex h-12 items-center rounded-xl border border-gray-200 px-1 bg-white">
				<button
					type="button"
					@click="if (quantity > 1) quantity--"
					class="w-10 h-10 flex items-center justify-center font-black text-gray-500 hover:text-primary transition-colors text-lg">
					&minus;
				</button>
				<input
					type="number"
					name="quantity"
					x-model.number="quantity"
					min="1"
					class="w-12 text-center text-sm font-black text-gray-900 border-none bg-transparent focus:outline-none" />
				<button
					type="button"
					@click="quantity++"
					class="w-10 h-10 flex items-center justify-center font-black text-gray-500 hover:text-primary transition-colors text-lg">
					&plus;
				</button>
			</div>

			<button
				type="submit"
				class="flex-1 h-12 rounded-xl bg-primary text-xs font-black uppercase tracking-wider text-white shadow-lg transition-colors hover:bg-accent hover:text-primary flex items-center justify-center gap-2"
				:disabled="isAddingToCart">
				<span x-show="!isAddingToCart">
					<?php echo esc_html(jerseyplug_pll('Add to Cart')); ?>
				</span>
				<span x-show="isAddingToCart" class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
			</button>
		</div>
	</form>

	<!-- Sticky Mobile Bar -->
	<div
		class="fixed bottom-0 inset-x-0 z-40 bg-white border-t border-gray-100 p-4 shadow-xl flex items-center justify-between md:hidden transition-transform duration-300"
		x-show="true"
		x-cloak
		x-data="{ showBar: false }"
		@scroll.window="showBar = (window.scrollY > 400)"
		:class="showBar ? 'translate-y-0' : 'translate-y-full'">
		<div class="flex flex-col">
			<span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider"><?php echo esc_html( jerseyplug_pll( 'Total' ) ); ?></span>
			<span class="text-lg font-black text-primary" x-text="formatCurrency(totalCalculatedPrice)"></span>
		</div>
		<button
			type="button"
			@click="document.querySelector('form').requestSubmit ? document.querySelector('form').requestSubmit() : document.querySelector('form').submit()"
			class="bg-primary text-white text-[11px] font-black uppercase tracking-wider px-6 py-3 rounded-xl shadow-lg hover:bg-accent hover:text-primary transition-colors">
			<?php echo esc_html(jerseyplug_pll('Add to Bag')); ?>
		</button>
	</div>
</div>