import axiosClient from '@/axios.js';

export async function getSuppliers(state, url = null, search = '', perPage = 10, sortField, sortDirection) {
    state.suppliers.loading = true;
    try {
        url = url || '/suppliers';
        await axiosClient.get(url, {
            params: {
                search,
                per_page: perPage,
                sort_field: sortField,
                sort_direction: sortDirection,
            }
        })
        .then(({ data }) => {
            state.suppliers = {
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
        state.suppliers.loading = false;
        console.error("Failed to get suppliers:", error);
        throw error;
    }
}

export async function createSupplier(supplier) {
    const form = new FormData();
    form.append('name', supplier.name || '');
    form.append('mobile', supplier.mobile || '');
    form.append('email', supplier.email || '');
    form.append('phone', supplier.phone || '');
    form.append('opening_balance', supplier.opening_balance);
    form.append('country', supplier.country || '');
    form.append('state', supplier.state) || '';
    form.append('city', supplier.city || '');
    form.append('postcode', supplier.postcode || '');
    form.append('address', supplier.address || '');
    form.append('payment_id', supplier.payment_id);
    form.append('status', supplier.status || 1);
    form.append('_method', 'POST');
    return await axiosClient.post('/suppliers', form);
}

export async function updateSupplier(supplier) {
    const id = supplier.id;
    const form = new FormData();
    form.append('name', supplier.name);
    form.append('mobile', supplier.mobile);
    form.append('email', supplier.email);
    form.append('phone', supplier.phone);
    form.append('opening_balance', supplier.opening_balance);
    form.append('country', supplier.country);
    form.append('state', supplier.state);
    form.append('city', supplier.city);
    form.append('postcode', supplier.postcode);
    form.append('address', supplier.address);
    form.append('payment_id', supplier.payment_id);
    form.append('status', supplier.status);
    form.append('_method', 'PUT');
    // debugger;
    return await axiosClient.post(`/suppliers/${id}`, form);
}

export async function deleteSupplier(id) {
    return await axiosClient.delete(`/suppliers/${id}`);
}

export async function getSupplier(id) {
    return await axiosClient.get(`/suppliers/${id}`);
}

export async function getSuppliersForSelect(state) {
    state.suppliers.loading = true;
    try {
        await axiosClient.get('/suppliers-for-select')
        .then(({ data }) => {
            state.suppliers = {
                loading: false,
                data: data,
            };
            return data;
        });
    } catch (error) {
        state.suppliers.loading = false;
        console.error("Failed to get suppliers for select:", error);
        throw error;
    }
}