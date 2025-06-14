export default () => ({
  loading: false,
  purchases: [],
  sales: [],
  expiredProducts: [],
  worldBankReports: [],
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
    report_type: 'purchase',
    report_date: '',
    world_bank_report_type: 'all'
  }
});