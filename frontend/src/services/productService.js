import { api } from "@/lib/axios";
import { transformWPProduct } from "@/lib/adapters";


export const trendingProduct = async () => {
    try {
  
        const response = await api.get("/products", {
            params: { per_page: 8 }
        });

        const items = response.data?.data; 

        if (items && Array.isArray(items)) {
            return items.map(transformWPProduct);
        }
        return [];
    } catch (error) {
        console.error("Link gọi bị lỗi:", error.config.url); 
        return [];
    }
}
export const getDetailProduct = async (slug) => {
    try {
        const response = await api.get(`/products/${slug}`);
        
        if (response.data) {
            return transformWPProduct(response.data);
        }
        return null;
    } catch (error) {
        console.error("Fetch Product Detail Error:", error);
        throw error;
    }
    
};
export const getRelatedProducts = async (productId, lang = 'en') => {
    try {
        const response = await api.get(`/related/${productId}`, {
            params: { lang: lang.toLowerCase() }
        });
        const items = response.data; 
        if (items && Array.isArray(items)) {
            return items.map(transformWPProduct); 
        }
        return [];
    } catch (error) {
        console.error("Related Products API Error:", error);
        return [];
    }
};