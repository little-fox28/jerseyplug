/**
 * Vanilla JS shop filter module.
 *
 * Handles AJAX filtering, sorting, and pagination for the products page.
 * Uses Fetch API to request the filtered URL, parses the response HTML,
 * and swaps the product grid + pagination containers without a full reload.
 * Manages URL state via history.pushState for bookmarkable filter URLs.
 *
 * @package JerseyPlug
 */

;(function () {
    'use strict'

    // -----------------------------------------------------------------------
    // DOM references
    // -----------------------------------------------------------------------
    const page          = document.getElementById('products-page')
    if (!page) return // Not on the shop page.

    const gridContainer = document.getElementById('product-grid')
    const resultCount   = document.getElementById('result-count')
    const pagination    = document.getElementById('pagination-container')
    const loadingEl     = document.getElementById('grid-loading')
    const clearAllBtn   = document.getElementById('desktop-clear-all')
    const clearEmptyBtn = document.getElementById('clear-all-filters')

    // Mobile elements.
    const mobileToggle  = document.getElementById('mobile-filter-toggle')
    const drawer        = document.getElementById('mobile-filter-drawer')
    const overlay       = document.getElementById('mobile-filter-overlay')
    const drawerClose   = document.getElementById('mobile-drawer-close')
    const mobileReset   = document.getElementById('mobile-filter-reset')
    const mobileApply   = document.getElementById('mobile-filter-apply')

    // Sort selects (desktop + mobile).
    const desktopSort   = document.getElementById('desktop-shop-sort')
    const mobileSort    = document.getElementById('mobile-shop-sort')

    const shopUrl       = page.dataset.shopUrl || window.location.pathname

    // -----------------------------------------------------------------------
    // State — derived from current URL on init
    // -----------------------------------------------------------------------
    let isLoading = false

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /**
     * Collect all checked filter values from the page.
     * Returns an object like { filter_competition: [...], filter_team: [...], ... }
     * Deduplicates values since desktop and mobile filters share the same names.
     */
    function collectFilters() {
        const filters = {}

        // Checkbox-based filters (deduplicate across desktop + mobile).
        const checkboxes = page.querySelectorAll('input[data-filter]:checked')
        checkboxes.forEach(function (cb) {
            const name = cb.name
            if (!name) return

            if (cb.type === 'radio') {
                filters[name] = cb.value
            } else {
                if (!filters[name]) filters[name] = []
                if (filters[name].indexOf(cb.value) === -1) {
                    filters[name].push(cb.value)
                }
            }
        })

        // Sort value.
        const sortEl = desktopSort || mobileSort
        if (sortEl && sortEl.value && sortEl.value !== 'featured') {
            filters['sort'] = sortEl.value
        }

        return filters
    }


    /**
     * Build a URL string from the collected filters object.
     */
    function buildUrl(filters) {
        const params = new URLSearchParams()

        Object.keys(filters).forEach(function (key) {
            const value = filters[key]
            if (Array.isArray(value)) {
                value.forEach(function (v) {
                    params.append(key + '[]', v)
                })
            } else if (value) {
                params.set(key, value)
            }
        })

        const qs = params.toString()
        return shopUrl + (qs ? '?' + qs : '')
    }

    /**
     * Show / hide the loading overlay on the grid.
     */
    function setLoading(state) {
        isLoading = state

        if (gridContainer) {
            if (state) {
                gridContainer.style.opacity = '0.5'
                gridContainer.style.pointerEvents = 'none'
            } else {
                gridContainer.style.opacity = ''
                gridContainer.style.pointerEvents = ''
            }
        }

        if (loadingEl) {
            if (state) {
                loadingEl.classList.remove('hidden')
                loadingEl.classList.add('flex')
            } else {
                loadingEl.classList.add('hidden')
                loadingEl.classList.remove('flex')
            }
        }
    }

    // -----------------------------------------------------------------------
    // Core: Fetch + swap
    // -----------------------------------------------------------------------

    /**
     * Fetch the given URL, parse the HTML response, and swap
     * the #product-grid, #result-count, and #pagination-container.
     */
    async function fetchAndSwap(url, pushState) {
        if (isLoading) return

        setLoading(true)

        try {
            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })

            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status)
            }

            const html = await response.text()
            const parser = new DOMParser()
            const doc = parser.parseFromString(html, 'text/html')

            // Swap product grid.
            const newGrid = doc.getElementById('product-grid')
            if (newGrid && gridContainer) {
                gridContainer.innerHTML = newGrid.innerHTML
            }

            // Swap result count.
            const newCount = doc.getElementById('result-count')
            if (newCount && resultCount) {
                resultCount.innerHTML = newCount.innerHTML
            }

            // Swap pagination.
            const newPagination = doc.getElementById('pagination-container')
            if (pagination) {
                if (newPagination) {
                    pagination.innerHTML = newPagination.innerHTML
                    pagination.classList.remove('hidden')
                } else {
                    pagination.innerHTML = ''
                    pagination.classList.add('hidden')
                }
            }

            // Sync toolbar state
            // Sync [data-filter-count] badges
            doc.querySelectorAll('[data-filter-count]').forEach(function(newBadge) {
                const filterType = newBadge.dataset.filterCount
                const oldBadge = page.querySelector(`[data-filter-count="${filterType}"]`)
                if (oldBadge) {
                    oldBadge.className = newBadge.className
                    oldBadge.innerHTML = newBadge.innerHTML
                }
            })

            // Sync desktop clear button
            const newDesktopClear = doc.getElementById('desktop-clear-all')
            const oldDesktopClear = document.getElementById('desktop-clear-all')
            if (newDesktopClear && oldDesktopClear) {
                oldDesktopClear.className = newDesktopClear.className
                oldDesktopClear.innerHTML = newDesktopClear.innerHTML
            }

            // Sync mobile filter count
            const newMobileCount = doc.getElementById('mobile-filter-count')
            const oldMobileCount = document.getElementById('mobile-filter-count')
            if (newMobileCount && oldMobileCount) {
                oldMobileCount.className = newMobileCount.className
                oldMobileCount.innerHTML = newMobileCount.innerHTML
            }

            // Sync dropdown triggers active styling
            doc.querySelectorAll('[data-dropdown-trigger]').forEach(function(newTrigger, index) {
                const oldTriggers = page.querySelectorAll('[data-dropdown-trigger]')
                if (oldTriggers[index]) {
                    oldTriggers[index].className = newTrigger.className
                }
            })

            // Re-bind pagination links for AJAX.
            bindPaginationLinks()

            // Push URL state.
            if (pushState !== false) {
                window.history.pushState({ shopFilter: true }, '', url)
            }

            // Scroll to grid top.
            if (gridContainer) {
                gridContainer.scrollIntoView({ behavior: 'smooth', block: 'start' })
            }

        } catch (err) {
            console.error('JerseyPlug: Filter fetch error', err)
        } finally {
            setLoading(false)
        }
    }

    /**
     * Apply current filter state: collect filters, build URL, fetch + swap.
     */
    function applyFilters(pushState) {
        const filters = collectFilters()
        const url = buildUrl(filters)
        fetchAndSwap(url, pushState)
    }

    // -----------------------------------------------------------------------
    // Dropdown toggles (desktop filter dropdowns)
    // -----------------------------------------------------------------------

    function initDropdowns() {
        const dropdowns = page.querySelectorAll('[data-dropdown]')

        dropdowns.forEach(function (dropdown) {
            const trigger = dropdown.querySelector('[data-dropdown-trigger]')
            const panel   = dropdown.querySelector('[data-dropdown-panel]')
            if (!trigger || !panel) return

            trigger.addEventListener('click', function (e) {
                e.stopPropagation()
                const isOpen = !panel.classList.contains('hidden')

                // Close all other dropdowns first.
                closeAllDropdowns()

                if (!isOpen) {
                    panel.classList.remove('hidden')
                    trigger.querySelector('svg:last-child')?.classList.add('rotate-180')
                }
            })

            // Apply button inside dropdown.
            const applyBtn = dropdown.querySelector('[data-apply-group]')
            if (applyBtn) {
                applyBtn.addEventListener('click', function () {
                    panel.classList.add('hidden')
                    trigger.querySelector('svg:last-child')?.classList.remove('rotate-180')
                    applyFilters()
                })
            }

            // Reset button inside dropdown.
            const resetBtn = dropdown.querySelector('[data-reset-group]')
            if (resetBtn) {
                resetBtn.addEventListener('click', function () {
                    const group = resetBtn.dataset.resetGroup
                    if (group === 'price') {
                        // Uncheck all price radios.
                        dropdown.querySelectorAll('input[data-filter="price"]').forEach(function (r) {
                            r.checked = false
                            updateCheckVisual(r)
                        })
                    } else {
                        dropdown.querySelectorAll('input[data-filter="' + group + '"]').forEach(function (cb) {
                            cb.checked = false
                            updateCheckVisual(cb)
                        })
                    }
                    applyFilters()
                })
            }
        })

        // Close dropdowns when clicking outside.
        document.addEventListener('click', function (e) {
            if (!e.target.closest('[data-dropdown]')) {
                closeAllDropdowns()
            }
        })
    }

    function closeAllDropdowns() {
        page.querySelectorAll('[data-dropdown-panel]').forEach(function (panel) {
            panel.classList.add('hidden')
        })
        page.querySelectorAll('[data-dropdown-trigger] svg:last-child').forEach(function (svg) {
            svg.classList.remove('rotate-180')
        })
    }

    // -----------------------------------------------------------------------
    // Checkbox / radio visual sync
    // -----------------------------------------------------------------------

    /**
     * Update the custom visual checkbox/radio indicator for a given input.
     */
    function updateCheckVisual(input) {
        const label = input.closest('label')
        if (!label) return

        const visual = label.querySelector('[data-check-visual]')
        const icon   = label.querySelector('[data-check-icon]')
        const text   = label.querySelector('span:last-child')

        if (input.checked) {
            if (visual) {
                visual.classList.add('border-primary', 'bg-primary')
                visual.classList.remove('border-gray-300', 'bg-white')
            }
            if (icon) icon.classList.remove('hidden')
            if (text) {
                text.classList.add('font-bold', 'text-primary')
                text.classList.remove('text-gray-600')
            }
        } else {
            if (visual) {
                visual.classList.remove('border-primary', 'bg-primary')
                visual.classList.add('border-gray-300', 'bg-white')
            }
            if (icon) icon.classList.add('hidden')
            if (text) {
                text.classList.remove('font-bold', 'text-primary')
                text.classList.add('text-gray-600')
            }
        }
    }

    function initCheckboxes() {
        // Desktop + mobile drawer checkboxes.
        page.querySelectorAll('input[data-filter]').forEach(function (input) {
            input.addEventListener('change', function () {
                // For radios, uncheck siblings (toggle behavior).
                if (input.type === 'radio') {
                    const wasChecked = input.dataset.wasChecked === 'true'
                    if (wasChecked) {
                        input.checked = false
                        input.dataset.wasChecked = 'false'
                    } else {
                        // Uncheck all radios with the same name in this container.
                        const container = input.closest('[data-dropdown-panel], [data-accordion-panel]')
                        if (container) {
                            container.querySelectorAll('input[name="' + input.name + '"]').forEach(function (r) {
                                r.dataset.wasChecked = 'false'
                                updateCheckVisual(r)
                            })
                        }
                        input.dataset.wasChecked = 'true'
                    }
                }

                updateCheckVisual(input)
            })

            // Initialize wasChecked state for radios.
            if (input.type === 'radio' && input.checked) {
                input.dataset.wasChecked = 'true'
            }
        })
    }

    // -----------------------------------------------------------------------
    // Sort selects
    // -----------------------------------------------------------------------

    function initSort() {
        if (desktopSort) {
            desktopSort.addEventListener('change', function () {
                // Sync mobile sort.
                if (mobileSort) mobileSort.value = desktopSort.value
                applyFilters()
            })
        }

        if (mobileSort) {
            mobileSort.addEventListener('change', function () {
                // Sync desktop sort.
                if (desktopSort) desktopSort.value = mobileSort.value
                applyFilters()
            })
        }

        // Custom sort dropdown interaction
        page.querySelectorAll('[data-sort-option]').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault()
                const val = btn.dataset.sortOption
                
                // Visually update all sort buttons in the dropdown
                page.querySelectorAll('[data-sort-option]').forEach(function (otherBtn) {
                    if (otherBtn === btn) {
                        otherBtn.classList.add('bg-gray-50', 'font-bold', 'text-primary')
                        otherBtn.classList.remove('text-gray-600')
                        // Add checkmark SVG if it doesn't exist
                        if (!otherBtn.querySelector('svg')) {
                            otherBtn.insertAdjacentHTML('beforeend', '<svg class="h-4 w-4 text-primary" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>')
                        }
                    } else {
                        otherBtn.classList.remove('bg-gray-50', 'font-bold', 'text-primary')
                        otherBtn.classList.add('text-gray-600')
                        const svg = otherBtn.querySelector('svg')
                        if (svg) svg.remove()
                    }
                })

                // Update the trigger label text
                const labelEl = btn.closest('[data-dropdown]')?.querySelector('[data-sort-label]')
                if (labelEl) {
                    // Remove the checkmark SVG text content if it got caught in textContent
                    labelEl.textContent = btn.textContent.trim()
                }

                // Trigger the AJAX fetch
                if (desktopSort && desktopSort.value !== val) {
                    desktopSort.value = val
                    desktopSort.dispatchEvent(new Event('change'))
                }
                
                closeAllDropdowns()
            })
        })
    }

    // -----------------------------------------------------------------------
    // Clear all filters
    // -----------------------------------------------------------------------

    function clearAllFilters() {
        // Uncheck all filter inputs.
        page.querySelectorAll('input[data-filter]').forEach(function (input) {
            input.checked = false
            input.dataset.wasChecked = 'false'
            updateCheckVisual(input)
        })

        // Reset sort.
        if (desktopSort && desktopSort.value !== 'featured') {
            desktopSort.value = 'featured'
            // Visually reset custom sort dropdown
            const featuredBtn = page.querySelector('[data-sort-option="featured"]')
            if (featuredBtn) {
                page.querySelectorAll('[data-sort-option]').forEach(function (otherBtn) {
                    if (otherBtn === featuredBtn) {
                        otherBtn.classList.add('bg-gray-50', 'font-bold', 'text-primary')
                        otherBtn.classList.remove('text-gray-600')
                        if (!otherBtn.querySelector('svg')) {
                            otherBtn.insertAdjacentHTML('beforeend', '<svg class="h-4 w-4 text-primary" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>')
                        }
                    } else {
                        otherBtn.classList.remove('bg-gray-50', 'font-bold', 'text-primary')
                        otherBtn.classList.add('text-gray-600')
                        const svg = otherBtn.querySelector('svg')
                        if (svg) svg.remove()
                    }
                })
                const labelEl = featuredBtn.closest('[data-dropdown]')?.querySelector('[data-sort-label]')
                if (labelEl) labelEl.textContent = featuredBtn.textContent.trim()
            }
        }
        if (mobileSort) mobileSort.value = 'featured'

        // Reset size toggle buttons in drawer.
        page.querySelectorAll('[data-size-toggle]').forEach(function (btn) {
            btn.classList.remove('border-primary', 'bg-primary', 'text-white')
            btn.classList.add('border-gray-200', 'bg-white', 'text-gray-600')
        })

        applyFilters()
    }

    function initClearButtons() {
        page.addEventListener('click', function(e) {
            if (e.target.closest('#desktop-clear-all') || e.target.closest('#clear-all-filters')) {
                e.preventDefault()
                clearAllFilters()
            }
        })
    }

    // -----------------------------------------------------------------------
    // Mobile drawer
    // -----------------------------------------------------------------------

    function openDrawer() {
        if (!drawer || !overlay) return
        overlay.classList.remove('hidden')
        drawer.classList.remove('translate-x-full')
        drawer.classList.add('translate-x-0')
        document.body.style.overflow = 'hidden'
    }

    function closeDrawer() {
        if (!drawer || !overlay) return
        overlay.classList.add('hidden')
        drawer.classList.add('translate-x-full')
        drawer.classList.remove('translate-x-0')
        document.body.style.overflow = ''
    }

    function initDrawer() {
        if (mobileToggle) {
            mobileToggle.addEventListener('click', openDrawer)
        }
        if (drawerClose) {
            drawerClose.addEventListener('click', closeDrawer)
        }
        if (overlay) {
            overlay.addEventListener('click', closeDrawer)
        }
        if (mobileReset) {
            mobileReset.addEventListener('click', function () {
                clearAllFilters()
                closeDrawer()
            })
        }
        if (mobileApply) {
            mobileApply.addEventListener('click', function () {
                closeDrawer()
                applyFilters()
            })
        }
    }

    // -----------------------------------------------------------------------
    // Mobile drawer: Accordions
    // -----------------------------------------------------------------------

    function initAccordions() {
        const accordions = page.querySelectorAll('[data-accordion]')

        accordions.forEach(function (accordion) {
            const trigger = accordion.querySelector('[data-accordion-trigger]')
            const panel   = accordion.querySelector('[data-accordion-panel]')
            if (!trigger || !panel) return

            trigger.addEventListener('click', function () {
                const isOpen = !panel.classList.contains('hidden')
                panel.classList.toggle('hidden', isOpen)

                // Rotate chevron.
                const svg = trigger.querySelector('svg')
                if (svg) {
                    svg.classList.toggle('rotate-180', !isOpen)
                }
            })
        })
    }

    // -----------------------------------------------------------------------
    // Mobile drawer: Size toggle buttons
    // -----------------------------------------------------------------------

    function initSizeToggles() {
        page.querySelectorAll('[data-size-toggle]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const size = btn.dataset.sizeToggle
                // Find the matching hidden checkbox.
                const checkbox = drawer?.querySelector('input[data-filter="sizes"][value="' + size + '"]')
                if (!checkbox) return

                checkbox.checked = !checkbox.checked

                // Update button visual.
                if (checkbox.checked) {
                    btn.classList.add('border-primary', 'bg-primary', 'text-white')
                    btn.classList.remove('border-gray-200', 'bg-white', 'text-gray-600')
                } else {
                    btn.classList.remove('border-primary', 'bg-primary', 'text-white')
                    btn.classList.add('border-gray-200', 'bg-white', 'text-gray-600')
                }
            })
        })
    }

    // -----------------------------------------------------------------------
    // Pagination: intercept links for AJAX
    // -----------------------------------------------------------------------

    function bindPaginationLinks() {
        const paginationContainer = document.getElementById('pagination-container')
        if (!paginationContainer) return

        paginationContainer.querySelectorAll('a.page-numbers').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault()
                const href = link.getAttribute('href')
                if (href) {
                    fetchAndSwap(href, true)
                }
            })
        })
    }

    // -----------------------------------------------------------------------
    // Browser back/forward (popstate)
    // -----------------------------------------------------------------------

    function initPopstate() {
        window.addEventListener('popstate', function () {
            fetchAndSwap(window.location.href, false)
        })
    }

    // -----------------------------------------------------------------------
    // Init
    // -----------------------------------------------------------------------

    function init() {
        initDropdowns()
        initCheckboxes()
        initSort()
        initClearButtons()
        initDrawer()
        initAccordions()
        initSizeToggles()
        bindPaginationLinks()
        initPopstate()
    }

    // Run on DOM ready.
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init)
    } else {
        init()
    }
})()
