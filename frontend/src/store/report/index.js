import { defineStore } from 'pinia';
import axiosClient from '../../axios';

export const useReportStore = defineStore('report', {
  state: () => ({
    loading: false,
    purchases: [],
    sales: [],
    expiredProducts: [],
    worldBankReports: [], // New state for World Bank reports
    pagination: {
      current_page: 1,
      per_page: 10,
      total: 0,
      links: []
    },
    filters: {
      from_date: new Date().toISOString().split('T')[0],
      to_date: new Date().toISOString().split('T')[0],
      supplier_id: '',
      customer_id: '',
      category_id: '',
      search: '',
      status: 'expired',
      report_type: 'purchase', // Tracks active report type
      // New filters for World Bank reports
      report_date: '',
      world_bank_report_type: 'all'
    }
  }),

  getters: {
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
  },

  actions: {
    // Purchase report method
    async fetchPurchases(url = null) {
      this.filters.report_type = 'purchase';
      this.loading = true;
      try {
        const endpoint = url || '/purchase-report';
        const response = await axiosClient.get(endpoint, {
          params: {
            from_date: this.filters.from_date,
            to_date: this.filters.to_date,
            supplier_id: this.filters.supplier_id,
            search: this.filters.search,
            per_page: this.pagination.per_page
          }
        });
        this.purchases = response.data.data;
        this.updatePagination(response.data);
      } catch (error) {
        console.error('Failed to fetch purchases:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // Sale report method
    async fetchSales(url = null) {
      this.filters.report_type = 'sale';
      this.loading = true;
      try {
        const endpoint = url || '/sale-report';
        const response = await axiosClient.get(endpoint, {
          params: {
            from_date: this.filters.from_date,
            to_date: this.filters.to_date,
            customer_id: this.filters.customer_id,
            search: this.filters.search,
            per_page: this.pagination.per_page
          }
        });
        this.sales = response.data.data;
        this.updatePagination(response.data);
      } catch (error) {
        console.error('Failed to fetch sales:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // Expired products report method
    async fetchExpiredProducts() {
      this.filters.report_type = 'expiredProducts';
      this.loading = true;
      try {
        const response = await axiosClient.get('/expired-products-report', {
          params: {
            search: this.filters.search,
            to_date: this.filters.to_date,
            status: this.filters.status,
            category_id: this.filters.category_id
          }
        });
        this.expiredProducts = response.data;
        // Reset pagination for expired products since we show all
        this.pagination = {
          current_page: 1,
          per_page: response.data.length,
          total: response.data.length,
          links: []
        };
      } catch (error) {
        console.error('Failed to fetch expired products:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // World Bank reports method
    async fetchWorldBankReports(url = null) {
      this.filters.report_type = 'worldBank';
      this.loading = true;
      try {
        const endpoint = url || '/world-bank-reports';
        const response = await axiosClient.get(endpoint, {
          params: {
            report_date: this.filters.report_date,
            search: this.filters.search,
            report_type: this.filters.world_bank_report_type,
            per_page: this.pagination.per_page
          }
        });
        this.worldBankReports = response.data.data;
        this.updatePagination(response.data);
      } catch (error) {
        console.error('Failed to fetch World Bank reports:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // Common methods
    updatePagination(responseData) {
      this.pagination = {
        current_page: responseData.current_page,
        per_page: responseData.per_page,
        total: responseData.total,
        links: responseData.links || []
      };
    },

    updateFilters(newFilters) {
      this.filters = { ...this.filters, ...newFilters };
    },

    resetFilters() {
      this.filters = {
        from_date: new Date().toISOString().split('T')[0],
        to_date: new Date().toISOString().split('T')[0],
        supplier_id: '',
        customer_id: '',
        category_id: '',
        search: '',
        status: 'expired',
        report_type: this.filters.report_type,
        // World Bank specific filters
        report_date: '',
        world_bank_report_type: 'all'
      };
    },

    changePage(url) {
      if (!url || this.loading) return;
      
      switch(this.filters.report_type) {
        case 'purchase':
          this.fetchPurchases(url);
          break;
        case 'sale':
          this.fetchSales(url);
          break;
        case 'expiredProducts':
          this.fetchExpiredProducts();
          break;
        case 'worldBank':
          this.fetchWorldBankReports(url);
          break;
      }
    }
  }
});