import router from '../router';
import axiosClient from './../axios';

export async function getUser(state) {
  try {
    await axiosClient.get('/user')
  .then((response)=>{
      state.user.data = response.data.user;
      state.user.role_id = response.data.user.role_id;            
      return response;
    })
  } catch (error) {
    console.error("Login failed:", error);
    throw error;
  }
}

export async function login(state, data) {
  try {
    const response = await axiosClient.post('/login', data);
        // Set all user data at once
        state.user = {
            token: response.data.token,
            data: response.data.user,
            role_id: response.data.role_id
        };
            
    sessionStorage.setItem('token', response.data.token);
    return response.data;
  } catch (error) {
    console.error("Login failed:", error);
    throw error;
  }
}

export async function logout(state) {
  try {
   const response = await axiosClient.get('/logout');    
        state.user = {
            token: null,
            data: {},
            role_id: null
        };
    sessionStorage.clear()
    return response;
  } catch (error) {
    console.error("Logout failed:", error);
    throw error;
  }
}


  

