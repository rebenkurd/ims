import axiosClient from '../../axios.js';

export async function getCustomers(state, url = null, search = '', perPage = 10, sortField, sortDirection) {
    state.customers.loading = true;
    try {
        url = url || '/customers';
        await axiosClient.get(url, {
            params: {
                search,
                per_page: perPage,
                sort_field: sortField,
                sort_direction: sortDirection,
            }
        })
        .then(({ data }) => {
            state.customers = {
                loading: false,
                data: data.data,
                links: data.meta?.links || [],
                to: data.meta?.to,
                from: data.meta?.from,
                page: data.meta?.current_page,
                limit: data.meta?.per_page,
                total: data.meta?.total,
            };
            return data;
        });
    } catch (error) {
        state.customers.loading = false;
        console.error("Failed to get customers:", error);
        throw error;
    }
}

export async function createCustomer(customer) {
    const form = new FormData();
    form.append('name', customer.name || '');
    form.append('mobile', customer.mobile || '');
    form.append('email', customer.email || '');
    form.append('phone', customer.phone || '');
    form.append('opening_balance', customer.opening_balance);
    form.append('country', customer.country || '');
    form.append('state', customer.state) || '';
    form.append('city', customer.city || '');
    form.append('postcode', customer.postcode || '');
    form.append('address', customer.address || '');
    form.append('payment_id', customer.payment_id);
    form.append('status', customer.status || 1);
    form.append('_method', 'POST');
    return await axiosClient.post('/customers', form);
}

export async function updateCustomer(customer) {
    const id = customer.id;
    const form = new FormData();
    form.append('name', customer.name);
    form.append('mobile', customer.mobile);
    form.append('email', customer.email);
    form.append('phone', customer.phone);
    form.append('opening_balance', customer.opening_balance);
    form.append('country', customer.country);
    form.append('state', customer.state);
    form.append('city', customer.city);
    form.append('postcode', customer.postcode);
    form.append('address', customer.address);
    form.append('payment_id', customer.payment_id);
    form.append('status', customer.status);
    form.append('_method', 'PUT');
    return await axiosClient.post(`/customers/${id}`, form);
}

export async function deleteCustomer(id) {
    return await axiosClient.delete(`/customers/${id}`);
}

export async function getCustomer(id) {
    return await axiosClient.get(`/customers/${id}`);
}

export async function getCustomersForSelect(state) {
    state.customers.loading = true;
    try {
        await axiosClient.get('/customers/select')
        .then(({ data }) => {
            state.customers = {
                loading: false,
                data: data,
            };
            return data;
        });
    } catch (error) {
        state.customers.loading = false;
        console.error("Failed to get customers for select:", error);
        throw error;
    }
}