import axiosClient from '../../axios.js';


export async function getPurchases(state,url=null,search='',perPage=10,sortField,sortDirection){
state.purchases.loading = true;
  try{
    url = url || '/purchases'
    await axiosClient.get(url,{
      params: {
        search,
        per_page: perPage,
        sort_field: sortField,
        sort_direction: sortDirection,
      }
    })
    .then(({data})=>{
      if(data){
        state.purchases={
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

  }catch(error){
    state.purchases.loading=false;
    console.error("Failed to get purchase:", error);
    throw error;
  }
}


export async function createPurchase(purchase) {
  let requestData;
  
  if (purchase.image instanceof File) {
    // Handle file upload case with FormData
    const form = new FormData();
    form.append('created_at', purchase.created_at);
    form.append('purchase_status', purchase.purchase_status);
    form.append('reference_no', purchase.reference_no);
    form.append('discount_all', purchase.discount_all);
    form.append('discount_type', purchase.discount_type);
    form.append('supplier_id', purchase.supplier_id);
    form.append('total', purchase.total);
    form.append('note', purchase.note);
    form.append('image', purchase.image);
    
    // Add purchase items
    if (purchase.items && purchase.items.length > 0) {
      purchase.items.forEach((item, index) => {
        form.append(`items[${index}][product_id]`, item.product_id);
        form.append(`items[${index}][quantity]`, item.quantity);
        form.append(`items[${index}][purchase_price]`, item.purchase_price);
        form.append(`items[${index}][discount]`, item.discount);
        form.append(`items[${index}][unit_cost]`, item.unit_cost);
        form.append(`items[${index}][total_amount]`, item.total_amount);
      });
    }
    
    // Add payments
    // if (purchase.payments && purchase.payments.length > 0) {
    //   purchase.payments.forEach((payment, index) => {
    //     form.append(`payments[${index}][date]`, payment.date);
    //     form.append(`payments[${index}][payment_type]`, payment.payment_type);
    //     form.append(`payments[${index}][payment_note]`, payment.payment_note);
    //     form.append(`payments[${index}][amount]`, payment.amount);
    //   });
    // }
    
    requestData = form;
  } else {
    // Handle standard JSON data case
    requestData = {
      ...purchase,
      // Ensure nested objects are properly structured
      items: purchase.items || [],
      // payments: purchase.payments || []
    };
  }
  
  return await axiosClient.post('/purchases', requestData);
}
export async function updatePurchase(purchase){
  const id = purchase.id;
  if(purchase.image instanceof File){
    const form = new FormData();
    form.append('id', purchase.id);
    form.append('date', purchase.date);
    form.append('code', purchase.code);
    form.append('purchase_status', purchase.purchase_status);
    form.append('reference_no', purchase.reference_no);
    form.append('discount', purchase.discount);
    form.append('supplier_id', purchase.supplier_id);
    form.append('total', purchase.total);
    form.append('payment_id', purchase.payment_id);
    form.append('created_by', purchase.created_by);
    form.append('status', purchase.status);
    form.append('note', purchase.note);
    form.append('_method', 'PUT'); 
    purchase = form;
  }else{
    purchase._method = 'PUT';
  }
  return await axiosClient.post(`/purchases/${id}`,purchase)

}

export async function deletePurchase(id){
  return await axiosClient.delete(`/purchases/${id}`)

}

export async function getPurchase(id){

  return await axiosClient.get(`/purchases/${id}`)

}
  
export async function getPurchaseInvoice(purchaseId, invoiceId) {
  try {
    const response = await axiosClient.get(`/purchases/${purchaseId}/invoices/${invoiceId}`);
    return response.data;
  } catch (error) {
    console.error('Invoice API Error:', error.response);
    throw error;
  }
}