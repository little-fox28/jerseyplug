function jerseyplugOpenCartDrawer(trigger) {
    if (!trigger) {
        return false
    }

    const source = trigger.dataset.cartDrawerTrigger || 'header'

    if (window.jerseyplugCartDrawer && typeof window.jerseyplugCartDrawer.open === 'function') {
        window.jerseyplugCartDrawer.open({ source })
        return true
    }

    if (window.jerseyplugCartDrawerStore && typeof window.jerseyplugCartDrawerStore.getState === 'function') {
        const state = window.jerseyplugCartDrawerStore.getState()
        if (state && typeof state.open === 'function') {
            state.open({ source })
            return true
        }
    }

    const event = new CustomEvent('jerseyplug:cart-drawer-open', {
        bubbles: true,
        cancelable: true,
        detail: { source },
    })

    document.dispatchEvent(event)
    return event.defaultPrevented
}

function jerseyplugInitHomeSlider() {
    const sections = document.querySelectorAll('[data-home-slider]')

    sections.forEach((section) => {
        const slides = Array.from(section.querySelectorAll('[data-home-slide]'))
        const indicators = Array.from(section.querySelectorAll('[data-home-slider-dot]'))

        if (!slides.length) {
            return
        }

        const reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches
        let current = 0
        let interval = null

        const syncIndicators = (index) => {
            indicators.forEach((button, idx) => {
                const span = button.querySelector('span')
                if (!span) {
                    return
                }

                if (idx === index) {
                    span.classList.add('w-8', 'bg-secondary')
                    span.classList.remove('w-2', 'bg-white/50')
                } else {
                    span.classList.add('w-2', 'bg-white/50')
                    span.classList.remove('w-8', 'bg-secondary')
                }
            })
        }

        const showSlide = (index) => {
            slides.forEach((slide, idx) => {
                slide.classList.toggle('opacity-100', idx === index)
                slide.classList.toggle('opacity-0', idx !== index)
                slide.setAttribute('aria-hidden', idx === index ? 'false' : 'true')
            })

            syncIndicators(index)
            current = index
        }

        const stop = () => {
            if (interval) {
                window.clearInterval(interval)
                interval = null
            }
        }

        const start = () => {
            if (reducedMotion || slides.length < 2 || interval) {
                return
            }

            interval = window.setInterval(() => {
                showSlide((current + 1) % slides.length)
            }, 5000)
        }

        indicators.forEach((button, index) => {
            button.addEventListener('click', (event) => {
                event.preventDefault()
                showSlide(index)
                stop()
                start()
            })
        })

        section.addEventListener('mouseenter', stop)
        section.addEventListener('mouseleave', start)

        showSlide(0)
        start()
    })
}

window.addEventListener('load', function () {
    let mainNavigation = document.getElementById('primary-navigation')
    let mainNavigationToggle = document.getElementById('primary-menu-toggle')

    if (mainNavigation && mainNavigationToggle) {
        mainNavigationToggle.addEventListener('click', function (e) {
            e.preventDefault()
            mainNavigation.classList.toggle('hidden')
        })
    }

    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('[data-cart-drawer-trigger]')
        if (!trigger) {
            return
        }

        const handled = jerseyplugOpenCartDrawer(trigger)
        if (handled) {
            event.preventDefault()
        }
    })

    jerseyplugInitHomeSlider()
})

// WooCommerce AJAX Listeners
if (typeof jQuery !== 'undefined') {
    jQuery(document.body).on('added_to_cart', function(event, fragments, cart_hash, $button) {
        let productName = 'Product';
        if ($button && $button.attr('aria-label')) {
            const aria = $button.attr('aria-label');
            // aria-label format: "Add &ldquo;Product Name&rdquo; to your cart"
            const match = aria.match(/&ldquo;(.*?)&rdquo;/);
            if (match && match[1]) {
                productName = match[1];
            } else {
                // Try fallback logic
                const possibleName = aria.replace(/Add /i, '').replace(/ to your cart/i, '').replace(/['"]/g, '').trim();
                if (possibleName) productName = possibleName;
            }
        } else if ($button && $button.data('product_title')) {
            productName = $button.data('product_title');
        }

        window.dispatchEvent(new CustomEvent('notify', {
            detail: { message: `${productName} successfully added to your cart.`, type: 'success' }
        }));
    });

    jQuery(document.body).on('checkout_error', function() {
        // WooCommerce usually displays errors in a div.woocommerce-error on the page
        // We can just show a generic error toast to direct their attention
        window.dispatchEvent(new CustomEvent('notify', {
            detail: { message: 'There was an error processing your checkout. Please check the form.', type: 'error' }
        }));
    });

    // Custom Coupon Handler for Cart Page
    jQuery(document.body).on('click', 'button[name="apply_coupon"]', function(e) {
        e.preventDefault();
        var $button = jQuery(this);
        var $couponInput = jQuery('#coupon_code');
        var couponCode = $couponInput.val().trim();

        if (!couponCode) return;

        $button.prop('disabled', true).addClass('opacity-50');

        var ajaxUrl = (typeof wc_cart_params !== 'undefined' && wc_cart_params.wc_ajax_url) 
            ? wc_cart_params.wc_ajax_url.replace('%%endpoint%%', 'apply_coupon') 
            : '/?wc-ajax=apply_coupon';

        var nonce = (typeof wc_cart_params !== 'undefined') ? wc_cart_params.apply_coupon_nonce : '';

        jQuery.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: {
                security: nonce,
                coupon_code: couponCode
            },
            dataType: 'html',
            success: function(response) {
                // Reload page so all totals, notices, and session states update perfectly
                window.location.reload();
            },
            error: function() {
                window.location.reload();
            }
        });
    });

    jQuery(document.body).on('keydown', '#coupon_code', function(e) {
        if (e.which === 13 || e.keyCode === 13) {
            e.preventDefault();
            jQuery('button[name="apply_coupon"]').trigger('click');
        }
    });
}
