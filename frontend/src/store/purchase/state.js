
const state = ()=>({
    purchases: {
        loading:true,
        data:[],
        links:[],
        from:null,
        to:null,
        page:1,
        limit:null,
        total:null,
    },
    currentPurchase: null,
    currentInvoice: null,
  
})

export default state;