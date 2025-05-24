export default () => ({
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
    // Additional report-specific filters
    supplier_id: null,
    customer_id: null,
    payment_method: null,
    category_id: null
  },
  report_types: {
    PURCHASE: 'purchase',
    SALES: 'sales',
    PROFIT_LOSS: 'profit-loss',
    STOCK: 'stock',
    EXPIRED: 'expired-items',
    PURCHASE_PAYMENTS: 'purchase-payments',
    SALES_PAYMENTS: 'sales-payments'
  },
  current_report: null
})