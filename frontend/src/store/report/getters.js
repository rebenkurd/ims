export default {
  totalAmount: (state) => {
    const items = state[state.filters.report_type === 'purchase' ? 'purchases' : 
                 state.filters.report_type === 'sale' ? 'sales' : 
                 state.filters.report_type === 'expiredProducts' ? 'expiredProducts' : 
                 'worldBankReports'];
    return items.reduce((sum, item) => sum + parseFloat(item.total_amount || item.price || 0), 0);
  },
  
  activeReportData: (state) => {
    switch(state.filters.report_type) {
      case 'purchase': return state.purchases;
      case 'sale': return state.sales;
      case 'expiredProducts': return state.expiredProducts;
      case 'worldBank': return state.worldBankReports;
      default: return [];
    }
  }
};