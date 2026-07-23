<?php

/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 */

if (! defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_before_account_navigation');
?>

<aside class="woocommerce-MyAccount-navigation fixed bottom-0 left-0 right-0 z-[100] bg-white border-t border-gray-200 shadow-[0_-10px_20px_rgba(0,0,0,0.05)] md:relative md:w-64 lg:w-1/4 md:flex-shrink-0 md:bg-transparent md:border-0 md:shadow-none p-0 overflow-hidden md:overflow-visible">
    <nav class="flex md:flex-col justify-between md:justify-start gap-1 md:gap-2 px-2 py-3 md:p-0 overflow-x-auto hide-scrollbar w-full">
        <?php foreach (wc_get_account_menu_items() as $endpoint => $label) : ?>
            <?php
            // Skip downloads if required
            if ('downloads' === $endpoint) {
                continue;
            }

            $classes = wc_get_account_menu_item_classes($endpoint);
            $is_active = strpos($classes, 'is-active') !== false;

            // Set icons based on endpoint
            $icon = '';
            if ('dashboard' === $endpoint) {
                $icon = '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>';
            } elseif ('orders' === $endpoint) {
                $icon = '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>';
            } elseif ('edit-address' === $endpoint) {
                $icon = '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.242-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>';
            } elseif ('edit-account' === $endpoint) {
                $icon = '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>';
            } elseif ('customer-logout' === $endpoint) {
                $icon = '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>';
            } else {
                $icon = '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
            }

            if ('customer-logout' === $endpoint) : ?>
                <a href="<?php echo esc_url(wc_logout_url(wc_get_page_permalink('myaccount'))); ?>" class="flex flex-col md:flex-row items-center justify-center md:justify-start gap-1 md:gap-3 px-1 md:px-5 py-2 md:py-3.5 rounded-xl font-bold text-[10px] md:text-sm transition-all text-center md:text-left !text-red-500 hover:bg-red-50 hover:text-red-700 md:mt-2 border border-transparent hover:border-red-100 flex-1 md:flex-none">
                    <?php echo $icon; ?>
                    <span class="truncate w-full"><?php echo esc_html( jerseyplug_pll( $label ) ); ?></span>
                </a>
            <?php elseif ('dashboard' === $endpoint) : ?>
                <!-- Nút Dashboard trên Desktop -->
                <a href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>"
                    class="hidden md:flex flex-row items-center justify-start gap-3 px-5 py-3.5 rounded-xl font-bold text-sm transition-all text-left flex-none <?php echo $is_active ? 'bg-primary shadow-md' : 'text-gray-500 hover:bg-gray-100 hover:text-slate-900'; ?>"
                    <?php if ($is_active) echo 'style="color: #f2c86c;"'; ?>>
                    <?php echo $icon; ?>
                    <span class="truncate w-full"><?php echo esc_html( jerseyplug_pll( $label ) ); ?></span>
                </a>
                
                <!-- Nút Home trên Mobile -->
                <a href="<?php echo esc_url(home_url('/')); ?>"
                    class="flex md:hidden flex-col items-center justify-center gap-1 px-1 py-2 rounded-xl font-bold text-[10px] transition-all text-center flex-1 text-gray-500 hover:bg-gray-100 hover:text-slate-900">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="truncate w-full"><?php echo esc_html( jerseyplug_pll( 'Home' ) ); ?></span>
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>"
                    class="flex flex-col md:flex-row items-center justify-center md:justify-start gap-1 md:gap-3 px-1 md:px-5 py-2 md:py-3.5 rounded-xl font-bold text-[10px] md:text-sm transition-all text-center md:text-left flex-1 md:flex-none <?php echo $is_active ? 'bg-primary shadow-md' : 'text-gray-500 hover:bg-gray-100 hover:text-slate-900'; ?>"
                    <?php if ($is_active) echo 'style="color: #f2c86c;"'; ?>>
                    <?php echo $icon; ?>
                    <span class="truncate w-full"><?php echo esc_html( jerseyplug_pll( $label ) ); ?></span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
</aside>

<?php do_action('woocommerce_after_account_navigation'); ?>