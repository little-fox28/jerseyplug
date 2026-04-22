/**
 * Product data structured for WooCommerce compatibility.
 * Fields match the WooCommerce REST API schema where applicable.
 */

export const getProducts = (t) => [
  {
    id: 1,
    sku: "JP-ARG-2024-M10",
    name: t("products.argentinaHome"),
    slug: "argentina-home-jersey-2024-messi-10",
    type: "simple",
    status: "publish",
    featured: true,
    catalog_visibility: "visible",
    description: "Authentic Argentina Home Jersey for the 2024 season, featuring Messi 10 printing.",
    short_description: "The official 2024 Argentina Home Jersey.",
    price: "1999",
    regular_price: "1999",
    sale_price: "",
    on_sale: false,
    purchasable: true,
    total_sales: 150,
    virtual: false,
    downloadable: false,
    stock_status: "instock",
    stock_quantity: 45,
    weight: "0.2",
    dimensions: { length: "30", width: "20", height: "2" },
    average_rating: "5.0",
    rating_count: 88,
    categories: [
      { id: 1, name: t("products.types.national"), slug: "national" }
    ],
    tags: [
      { id: 1, name: t("products.tags.bestSeller"), slug: "best-seller" }
    ],
    images: [
      {
        id: 1,
        src: "https://images.unsplash.com/photo-1511252998634-1181285db21d?auto=format&fit=crop&q=80&w=800",
        name: "Argentina Home Jersey",
        alt: t("products.argentinaHome")
      }
    ],
    // UI specific fields (mapped for current components)
    image: "https://images.unsplash.com/photo-1511252998634-1181285db21d?auto=format&fit=crop&q=80&w=800",
    category: t("products.types.national"),
    tag: t("products.tags.bestSeller")
  },
  {
    id: 2,
    sku: "JP-NIKE-PREM-2025",
    name: t("products.premierBall"),
    slug: "nike-premier-match-ball-2025",
    type: "simple",
    status: "publish",
    featured: false,
    description: "Nike Premier Match Ball for the 2025 season. Professional grade.",
    short_description: "Professional match ball.",
    price: "2499",
    regular_price: "2499",
    sale_price: "",
    on_sale: false,
    stock_status: "instock",
    average_rating: "5.0",
    rating_count: 42,
    categories: [
      { id: 2, name: t("products.types.accessories"), slug: "accessories" }
    ],
    tags: [
      { id: 2, name: t("products.tags.new"), slug: "new" }
    ],
    image: "https://images.unsplash.com/photo-1614632537423-1e6c2e7e0aab?auto=format&fit=crop&q=80&w=800",
    category: t("products.types.accessories"),
    tag: t("products.tags.new")
  },
  {
    id: 3,
    sku: "JP-RM-HOME-2425",
    name: t("products.realMadridHome"),
    slug: "real-madrid-home-kit-24-25",
    type: "simple",
    status: "publish",
    description: "Real Madrid Home Kit for the 24/25 season.",
    price: "1499",
    regular_price: "1499",
    sale_price: "",
    on_sale: false,
    stock_status: "instock",
    average_rating: "5.0",
    rating_count: 120,
    categories: [
      { id: 3, name: t("header.leagues.laLiga"), slug: "la-liga" }
    ],
    image: "https://images.unsplash.com/photo-1551966775-a4ddc8df052b?auto=format&fit=crop&q=80&w=800",
    category: t("header.leagues.laLiga")
  },
  {
    id: 4,
    sku: "JP-PRO-CARB-SHIN",
    name: t("products.shinGuards"),
    slug: "pro-carbon-shin-guards",
    type: "simple",
    status: "publish",
    description: "Professional carbon fiber shin guards for maximum protection.",
    price: "699",
    regular_price: "699",
    stock_status: "instock",
    average_rating: "5.0",
    rating_count: 56,
    categories: [
      { id: 2, name: t("products.types.accessories"), slug: "accessories" }
    ],
    image: "https://images.unsplash.com/photo-1517466787929-bc90951d6dbb?auto=format&fit=crop&q=80&w=800",
    category: t("products.types.accessories")
  }
];

export const getNewArrivals = (t) => [
  {
    id: 101,
    sku: "JP-ITA-HOME-2024",
    name: t("products.italyHome"),
    slug: "italy-home-2024",
    type: "simple",
    status: "publish",
    price: "1699",
    regular_price: "1699",
    stock_status: "instock",
    average_rating: "5.0",
    rating_count: 34,
    categories: [
      { id: 1, name: t("products.types.national"), slug: "national" }
    ],
    tags: [
      { id: 2, name: t("products.tags.new"), slug: "new" }
    ],
    image: "https://images.unsplash.com/photo-1577212017184-80cc0da11082?auto=format&fit=crop&q=80&w=800",
    category: t("products.types.national"),
    tag: t("products.tags.new")
  },
  {
    id: 102,
    sku: "JP-PUMA-FUTURE-ULT",
    name: t("products.pumaBoots"),
    slug: "puma-future-ultimate",
    type: "simple",
    price: "3999",
    regular_price: "3999",
    stock_status: "instock",
    average_rating: "5.0",
    rating_count: 15,
    categories: [
      { id: 4, name: t("products.types.boots"), slug: "boots" }
    ],
    tags: [
      { id: 3, name: t("products.tags.hot"), slug: "hot" }
    ],
    image: "https://images.unsplash.com/photo-1511886929837-354d827aae26?auto=format&fit=crop&q=80&w=800",
    category: t("products.types.boots"),
    tag: t("products.tags.hot")
  },
  {
    id: 103,
    sku: "JP-BAY-AWAY-2425",
    name: t("products.bayernAway"),
    slug: "bayern-munich-away-24-25",
    type: "simple",
    price: "1499",
    regular_price: "1499",
    stock_status: "instock",
    average_rating: "5.0",
    rating_count: 22,
    categories: [
      { id: 5, name: t("header.leagues.bundesliga"), slug: "bundesliga" }
    ],
    tags: [
      { id: 2, name: t("products.tags.new"), slug: "new" }
    ],
    image: "https://images.unsplash.com/photo-1615144670267-33e387c244c7?auto=format&fit=crop&q=80&w=800",
    category: t("header.leagues.bundesliga"),
    tag: t("products.tags.new")
  },
  {
    id: 104,
    sku: "JP-VN-TRAIN-KIT",
    name: t("products.vietnamTraining"),
    slug: "vietnam-training-kit",
    type: "simple",
    price: "699",
    regular_price: "699",
    stock_status: "instock",
    average_rating: "5.0",
    rating_count: 95,
    categories: [
      { id: 1, name: t("products.types.national"), slug: "national" }
    ],
    tags: [
      { id: 4, name: t("products.tags.limited"), slug: "limited" }
    ],
    image: "https://images.unsplash.com/photo-1560272564-c83b66b1ad12?auto=format&fit=crop&q=80&w=800",
    category: t("products.types.national"),
    tag: t("products.tags.limited")
  }
];
