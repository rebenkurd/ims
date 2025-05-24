
const state = ()=>({
        user: {
            token: sessionStorage.getItem('token') || null,
            data:{},
            role_id:1,
        },
      
    })

export default state;