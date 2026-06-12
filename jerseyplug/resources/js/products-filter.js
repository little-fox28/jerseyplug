/**
 * Alpine.js component for the products page filter/sort/pagination.
 *
 * Registered as `Alpine.data('productsFilter', ...)` and mounted on
 * the <main> element of products-page.php via `x-data="productsFilter"`.
 *
 * @package JerseyPlug
 */

document.addEventListener('alpine:init', () => {
    Alpine.data('productsFilter', () => ({
        // --- State ---
        selectedCompetitions: [],
        selectedTeams: [],
        selectedVersions: [],
        selectedSizes: [],
        selectedPriceRange: null,
        sortBy: 'featured',

        currentPage: 1,
        maxPages: 1,
        perPage: 12,
        totalProducts: 0,
        displayedCount: 0,

        loading: false,
        loadingMore: false,
        drawerOpen: false,

        ajaxUrl: '',
        nonce: '',

        // --- Computed ---
        get totalFilters() {
            return (
                this.selectedCompetitions.length +
                this.selectedTeams.length +
                this.selectedVersions.length +
                this.selectedSizes.length +
                (this.selectedPriceRange ? 1 : 0)
            )
        },

        // --- Init ---
        init() {
            const el = this.$el
            this.ajaxUrl = el.dataset.ajaxUrl || ''
            this.nonce = el.dataset.nonce || ''
            this.perPage = parseInt(el.dataset.perPage, 10) || 12
            this.totalProducts = parseInt(el.dataset.total, 10) || 0
            this.maxPages = parseInt(el.dataset.maxPages, 10) || 1

            // Count initial server-rendered cards.
            this.$nextTick(() => {
                const grid = this.$refs.productGrid
                if (grid) {
                    this.displayedCount = grid.children.length
                }
            })

            // Restore filters from URL query params.
            this.restoreFromUrl()
        },

        // --- Filter toggle ---
        toggleFilter(type, value) {
            const map = {
                competitions: 'selectedCompetitions',
                teams: 'selectedTeams',
                versions: 'selectedVersions',
                sizes: 'selectedSizes',
            }

            const key = map[type]
            if (!key) return

            const arr = this[key]
            const idx = arr.indexOf(value)
            if (idx > -1) {
                arr.splice(idx, 1)
            } else {
                arr.push(value)
            }
        },

        // --- Clear all filters ---
        clearAllFilters() {
            this.selectedCompetitions = []
            this.selectedTeams = []
            this.selectedVersions = []
            this.selectedSizes = []
            this.selectedPriceRange = null
            this.applyFilters()
        },

        // --- Apply filters (full refresh) ---
        async applyFilters() {
            this.currentPage = 1
            this.loading = true

            try {
                const result = await this.fetchProducts(1)
                const grid = this.$refs.productGrid
                if (grid) {
                    grid.innerHTML = result.html
                }
                this.totalProducts = result.total
                this.maxPages = result.max_pages
                this.displayedCount = grid ? grid.children.length : 0
                this.pushToUrl()
            } catch (err) {
                console.error('JerseyPlug: Filter error', err)
            } finally {
                this.loading = false
            }
        },

        // --- Load more ---
        async loadMore() {
            if (this.loadingMore || this.currentPage >= this.maxPages) return

            this.loadingMore = true
            const nextPage = this.currentPage + 1

            try {
                const result = await this.fetchProducts(nextPage)
                const grid = this.$refs.productGrid
                if (grid && result.html) {
                    // Create a temporary container and append children.
                    const temp = document.createElement('div')
                    temp.innerHTML = result.html
                    while (temp.firstChild) {
                        grid.appendChild(temp.firstChild)
                    }
                }
                this.currentPage = nextPage
                this.totalProducts = result.total
                this.maxPages = result.max_pages
                this.displayedCount = grid ? grid.children.length : 0
            } catch (err) {
                console.error('JerseyPlug: Load more error', err)
            } finally {
                this.loadingMore = false
            }
        },

        // --- Fetch products from AJAX endpoint ---
        async fetchProducts(page) {
            const body = new FormData()
            body.append('action', 'jerseyplug_filter_products')
            body.append('nonce', this.nonce)
            body.append('page', page)
            body.append('per_page', this.perPage)
            body.append('sort_by', this.sortBy)

            if (this.selectedPriceRange) {
                body.append('price_range', this.selectedPriceRange)
            }

            this.selectedCompetitions.forEach((v) => body.append('competitions[]', v))
            this.selectedTeams.forEach((v) => body.append('teams[]', v))
            this.selectedVersions.forEach((v) => body.append('versions[]', v))
            this.selectedSizes.forEach((v) => body.append('sizes[]', v))

            const response = await fetch(this.ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                body,
            })

            const json = await response.json()

            if (!json.success) {
                throw new Error(json.data?.message || 'Unknown error')
            }

            return json.data
        },

        // --- URL state sync ---
        pushToUrl() {
            const params = new URLSearchParams()

            if (this.selectedCompetitions.length) {
                params.set('competitions', this.selectedCompetitions.join(','))
            }
            if (this.selectedTeams.length) {
                params.set('teams', this.selectedTeams.join(','))
            }
            if (this.selectedVersions.length) {
                params.set('versions', this.selectedVersions.join(','))
            }
            if (this.selectedSizes.length) {
                params.set('sizes', this.selectedSizes.join(','))
            }
            if (this.selectedPriceRange) {
                params.set('price', this.selectedPriceRange)
            }
            if (this.sortBy && this.sortBy !== 'featured') {
                params.set('sort', this.sortBy)
            }

            const qs = params.toString()
            const url = window.location.pathname + (qs ? '?' + qs : '')
            window.history.replaceState(null, '', url)
        },

        restoreFromUrl() {
            const params = new URLSearchParams(window.location.search)

            const competitions = params.get('competitions')
            if (competitions) {
                this.selectedCompetitions = competitions.split(',')
            }

            const teams = params.get('teams')
            if (teams) {
                this.selectedTeams = teams.split(',')
            }

            const versions = params.get('versions')
            if (versions) {
                this.selectedVersions = versions.split(',')
            }

            const sizes = params.get('sizes')
            if (sizes) {
                this.selectedSizes = sizes.split(',')
            }

            const price = params.get('price')
            if (price) {
                this.selectedPriceRange = price
            }

            const sort = params.get('sort')
            if (sort) {
                this.sortBy = sort
            }

            // If any filters were restored from the URL, apply them.
            if (this.totalFilters > 0 || (sort && sort !== 'featured')) {
                this.applyFilters()
            }
        },
    }))
})
