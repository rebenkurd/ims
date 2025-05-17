import axiosClient from '../../axios.js';

export async function getSales(state, url = null, search = '', perPage = 10, sortField, sortDirection) {
  state.sales.loading = true;
  try {
    url = url || '/sales'
    await axiosClient.get(url, {
      params: {
        search,
        per_page: perPage,
        sort_field: sortField,
        sort_direction: sortDirection,
      }
    })
    .then(({data}) => {
      if (data) {
        state.sales = {
          loading: false,
          data: data.data,
          links: data.meta?.links || [],
          to: data.meta?.to,
          from: data.meta?.from,
          page: data.meta?.current_page,
          limit: data.meta?.per_page,
          total: data.meta?.total,
        }
      }
      return data
    })
  } catch (error) {
    state.sales.loading = false;
    console.error("Failed to get sales:", error);
    throw error;
  }
}

export async function createSale(sale) {
  let requestData;
  
  if (sale.image instanceof File) {
    const form = new FormData();
    form.append('created_at', sale.created_at);
    form.append('sale_status', sale.sale_status);
    form.append('reference_no', sale.reference_no);
    form.append('discount_all', sale.discount_all);
    form.append('discount_type', sale.discount_type);
    form.append('customer_id', sale.customer_id);
    form.append('total', sale.total);
    form.append('note', sale.note);
    form.append('image', sale.image);
    
    if (sale.items && sale.items.length > 0) {
      sale.items.forEach((item, index) => {
        form.append(`items[${index}][product_id]`, item.product_id);
        form.append(`items[${index}][quantity]`, item.quantity);
        form.append(`items[${index}][sale_price]`, item.sale_price);
        form.append(`items[${index}][discount]`, item.discount);
        form.append(`items[${index}][unit_price]`, item.unit_price);
        form.append(`items[${index}][total_amount]`, item.total_amount);
      });
    }
    
    requestData = form;
  } else {
    requestData = {
      ...sale,
      items: sale.items || [],
    };
  }
  
  return await axiosClient.post('/sales', requestData);
}

export async function updateSale(sale) {
  const id = sale.id;
  if (sale.image instanceof File) {
    const form = new FormData();
    form.append('id', sale.id);
    form.append('date', sale.date);
    form.append('code', sale.code);
    form.append('sale_status', sale.sale_status);
    form.append('reference_no', sale.reference_no);
    form.append('discount', sale.discount);
    form.append('customer_id', sale.customer_id);
    form.append('total', sale.total);
    form.append('payment_id', sale.payment_id);
    form.append('created_by', sale.created_by);
    form.append('status', sale.status);
    form.append('note', sale.note);
    form.append('_method', 'PUT'); 
    sale = form;
  } else {
    sale._method = 'PUT';
  }
  return await axiosClient.post(`/sales/${id}`, sale)
}

export async function deleteSale(id) {
  return await axiosClient.delete(`/sales/${id}`)
}

export async function getSale(id) {
  return await axiosClient.get(`/sales/${id}`)
}
  
export async function getSaleInvoice(saleId, invoiceId) {
  try {
    const response = await axiosClient.get(`/sales/${saleId}/invoices/${invoiceId}`);
    return response.data;
  } catch (error) {
    console.error('Invoice API Error:', error.response);
    throw error;
  }
}