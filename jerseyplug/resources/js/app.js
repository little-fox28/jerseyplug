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
})
