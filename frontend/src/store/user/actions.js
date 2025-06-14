import axiosClient from '@/axios.js';


export async function getUsers(state,url=null,search='',perPage=10,sortField,sortDirection){
state.users.loading = true;
  try{
    url = url || '/users'
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
        state.users={
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
    state.users.loading=false;
    console.error("Failed to get users:", error);
    throw error;
  }
}


export async function createUser(user){

  if(user.image instanceof File){
    const form = new FormData();
    form.append('name', user.name);
    form.append('username', user.username);
    form.append('email', user.email);
    form.append('password', user.password);
    form.append('phone', user.phone);
    form.append('mobile', user.mobile);
    form.append('dob', user.dob);
    form.append('role_id', user.role_id);
    form.append('image', user.image);
    user = form;
  }
  return await axiosClient.post('/users',user)
}

export async function updateUser(user){
  const id = user.id;
  if(user.image instanceof File){
    const form = new FormData();
    form.append('id', user.id);
    form.append('name', user.name);
    form.append('username', user.username);
    form.append('email', user.email);
    form.append('password', user.password);
    form.append('phone', user.phone);
    form.append('mobile', user.mobile);
    form.append('dob', user.dob);
    form.append('role_id', user.role_id);
    form.append('image', user.image);
    form.append('_method', 'PUT'); 
    
    user = form;
  }else{
    user._method = 'PUT';
  }
  return await axiosClient.post(`/users/${id}`,user)

}

export async function deleteUser(id){
  return await axiosClient.delete(`/users/${id}`)

}

export async function getUser(id){

  return await axiosClient.get(`/users/${id}`)

}
  
