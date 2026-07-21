<?php

/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$current_user = wp_get_current_user();
?>
<div class="space-y-6">
    <!-- Lời chào chuyên nghiệp -->
    <div class="bg-white border border-gray-200 rounded-[2rem] p-8 md:p-10 shadow-sm relative overflow-hidden">
        <!-- Hiệu ứng bóng trang trí -->
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-green-50 rounded-full opacity-50 blur-3xl pointer-events-none"></div>

        <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight mb-3">
            <?php
            /* translators: %s: User display name */
            printf(esc_html( jerseyplug_pll( 'Hello, %s!' ) ), esc_html($current_user->display_name));
            ?>
        </h2>
        <p class="text-gray-500 font-medium max-w-xl leading-relaxed">
            <?php
            printf(
                wp_kses(
                    /* translators: 1: Orders URL 2: Address URL 3: Account URL. */
                    jerseyplug_pll( 'From your account dashboard you can view your <a href="%1$s" class="text-primary hover:underline font-bold">recent orders</a>, manage your <a href="%2$s" class="text-primary hover:underline font-bold">shipping and billing addresses</a>, and <a href="%3$s" class="text-primary hover:underline font-bold">edit your password and account details</a>.' ),
                    array(
                        'a' => array(
                            'href' => array(),
                            'class' => array(),
                        ),
                    )
                ),
                esc_url(wc_get_endpoint_url('orders')),
                esc_url(wc_get_endpoint_url('edit-address')),
                esc_url(wc_get_endpoint_url('edit-account'))
            );
            ?>
        </p>
    </div>

    <!-- Quick Links (Lưới 3 cột) -->
    <div class="grid grid-cols-3 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-6">

        <!-- Order Card -->
        <a href="<?php echo esc_url(wc_get_endpoint_url('orders')); ?>" class="bg-white border border-gray-100 rounded-2xl sm:rounded-[2rem] p-3 sm:p-6 hover:shadow-lg hover:border-gray-200 transition-all cursor-pointer group flex flex-col items-center sm:items-start text-center sm:text-left gap-2 sm:gap-4">
            <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-extrabold text-[11px] sm:text-lg text-slate-900 mb-0 sm:mb-1"><?php echo esc_html( jerseyplug_pll( 'Orders' ) ); ?></h3>
                <p class="hidden sm:block text-sm text-gray-500 font-medium"><?php echo esc_html( jerseyplug_pll( 'Check your order status & history.' ) ); ?></p>
            </div>
        </a>

        <!-- Address Card -->
        <a href="<?php echo esc_url(wc_get_endpoint_url('edit-address')); ?>" class="bg-white border border-gray-100 rounded-2xl sm:rounded-[2rem] p-3 sm:p-6 hover:shadow-lg hover:border-gray-200 transition-all cursor-pointer group flex flex-col items-center sm:items-start text-center sm:text-left gap-2 sm:gap-4">
            <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.242-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-extrabold text-[11px] sm:text-lg text-slate-900 mb-0 sm:mb-1"><?php echo esc_html( jerseyplug_pll( 'Addresses' ) ); ?></h3>
                <p class="hidden sm:block text-sm text-gray-500 font-medium"><?php echo esc_html( jerseyplug_pll( 'Manage your delivery locations.' ) ); ?></p>
            </div>
        </a>

        <!-- Account Card -->
        <a href="<?php echo esc_url(wc_get_endpoint_url('edit-account')); ?>" class="bg-white border border-gray-100 rounded-2xl sm:rounded-[2rem] p-3 sm:p-6 hover:shadow-lg hover:border-gray-200 transition-all cursor-pointer group flex flex-col items-center sm:items-start text-center sm:text-left gap-2 sm:gap-4">
            <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-extrabold text-[11px] sm:text-lg text-slate-900 mb-0 sm:mb-1"><?php echo esc_html( jerseyplug_pll( 'Details' ) ); ?></h3>
                <p class="hidden sm:block text-sm text-gray-500 font-medium"><?php echo esc_html( jerseyplug_pll( 'Update password & profile info.' ) ); ?></p>
            </div>
        </a>

        <?php
        /**
         * My Account dashboard.
         *
         * @since 2.6.0
         */
        do_action('woocommerce_account_dashboard');
        ?>
    </div>