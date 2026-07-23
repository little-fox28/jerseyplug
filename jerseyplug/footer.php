<?php

/**
 * Theme footer template.
 *
 * @package TailPress
 */
?>
</main>

<?php do_action('tailpress_content_end'); ?>
</div>

<?php do_action('tailpress_content_after'); ?>

<?php get_template_part('components/layout/footer'); ?>
</div>

<?php get_template_part('components/ui/floating-socials'); ?>

<!-- Alpine Toast Notification System -->
<!-- Inline toastSystem so it's available before Alpine.js deferred script initializes -->
<script>
	window.toastSystem = function() {
		return {
			toasts: [],
			addToast(message, type) {
				if (type === undefined) type = 'success';
				var id = Date.now() + Math.floor(Math.random() * 1000);
				this.toasts.push({ id: id, message: message, type: type, visible: true });
				var self = this;
				setTimeout(function() { self.removeToast(id); }, 3500);
			},
			removeToast(id) {
				var index = this.toasts.findIndex(function(t) { return t.id === id; });
				if (index !== -1) {
					this.toasts[index].visible = false;
					var self = this;
					setTimeout(function() {
						self.toasts = self.toasts.filter(function(t) { return t.id !== id; });
					}, 300);
				}
			}
		};
	};
</script>
<div
	x-data="toastSystem()"
	@notify.window="addToast($event.detail.message, $event.detail.type)"
	class="fixed bottom-4 left-4 right-4 flex flex-col items-center md:top-5 md:right-5 md:bottom-auto md:left-auto md:items-end gap-2 z-[9999] pointer-events-none">
	<template x-for="toast in toasts" :key="toast.id">
		<div
			x-show="toast.visible"
			x-transition:enter="transition ease-out duration-300 transform"
			x-transition:enter-start="opacity-0 translate-y-full md:translate-y-0 md:translate-x-full"
			x-transition:enter-end="opacity-100 translate-y-0 md:translate-x-0"
			x-transition:leave="transition ease-in duration-300 transform"
			x-transition:leave-start="opacity-100 translate-y-0 md:translate-x-0"
			x-transition:leave-end="opacity-0 translate-y-full md:translate-y-0 md:translate-x-full"
			class="pointer-events-auto flex items-start gap-3 p-4 bg-white shadow-xl rounded-lg border border-gray-100 w-full max-w-sm"
			:class="{
					'border-l-4 border-l-emerald-500 bg-emerald-50 text-emerald-900': toast.type === 'success',
					'border-l-4 border-l-rose-500 bg-rose-50 text-rose-900': toast.type === 'error',
					'border-l-4 border-l-slate-500 bg-slate-50 text-slate-900': toast.type === 'info' || toast.type === 'notice'
				}">
			<!-- Icon -->
			<div class="flex-shrink-0 mt-0.5">
				<template x-if="toast.type === 'success'">
					<svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
					</svg>
				</template>
				<template x-if="toast.type === 'error'">
					<svg class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
					</svg>
				</template>
				<template x-if="toast.type === 'info' || toast.type === 'notice'">
					<svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
					</svg>
				</template>
			</div>
			<!-- Message -->
			<div class="flex-1 text-sm font-medium leading-relaxed" x-html="toast.message"></div>
			<!-- Close Button -->
			<button @click="removeToast(toast.id)" class="flex-shrink-0 text-gray-400 hover:text-gray-600 focus:outline-none">
				<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
				</svg>
			</button>
		</div>
	</template>
</div>

<!-- Override native window.alert to use our Toast System -->
<script>
	(function() {
		var _originalAlert = window.alert;
		window.alert = function(message) {
			// We assume alerts from Woo are mostly errors (like out of stock, select variations)
			window.dispatchEvent(new CustomEvent('notify', {
				detail: {
					message: message,
					type: 'error'
				}
			}));
		};
	})();
</script>

<?php wp_footer(); ?>
</body>

</html>