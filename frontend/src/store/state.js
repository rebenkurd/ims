
const state = ()=>({
        user: {
            token: sessionStorage.getItem('token'),
            data:{}
        },
      
    })

export default state;