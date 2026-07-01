<?php
/**
 * Personalization Builder Fragment
 *
 * Included via hook `woocommerce_before_add_to_cart_button`
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
$product_id = $product->get_id();

$allow_print = get_post_meta($product_id, '_allow_personalization', true) === 'yes' || get_post_meta($product_id, '_allow_print', true) === 'yes';
$print_price = (float) get_post_meta($product_id, '_print_price', true);
if ($print_price <= 0) {
	$print_price = 150.0;
}

$patches_meta = get_post_meta($product_id, '_available_patches', true);
if (empty($patches_meta)) {
	$patches_meta = get_post_meta($product_id, '_patches', true);
}
$patches = is_array($patches_meta) ? $patches_meta : [];

// If it's just array of IDs from the new backend, we need to load them
$formatted_patches = [];
if (!empty($patches)) {
	foreach ($patches as $patch_data) {
		if (is_numeric($patch_data)) {
			// It's a product ID (from our new backend logic)
			$patch_product = wc_get_product($patch_data);
			if ($patch_product) {
				$formatted_patches[] = [
					'slug'  => $patch_product->get_id(),
					'name'  => $patch_product->get_name(),
					'price' => (float) $patch_product->get_price(),
				];
			}
		} elseif (is_array($patch_data)) {
			// Legacy format
			$formatted_patches[] = [
				'slug'  => isset($patch_data['slug']) ? $patch_data['slug'] : sanitize_title($patch_data['name']),
				'name'  => $patch_data['name'],
				'price' => (float) $patch_data['price'],
			];
		}
	}
}

if ( ! $allow_print && empty($formatted_patches) ) {
	return;
}
?>

<div class="rounded-2xl border border-gray-100 bg-zinc-50 p-4 space-y-4 mb-6">
	<div class="flex items-center justify-between">
		<h3 class="text-xs font-black uppercase tracking-widest text-gray-500">
			<?php esc_html_e('Personalization', 'jerseyplug'); ?>
		</h3>
		<?php if ( $allow_print ) : ?>
			<span class="text-[10px] font-bold text-primary bg-accent/20 px-2 py-0.5 rounded-full">
				+R <?php echo esc_html(number_format($print_price, 0)); ?>
			</span>
		<?php endif; ?>
	</div>

	<?php if ( $allow_print ) : ?>
		<!-- Name + Number -->
		<div class="grid grid-cols-3 gap-3">
			<div class="col-span-2">
				<label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5" for="custom_name_input">
					<?php esc_html_e('Name', 'jerseyplug'); ?>
				</label>
				<input
					id="custom_name_input"
					type="text"
					name="custom_name"
					x-model="customName"
					maxlength="12"
					placeholder="MESSI"
					autocomplete="off"
					class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-xs font-bold uppercase tracking-wider text-gray-900 placeholder:text-gray-300 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors" />
			</div>
			<div>
				<label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5" for="custom_number_input">
					<?php esc_html_e('Number', 'jerseyplug'); ?>
				</label>
				<input
					id="custom_number_input"
					type="text"
					name="custom_number"
					x-model="customNumber"
					maxlength="2"
					placeholder="10"
					autocomplete="off"
					class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-xs font-bold text-gray-900 placeholder:text-gray-300 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors" />
			</div>
		</div>
	<?php endif; ?>

	<!-- Patches -->
	<?php if (! empty($formatted_patches)) : ?>
		<div class="space-y-2">
			<span class="block text-[10px] font-bold uppercase tracking-widest text-gray-400">
				<?php esc_html_e('Patches', 'jerseyplug'); ?>
			</span>
			<div class="grid grid-cols-2 gap-2">
				<?php foreach ($formatted_patches as $patch) :
					$p_slug  = esc_attr($patch['slug']);
					$p_name  = esc_attr($patch['name']);
					$p_price = (float) $patch['price'];
				?>
					<button
						type="button"
						@click="togglePatch({ slug: '<?php echo $p_slug; ?>', name: '<?php echo $p_name; ?>', price: <?php echo $p_price; ?> })"
						class="flex items-center justify-between rounded-xl border p-2.5 text-left transition-all duration-200"
						:class="isPatchSelected('<?php echo $p_slug; ?>')
							? 'border-primary bg-primary/5 shadow-sm'
							: 'border-gray-200 bg-white hover:border-gray-400'">
						<span class="text-[11px] font-bold text-gray-900 leading-tight">
							<?php echo esc_html($patch['name']); ?>
						</span>
						<span class="text-[10px] font-bold text-gray-400 shrink-0 pl-1">
							+R <?php echo esc_html(number_format($p_price, 0)); ?>
						</span>
					</button>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

	<!-- Hidden inputs to submit patches to cart -->
	<template x-for="p in selectedPatches" :key="p.slug">
		<input type="hidden" name="selected_patch[]" :value="JSON.stringify(p)" />
	</template>

	<!-- Summary Row -->
	<div
		x-show="customName.trim() !== '' || customNumber.trim() !== '' || selectedPatches.length > 0"
		x-cloak
		class="flex items-center justify-between pt-2 border-t border-gray-200 text-xs">
		<span class="text-gray-500 font-medium"><?php esc_html_e('Personalization total', 'jerseyplug'); ?></span>
		<span class="font-black text-primary" x-text="formatCurrency(singleProductPrice - basePrice)"></span>
	</div>
</div>
