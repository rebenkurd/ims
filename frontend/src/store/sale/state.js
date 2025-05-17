const state = () => ({
    sales: {
      loading: true,
      data: [],
      links: [],
      from: null,
      to: null,
      page: 1,
      limit: null,
      total: null,
    },
    currentSale: null,
    currentInvoice: null,
  })
  
  export default state;