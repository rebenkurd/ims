import { defineStore } from 'pinia'
import axios from '@/axios'

export const useReportStore = defineStore('report', {
  state: () => ({
    loading: false,
    data: [],
    meta: {
      links: [],
      from: null,
      to: null,
      current_page: 1,
      per_page: 10,
      total: null
    },
    filters: {
      date_range: {
        from: new Date().toISOString().split('T')[0],
        to: new Date().toISOString().split('T')[0]
      },
      search_query: '',
      status: 'all',
      supplier_id: null,
      customer_id: null,
      payment_method: null,
      category_id: null
    },
    current_report: null
  }),

  getters: {
    isLoading: (state) => state.loading,
    reportData: (state) => state.data,
    pagination: (state) => state.meta,
    currentFilters: (state) => state.filters,
    currentReportType: (state) => state.current_report,
    
    // Calculated values
    totalAmount: (state) => state.data.reduce((sum, item) => sum + parseFloat(item.total || 0), 0),
    totalPaid: (state) => state.data.reduce((sum, item) => sum + parseFloat(item.paid || 0), 0),
    totalDue: (state) => state.data.reduce((sum, item) => sum + parseFloat(item.due || 0), 0),
  },

  actions: {
    async fetchReport(reportType, params = {}, url = null) {
      this.loading = true
      this.current_report = reportType
      
      try {
        const endpoint = url || `/reports/${reportType}`
        const response = await axios.get(endpoint, {
          params: {
            ...this.filters,
            ...params,
            per_page: this.meta.per_page
          }
        })
        
        this.data = response.data.data
        this.meta = {
          links: response.data.meta?.links || [],
          from: response.data.meta?.from,
          to: response.data.meta?.to,
          current_page: response.data.meta?.current_page,
          per_page: response.data.meta?.per_page,
          total: response.data.meta?.total
        }
        
        return response.data
      } catch (error) {
        console.error(`Error fetching ${reportType} report:`, error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchPurchaseReport(params = {}) {
      return this.fetchReport('purchases', params)
    },

    async fetchSalesReport(params = {}) {
      return this.fetchReport('sales', params)
    },

    async fetchProfitLossReport(params = {}) {
      return this.fetchReport('profit-loss', params)
    },

    async fetchStockReport(params = {}) {
      return this.fetchReport('stock', params)
    },

    async fetchExpiredItemsReport(params = {}) {
      return this.fetchReport('expired-items', params)
    },

    async fetchPurchasePaymentsReport(params = {}) {
      return this.fetchReport('purchase-payments', params)
    },

    async fetchSalesPaymentsReport(params = {}) {
      return this.fetchReport('sales-payments', params)
    },

    updateFilters(newFilters) {
      this.filters = {
        ...this.filters,
        ...newFilters
      }
    },

    resetFilters() {
      this.filters = {
        date_range: {
          from: new Date().toISOString().split('T')[0],
          to: new Date().toISOString().split('T')[0]
        },
        search_query: '',
        status: 'all',
        supplier_id: null,
        customer_id: null,
        payment_method: null,
        category_id: null
      }
    },

    async changePage(url) {
      return this.fetchReport(this.current_report, {}, url)
    }
  }
})