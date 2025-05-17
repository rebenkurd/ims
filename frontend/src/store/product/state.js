
const state = ()=>({
    products: {
        loading:true,
        searchResults: [],
        error: null,
        data:[],
        links:[],
        from:null,
        to:null,
        page:1,
        limit:null,
        total:null,
    },
  
})

export default state;