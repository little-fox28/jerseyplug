<?php
/**
 * Cart Page — JerseyPlug WooCommerce Template Override
 *
 * Faithfully ported from CartPage.jsx.
 * Retains all WooCommerce action hooks for plugin compatibility.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.9.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );

// ── Shipping progress bar calculations ──────────────────────────────────────
$shipping_threshold     = 2000; // ZAR threshold matching React
$subtotal_raw           = (float) WC()->cart->subtotal;
$amount_to_free         = max( 0, $shipping_threshold - $subtotal_raw );
$progress_pct           = min( 100, ( $subtotal_raw / $shipping_threshold ) * 100 );
$cart_is_empty          = WC()->cart->is_empty();
?>

<div class="min-h-screen bg-white">



    <div class="container mx-auto px-4 py-6 lg:py-10">
      <div class="flex flex-col lg:flex-row gap-12 max-w-6xl mx-auto">

        <?php /* ════════════════════════════════════════
               LEFT COLUMN — Cart form & items
               ════════════════════════════════════════ */ ?>
        <div class="flex-1">

          <h1 class="text-3xl font-bold text-[#163300] mb-8"><?php esc_html_e( 'Shopping Cart', 'woocommerce' ); ?></h1>

          <?php /* ── Free Shipping Progress Bar ── */ ?>
          <div class="mb-8 bg-gray-50 p-4 rounded-xl border border-gray-200">
            <?php if ( $amount_to_free > 0 ) : ?>
              <p class="text-sm text-gray-600 mb-2">
                <?php
                printf(
                  /* translators: %s: currency amount */
                  esc_html__( 'Spend %s more for %s', 'woocommerce' ),
                  '<span class="font-bold text-[#163300]">' . wp_kses_post( wc_price( $amount_to_free ) ) . '</span>',
                  '<span class="text-[#65cf21] font-bold uppercase">' . esc_html__( 'Free Delivery', 'woocommerce' ) . '</span>'
                );
                ?>
              </p>
            <?php else : ?>
              <p class="text-sm text-[#163300] font-bold mb-2 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="#65cf21" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 3h15v13H1z"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/>
                  <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
                <?php esc_html_e( "You've unlocked Free Delivery!", 'woocommerce' ); ?>
              </p>
            <?php endif; ?>
            <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
              <div class="h-full bg-[#65cf21] transition-all duration-500 ease-out"
                   style="width: <?php echo esc_attr( $progress_pct ); ?>%;"></div>
            </div>
          </div>

          <?php /* ── WooCommerce Cart Form ── */ ?>
          <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
            <?php do_action( 'woocommerce_before_cart_table' ); ?>

            <div class="woocommerce-cart-form__contents border-t border-gray-100">
              <?php do_action( 'woocommerce_before_cart_contents' ); ?>

              <?php
              foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
                $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
                $product_name = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );

                if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 ) continue;
                if ( ! apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) continue;

                $product_permalink = apply_filters(
                  'woocommerce_cart_item_permalink',
                  $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '',
                  $cart_item,
                  $cart_item_key
                );
                ?>

                <div class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?> flex gap-3 py-6 border-b border-gray-100 last:border-0 group/item">

                  <?php /* — Product Thumbnail — */ ?>
                  <div class="relative bg-gray-100 rounded-lg overflow-hidden shrink-0 w-24 h-32 md:w-32 md:h-40">
                    <?php
                    $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail', [ 'class' => 'w-full h-full object-cover' ] ), $cart_item, $cart_item_key );
                    if ( ! $product_permalink ) {
                      echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    } else {
                      printf( '<a href="%s" class="block w-full h-full">%s</a>', esc_url( $product_permalink ), $thumbnail ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    }
                    ?>
                  </div>

                  <?php /* — Product Details — */ ?>
                  <div class="flex-1 flex flex-col justify-between">
                    <div>
                      <div class="flex justify-between items-start gap-2">

                        <?php /* Product name + meta */ ?>
                        <div class="flex-1">
                          <h3 class="font-bold text-[#163300] line-clamp-2 text-base md:text-lg">
                            <?php
                            if ( ! $product_permalink ) {
                              echo wp_kses_post( $product_name . '&nbsp;' );
                            } else {
                              echo wp_kses_post( apply_filters(
                                'woocommerce_cart_item_name',
                                sprintf( '<a href="%s" class="hover:text-[#65cf21] transition-colors">%s</a>', esc_url( $product_permalink ), $_product->get_name() ),
                                $cart_item,
                                $cart_item_key
                              ) );
                            }
                            do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );
                            ?>
                          </h3>

                          <?php /* Variation / meta data */ ?>
                          <div class="text-xs text-gray-500 mt-1">
                            <?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore ?>
                          </div>

                          <?php /* Backorder notice */ ?>
                          <?php if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) : ?>
                            <p class="text-xs text-amber-600 mt-1"><?php esc_html_e( 'Available on backorder', 'woocommerce' ); ?></p>
                          <?php endif; ?>
                        </div>

                        <?php /* — Remove Button (Desktop) — */ ?>
                        <div class="hidden md:block shrink-0">
                          <?php
                          echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            'woocommerce_cart_item_remove_link',
                            sprintf(
                              '<a href="%s"
                                 class="remove text-gray-400 hover:text-red-500 transition-colors block p-1"
                                 aria-label="%s"
                                 data-product_id="%s"
                                 data-product_sku="%s">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                  <path d="M3 6h18"/>
                                  <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                  <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                  <line x1="10" y1="11" x2="10" y2="17"/>
                                  <line x1="14" y1="11" x2="14" y2="17"/>
                                </svg>
                              </a>',
                              esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                              /* translators: %s: product name */
                              esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
                              esc_attr( $product_id ),
                              esc_attr( $_product->get_sku() )
                            ),
                            $cart_item_key
                          );
                          ?>
                        </div>

                      </div>
                    </div>

                    <?php /* — Quantity stepper + line subtotal — */ ?>
                    <div class="flex justify-between items-end mt-3">

                      <?php /* Quantity */ ?>
                      <div class="flex items-center border border-gray-200 rounded-lg h-10 overflow-hidden bg-white">
                        <?php
                        if ( $_product->is_sold_individually() ) {
                          $min_qty = $max_qty = 1;
                        } else {
                          $min_qty = 0;
                          $max_qty = $_product->get_max_purchase_quantity();
                        }

                        $qty_html = woocommerce_quantity_input(
                          [
                            'input_name'   => "cart[{$cart_item_key}][qty]",
                            'input_value'  => $cart_item['quantity'],
                            'max_value'    => $max_qty,
                            'min_value'    => $min_qty,
                            'product_name' => $product_name,
                            'classes'      => apply_filters(
                              'woocommerce_quantity_input_classes',
                              [ 'input-text', 'qty', 'text', 'w-12', 'text-center', 'font-bold', 'text-sm', 'border-0', 'focus:outline-none', 'bg-transparent' ],
                              $_product
                            ),
                          ],
                          $_product,
                          false
                        );
                        echo apply_filters( 'woocommerce_cart_item_quantity', $qty_html, $cart_item_key, $cart_item ); // phpcs:ignore
                        ?>
                      </div>

                      <?php /* Line subtotal */ ?>
                      <div class="text-right">
                        <span class="font-bold text-[#163300] text-base md:text-lg">
                          <?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore ?>
                        </span>
                      </div>

                    </div>

                    <?php /* — Remove Button (Mobile) — */ ?>
                    <div class="md:hidden mt-3">
                      <?php
                      echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        'woocommerce_cart_item_remove_link',
                        sprintf(
                          '<a href="%s"
                             class="remove text-xs text-red-500 flex items-center gap-1"
                             aria-label="%s"
                             data-product_id="%s"
                             data-product_sku="%s">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                              <path d="M3 6h18"/>
                              <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                              <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                              <line x1="10" y1="11" x2="10" y2="17"/>
                              <line x1="14" y1="11" x2="14" y2="17"/>
                            </svg>
                            %s
                          </a>',
                          esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                          /* translators: %s: product name */
                          esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
                          esc_attr( $product_id ),
                          esc_attr( $_product->get_sku() ),
                          esc_html__( 'Remove', 'woocommerce' )
                        ),
                        $cart_item_key
                      );
                      ?>
                    </div>

                  </div>
                </div>

              <?php endforeach; // end cart items loop ?>

              <?php do_action( 'woocommerce_cart_contents' ); ?>

              <?php /* ── Coupon & Update Cart ── */ ?>
              <div class="actions flex flex-col sm:flex-row justify-between items-center pt-6 pb-2 gap-4">

                <?php if ( wc_coupons_enabled() ) : ?>
                  <div class="coupon flex w-full sm:w-auto">
                    <label for="coupon_code" class="sr-only"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label>
                    <input type="text"
                           name="coupon_code"
                           id="coupon_code"
                           value=""
                           placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>"
                           class="input-text w-full sm:w-48 border border-gray-300 rounded-l-lg px-4 py-2 text-sm
                                  focus:outline-none focus:border-[#163300] transition-colors bg-white" />
                    <button type="submit"
                            name="apply_coupon"
                            value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"
                            class="button bg-[#163300] text-white font-bold px-4 py-2 text-sm rounded-r-lg
                                   hover:bg-black transition-colors whitespace-nowrap">
                      <?php esc_html_e( 'Apply', 'woocommerce' ); ?>
                    </button>
                    <?php do_action( 'woocommerce_cart_coupon' ); ?>
                  </div>
                <?php endif; ?>

                <button type="submit"
                        name="update_cart"
                        value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"
                        class="button w-full sm:w-auto border border-gray-300 rounded-lg px-6 py-2 text-sm
                               font-bold text-gray-700 hover:bg-gray-100 hover:text-black transition-colors
                               disabled:opacity-50">
                  <?php esc_html_e( 'Update cart', 'woocommerce' ); ?>
                </button>

                <?php do_action( 'woocommerce_cart_actions' ); ?>
                <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
              </div>

              <?php do_action( 'woocommerce_after_cart_contents' ); ?>
            </div>

            <?php do_action( 'woocommerce_after_cart_table' ); ?>

            <?php /* ── Mobile Checkout Button ── */ ?>
            <div class="lg:hidden mt-8">
              <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>"
                 class="w-full bg-[#163300] text-white font-bold py-4 rounded-xl shadow-lg
                        flex items-center justify-center gap-2 hover:bg-[#65cf21] hover:text-[#163300]
                        transition-colors">
                <?php esc_html_e( 'Checkout', 'woocommerce' ); ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                  <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                </svg>
              </a>
            </div>

          </form>

          <?php do_action( 'woocommerce_after_cart_form' ); ?>
        </div>

        <?php /* ════════════════════════════════════════
               RIGHT COLUMN — Order Summary (Sticky)
               ════════════════════════════════════════ */ ?>
        <div class="hidden lg:block w-96 shrink-0">
          <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200 sticky top-24">

            <h3 class="font-bold text-lg text-[#163300] mb-4">
              <?php esc_html_e( 'Order Summary', 'woocommerce' ); ?>
            </h3>

            <?php /* Cart totals (subtotal, shipping, coupon rows, total) */ ?>
            <div class="cart-collaterals wc-cart-collaterals">
              <?php
              /**
               * Cart collaterals hook.
               *
               * @hooked woocommerce_cross_sell_display
               * @hooked woocommerce_cart_totals - 10
               */
              do_action( 'woocommerce_cart_collaterals' );
              ?>
            </div>

            <?php /* ── Proceed to Checkout CTA ── */ ?>
            <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>"
               class="w-full mt-4 bg-[#163300] hover:bg-black text-white font-bold py-4 rounded-xl
                      shadow-lg flex items-center justify-center gap-2 transition-all active:scale-95 group">
              <?php esc_html_e( 'Proceed to Checkout', 'woocommerce' ); ?>
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                   fill="none" stroke="currentColor" stroke-width="2.5"
                   stroke-linecap="round" stroke-linejoin="round"
                   class="group-hover:translate-x-1 transition-transform">
                <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
              </svg>
            </a>

            <?php /* ── Guaranteed Safe Checkout & Payment Logos ── */ ?>
            <div class="mt-6 pt-6 border-t border-gray-200">
              <p class="text-[10px] text-gray-400 text-center mb-3 uppercase tracking-wider font-bold">
                <?php esc_html_e( 'Guaranteed Safe Checkout', 'woocommerce' ); ?>
              </p>
              <div class="flex justify-center gap-2 opacity-70 grayscale hover:grayscale-0 transition-all">
                <div class="h-6 px-2 bg-white border border-gray-200 rounded flex items-center text-[10px] font-bold text-gray-700">Visa</div>
                <div class="h-6 px-2 bg-white border border-gray-200 rounded flex items-center text-[10px] font-bold text-gray-700">Mastercard</div>
                <div class="h-6 px-2 bg-white border border-red-200 rounded flex items-center text-[10px] font-bold text-red-600">PayFast</div>
                <div class="h-6 px-2 bg-black border border-black rounded flex items-center text-[10px] font-bold text-[#65cf21]">PayJustNow</div>
              </div>
              <div class="mt-4 flex items-center justify-center gap-1.5 text-[11px] text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                  <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <?php esc_html_e( 'SSL Secured Checkout', 'woocommerce' ); ?>
              </div>
            </div>

          </div>
        </div>
        <?php /* end right column */ ?>

      </div>
    </div>

</div>

<?php do_action( 'woocommerce_after_cart' ); ?>