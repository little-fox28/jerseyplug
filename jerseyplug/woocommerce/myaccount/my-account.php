<?php
/**
 * My Account page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="max-w-7xl mx-auto py-4 sm:py-8 w-full">
    <!-- Tiêu đề trang -->
    <h1 class="text-3xl font-extrabold text-primary tracking-tight mb-8"><?php esc_html_e( 'My Account', 'woocommerce' ); ?></h1>

    <style>
        @keyframes tab-fade-in {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-tab-fade {
            animation: tab-fade-in 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
    </style>

    <div class="flex flex-col md:flex-row gap-8 lg:gap-12 items-start">
        
        <!-- SIDEBAR NAV (Điều hướng) -->
        <?php do_action( 'woocommerce_account_navigation' ); ?>

        <!-- MAIN CONTENT AREA -->
        <div class="woocommerce-MyAccount-content w-full flex-1 animate-tab-fade pb-24 md:pb-0">
            <?php do_action( 'woocommerce_account_content' ); ?>
        </div>
        
    </div>
</div>
