import axiosClient from './../axios';

export async function getUser(state) {
  try {
    await axiosClient.get('/user')
  .then((response)=>{
      state.user.data = response.data.user;
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
    state.user.data = response.data.user;
    state.user.token = response.data.token;
    sessionStorage.setItem('token', response.data.token);
    return response.data;
  } catch (error) {
    console.error("Login failed:", error);
    throw error;
  }
}

export async function logout(state) {
  try {
    await axiosClient.get('/logout');    
    state.token = null;
    state.data = {};
    sessionStorage.removeItem('token');
  } catch (error) {
    console.error("Logout failed:", error);
    throw error;
  }
}


  

