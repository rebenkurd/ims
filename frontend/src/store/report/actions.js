import axiosClient from '@/axios';

export async function fetchPurchases(store, url = null) {
  store.filters.report_type = 'purchase';
  store.loading = true;
  try {
    const endpoint = url || '/purchase-report';
    const response = await axiosClient.get(endpoint, {
      params: {
        from_date: store.filters.from_date,
        to_date: store.filters.to_date,
        supplier_id: store.filters.supplier_id,
        search: store.filters.search,
        per_page: store.pagination.per_page
      }
    });
    store.purchases = response.data.data;
    updatePagination(store, response.data);
  } catch (error) {
    console.error('Failed to fetch purchases:', error);
    throw error;
  } finally {
    store.loading = false;
  }
}

export async function fetchSales(store, url = null) {
  store.filters.report_type = 'sale';
  store.loading = true;
  try {
    const endpoint = url || '/sale-report';
    const response = await axiosClient.get(endpoint, {
      params: {
        from_date: store.filters.from_date,
        to_date: store.filters.to_date,
        customer_id: store.filters.customer_id,
        search: store.filters.search,
        per_page: store.pagination.per_page
      }
    });
    store.sales = response.data.data;
    updatePagination(store, response.data);
  } catch (error) {
    console.error('Failed to fetch sales:', error);
    throw error;
  } finally {
    store.loading = false;
  }
}

export async function fetchExpiredProducts(store) {
  store.filters.report_type = 'expiredProducts';
  store.loading = true;
  try {
    const response = await axiosClient.get('/expired-products-report', {
      params: {
        search: store.filters.search,
        to_date: store.filters.to_date,
        status: store.filters.status,
        category_id: store.filters.category_id
      }
    });
    store.expiredProducts = response.data;
    store.pagination = {
      current_page: 1,
      per_page: response.data.length,
      total: response.data.length,
      links: []
    };
  } catch (error) {
    console.error('Failed to fetch expired products:', error);
    throw error;
  } finally {
    store.loading = false;
  }
}

export async function fetchWorldBankReports(store, url = null) {
  store.filters.report_type = 'worldBank';
  store.loading = true;
  try {
    const endpoint = url || '/world-bank-reports';
    const response = await axiosClient.get(endpoint, {
      params: {
        report_date: store.filters.report_date,
        search: store.filters.search,
        report_type: store.filters.world_bank_report_type,
        per_page: store.pagination.per_page
      }
    });
    store.worldBankReports = response.data.data;
    updatePagination(store, response.data);
  } catch (error) {
    console.error('Failed to fetch World Bank reports:', error);
    throw error;
  } finally {
    store.loading = false;
  }
}

export function updatePagination(store, responseData) {
  store.pagination = {
    current_page: responseData.current_page,
    per_page: responseData.per_page,
    total: responseData.total,
    links: responseData.links || []
  };
}

export function updateFilters(store, newFilters) {
  store.filters = { ...store.filters, ...newFilters };
}

export function resetFilters(store) {
  store.filters = {
    from_date: new Date().toISOString().split('T')[0],
    to_date: new Date().toISOString().split('T')[0],
    supplier_id: '',
    customer_id: '',
    category_id: '',
    search: '',
    status: 'expired',
    report_type: store.filters.report_type,
    report_date: '',
    world_bank_report_type: 'all'
  };
}

export function changePage(store, url) {
  if (!url || store.loading) return;
  
  switch(store.filters.report_type) {
    case 'purchase':
      fetchPurchases(store, url);
      break;
    case 'sale':
      fetchSales(store, url);
      break;
    case 'expiredProducts':
      fetchExpiredProducts(store);
      break;
    case 'worldBank':
      fetchWorldBankReports(store, url);
      break;
  }
}