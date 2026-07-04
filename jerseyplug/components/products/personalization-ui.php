<?php
/**
 * Product Personalization UI (Name & Number)
 *
 * @package JerseyPlug
 */

defined('ABSPATH') || exit;
?>

<!-- Custom Name & Number UI (Always Visible, Alpine-bound) -->
<div class="rounded-2xl border border-gray-100 bg-zinc-50 p-4 space-y-4">
	<h3 class="text-xs font-black uppercase tracking-widest text-gray-500">
		<?php esc_html_e('Personalization Details', 'jerseyplug'); ?>
	</h3>
	<div class="grid grid-cols-3 gap-3">
		<div class="col-span-2">
			<label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5" for="custom_name_display">
				<?php esc_html_e('Name', 'jerseyplug'); ?>
			</label>
			<input
				id="custom_name_display"
				type="text"
				x-model="customName"
				maxlength="12"
				placeholder="MESSI"
				autocomplete="off"
				class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-xs font-bold uppercase tracking-wider text-gray-900 placeholder:text-gray-300 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors" />
		</div>
		<div>
			<label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5" for="custom_number_display">
				<?php esc_html_e('Number', 'jerseyplug'); ?>
			</label>
			<input
				id="custom_number_display"
				type="text"
				x-model="customNumber"
				maxlength="2"
				placeholder="10"
				autocomplete="off"
				class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-xs font-bold text-gray-900 placeholder:text-gray-300 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors" />
		</div>
	</div>
</div>
