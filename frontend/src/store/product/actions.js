import axiosClient from '@/axios.js';


export async function getProducts(state,url=null,search='',perPage=10,sortField,sortDirection){
state.products.loading = true;
  try{
    url = url || '/products'
    await axiosClient.get(url,{
      params: {
        search,
        per_page: perPage,
        sort_field: sortField,
        sort_direction: sortDirection,
      }
    })
    .then(({data})=>{
      if(data){
        state.products={
          loading: false,
          data: data.data,
          links: data.meta?.links || [],
          to: data.meta?.to,
          from: data.meta?.from,
          page: data.meta?.current_page,
          limit: data.meta?.per_page,
          total: data.meta?.total,

        }
      }
      return data
    })

  }catch(error){
    state.products.loading=false;
    console.error("Failed to get products:", error);
    throw error;
  }
}


export async function createProduct(product){

  if(product.image instanceof File){
    const form = new FormData();
    form.append('code', product.code);
    form.append('name', product.name);
    form.append('brand_id', product.brand_id);
    form.append('category_id', product.category_id);
    form.append('per_carton', product.per_carton);
    form.append('minimum_qty', product.minimum_qty);
    form.append('barcode', product.barcode);
    form.append('expire_date', product.expire_date);
    form.append('image', product.image);
    form.append('description', product.description);
    form.append('purchase_price', product.purchase_price);
    form.append('sales_price', product.sales_price); 
    form.append('final_price', product.final_price); 
    form.append('profit_margin', product.profit_margin); 
    form.append('discount_type', product.discount_type); 
    form.append('discount', product.discount); 
    form.append('current_opening_stock', product.current_opening_stock); 
    form.append('adjust_stock', product.adjust_stock); 
    form.append('adjustment_note', product.adjustment_note); 
    product = form;
  }
  return await axiosClient.post('/products',product)
}

export async function updateProduct(product){
  const id = product.id;
  if(product.image instanceof File){
    const form = new FormData();
    form.append('id', product.id);
    form.append('code', product.code);
    form.append('name', product.name);
    form.append('brand_id', product.brand_id);
    form.append('category_id', product.category_id);
    form.append('per_carton', product.per_carton);
    form.append('minimum_qty', product.minimum_qty);
    form.append('barcode', product.barcode);
    form.append('expire_date', product.expire_date);
    form.append('image', product.image);
    form.append('description', product.description);
    form.append('purchase_price', product.purchase_price);
    form.append('sales_price', product.sales_price); 
    form.append('final_price', product.final_price); 
    form.append('profit_margin', product.profit_margin); 
    form.append('discount_type', product.discount_type); 
    form.append('discount', product.discount); 
    form.append('current_opening_stock', product.current_opening_stock); 
    form.append('adjust_stock', product.adjust_stock); 
    form.append('adjustment_note', product.adjustment_note); 
    form.append('_method', 'PUT'); 
    
    product = form;
  }else{
    product._method = 'PUT';
  }
  return await axiosClient.post(`/products/${id}`,product)

}

export async function deleteProduct(id){
  return await axiosClient.delete(`/products/${id}`)

}

export async function updateProductStatus(product){
  return await axiosClient.get(`/product_status_update/${product.id}`,product)
}


export async function getProduct(id){

  return await axiosClient.get(`/products/${id}`)

}
  





export async function fetchItems(state) {
  state.isLoading = true
  try {
    const response = await axiosClient.get('/api/items')
    state.items = response.data.data
    state.error = null
  } catch (error) {
    state.error = error.message || 'Failed to fetch items'
  } finally {
    state.isLoading = false
  }
}


export async function searchItems(state,query) {
  state.loading = true
  try {
    const response = await axiosClient.get(`/api/items/search?q=${query}`)
    state.searchResults = response.data.data
    state.error = null
  } catch (error) {
    state.error = error.message || 'Failed to search items'
    state.searchResults = []
  } finally {
    state.loading = false
  }
}

export async function clearSearch(state) {
  state.searchResults = []
}











