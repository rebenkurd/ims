import axiosClient from '../../axios.js';


export async function getBrands(state,url=null,search='',perPage=10,sortField,sortDirection){
state.brands.loading = true;
  try{
    url = url || '/brands'
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
        state.brands={
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
    state.brands.loading=false;
    console.error("Failed to get brands:", error);
    throw error;
  }
}


export async function createBrand(brand){

    const form = new FormData();
    form.append('name', brand.name);
    form.append('description', brand.description);
    brand = form;
    return await axiosClient.post('/brands',brand)
}

export async function updateBrand(brand){
  const id = brand.id;
  const form = new FormData();
  form.append('id', brand.id);
  form.append('name', brand.name);
  form.append('description', brand.description);
  form.append('_method', 'PUT'); 
  brand = form;
  return await axiosClient.post(`/brands/${id}`,brand)
}

export async function deleteBrand(id){
  return await axiosClient.delete(`/brands/${id}`)

}

export async function getBrand(id){
  return await axiosClient.get(`/brands/${id}`)
}
  


// get brands for selections
export async function getBrandds(state){
  state.brands.loading = true;
    try{
      await axiosClient.get('/get_brands')
      .then(({data})=>{
        if(data){
          state.brands={
            loading: false,
            data: data.data,
          }
        }
        return data
      })
  
      // debugger
    }catch(error){
      state.brands.loading=false;
      console.error("Failed to get brands:", error);
      throw error;
    }
  }