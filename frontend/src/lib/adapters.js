export const transformWPProduct = (data) => {
  if (!data) return null;

  return {
    id: data.id,
    name: data.name,
    slug: data.slug,
    image: data.main_image?.full || data.main_image || '/placeholder.jpg',
    breakpoints: {
      s300: data.images?.thumb || null,
      s600: data.images?.medium || null,
    },
    category: data.categories?.[0] || "Jersey",
    price: data.price || 0,
    regular_price: data.regular_price,
    sale_price: data.sale_price,
    on_sale: data.on_sale,
    description: data.description,
    gallery: data.gallery || [],
    variations: data.variations || [],
    total_stock: data.total_stock,
    season: data.season,
    allow_print: data.allow_print,
    print_price: data.print_price,
    patches: data.patches || [],
    sold: data.sold || 0,
    rating: data.rating || 5.0
  };
};