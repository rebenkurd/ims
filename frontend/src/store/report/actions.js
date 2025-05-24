import axios from '@/axios'

export default {
  async fetchReport({ state, commit }, { reportType, params = {}, url = null }) {
    commit('SET_LOADING', true)
    commit('SET_CURRENT_REPORT', reportType)
    
    try {
      const endpoint = url || `/reports/${reportType}`
      const response = await axios.get(endpoint, {
        params: {
          ...state.filters,
          ...params,
          per_page: state.meta.per_page
        }
      })
      
      commit('SET_REPORT_DATA', response.data.data)
      commit('SET_PAGINATION', {
        links: response.data.meta?.links || [],
        from: response.data.meta?.from,
        to: response.data.meta?.to,
        current_page: response.data.meta?.current_page,
        per_page: response.data.meta?.per_page,
        total: response.data.meta?.total
      })
      
      return response.data
    } catch (error) {
      console.error(`Error fetching ${reportType} report:`, error)
      throw error
    } finally {
      commit('SET_LOADING', false)
    }
  },

  async fetchPurchaseReport({ dispatch }, params = {}) {
    return dispatch('fetchReport', { 
      reportType: this.state.report_types.PURCHASE,
      params 
    })
  },

  async fetchSalesReport({ dispatch }, params = {}) {
    return dispatch('fetchReport', { 
      reportType: this.state.report_types.SALES,
      params 
    })
  },

  // Other specific report actions...
  async fetchProfitLossReport({ dispatch }, params = {}) {
    return dispatch('fetchReport', { 
      reportType: this.state.report_types.PROFIT_LOSS,
      params 
    })
  },

  updateFilters({ commit }, filters) {
    commit('UPDATE_FILTERS', filters)
  },

  resetFilters({ commit }) {
    commit('RESET_FILTERS')
  },

  changePage({ dispatch, state }, url) {
    return dispatch('fetchReport', {
      reportType: state.current_report,
      url
    })
  }
}