export default {
  isLoading: (state) => state.loading,
  reportData: (state) => state.data,
  pagination: (state) => state.meta,
  currentFilters: (state) => state.filters,
  currentReportType: (state) => state.current_report,
  
  // Specific data getters
  totalAmount: (state) => state.data.reduce((sum, item) => sum + parseFloat(item.total || 0), 0),
  totalPaid: (state) => state.data.reduce((sum, item) => sum + parseFloat(item.paid || 0), 0),
  totalDue: (state) => state.data.reduce((sum, item) => sum + parseFloat(item.due || 0), 0),
  
  // Report-specific getters
  purchaseReportData: (state) => state.current_report === state.report_types.PURCHASE ? state.data : [],
  salesReportData: (state) => state.current_report === state.report_types.SALES ? state.data : []
}