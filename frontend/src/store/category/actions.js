import axiosClient from '@/axios.js';


export async function getCategories(state,url=null,search='',perPage=10,sortField,sortDirection){
state.categories.loading = true;
  try{
    url = url || '/categories'
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
        state.categories={
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
    state.categories.loading=false;
    console.error("Failed to get categories:", error);
    throw error;
  }
}


export async function createCategory(category){

  if(category.image instanceof File){
    const form = new FormData();
    form.append('name', category.name);
    form.append('description', category.description);
    category = form;
  }
  return await axiosClient.post('/categories',category)
}

export async function updateCategory(category){
  const id = category.id;
  const form = new FormData();
  form.append('id', category.id);
  form.append('name', category.name);
  form.append('description', category.description);
  form.append('_method', 'PUT'); 
  category = form;
  return await axiosClient.post(`/categories/${id}`,category)
}

export async function deleteCategory(id){
  return await axiosClient.delete(`/categories/${id}`)

}

export async function getCategory(id){
  return await axiosClient.get(`/categories/${id}`)
}
  


// get categories for selections
export async function getCategorys(state){
  state.categories.loading = true;
    try{
      await axiosClient.get('/get_category')
      .then(({data})=>{
        if(data){
          state.categories={
            loading: false,
            data: data.data,
          }
        }
        return data
      })
  
      // debugger
    }catch(error){
      state.categories.loading=false;
      console.error("Failed to get categories:", error);
      throw error;
    }
  }