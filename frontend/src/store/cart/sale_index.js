import { defineStore } from 'pinia';
import axiosClient from '@/axios';

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    otherCharges: 0,
    discountAll: 0,
    discountType: 'fixed',
    note: '',
    searchResults: [],
    payments: [],
    currentPayment: {
      amount: 0,
      payment_type: 'cash',
      payment_note: '',
      date: new Date().toISOString().split('T')[0]
    },
    paidAmount: 0
  }),
  
  getters: {
    subtotal: (state) => state.items.reduce((sum, item) => sum + item.totalAmount, 0),
    totalQuantities: (state) => state.items.reduce((sum, item) => sum + item.quantity, 0),
    totalTax: (state) => state.items.reduce((sum, item) => sum + item.taxAmount, 0),
    grandTotal: (state) => {
      const subTotal = state.subtotal;
      const discount = state.discountType === 'per' 
        ? subTotal * (state.discountAll / 100) 
        : state.discountAll;
      return subTotal + state.otherCharges - discount;
    },
    balanceDue: (state) => state.grandTotal - state.paidAmount
  },
  
  actions: {
    clearItems() {
      this.items = [];
    },
    
    addItemDirect(item) {
      this.items.push({
        ...item,
        unitPrice: parseFloat(item.unit_price || item.salePrice || 0),
        totalAmount: parseFloat(item.total_amount || (item.quantity * item.salePrice) || 0)
      });
    },
    
    syncWithSale(sale) {
      this.clearItems();
      
      if (sale.items && Array.isArray(sale.items) && sale.items.length > 0) {
        sale.items.forEach(item => {
          this.addItemDirect({
            id: item.product_id,
            name: item.product_name,
            quantity: item.quantity,
            salePrice: item.sale_price,
            discount: item.discount,
            unit_price: item.unit_price,
            total_amount: item.total_amount
          });
        });
      }
      
      this.discountAll = sale.discount_all || 0;
      this.discountType = sale.discount_type || 'fixed';
      this.note = sale.note || '';
      this.payments = sale.payments || [];
      this.paidAmount = this.payments.reduce((sum, payment) => sum + parseFloat(payment.amount), 0);
    },
    
    async searchProducts(query) {
      try {
        const response = await axiosClient.post(`/products/search?query=${query}`);
        this.searchResults = response.data;
      } catch (error) {
        console.error('Error searching products:', error);
      }
    },
    
    addItem(product) {
      const existingItem = this.items.find(item => item.id === product.id);
      
      if (existingItem) {
        existingItem.quantity += 1;
        this.updateItemTotals(existingItem);
      } else {
        const newItem = {
          id: product.id,
          name: product.name,
          quantity: 1,
          salePrice: product.price,
          discount: 0,
          taxPercent: 10,
          taxAmount: product.price * 0.1,
          unitPrice: product.price * 1.1,
          totalAmount: product.price * 1.1
        };
        this.items.push(newItem);
      }
    },
    
    removeItem(itemId) {
      this.items = this.items.filter(item => item.id !== itemId);
    },
    
    updateQuantity(itemId, quantity) {
      const item = this.items.find(item => item.id === itemId);
      if (item) {
        item.quantity = Math.max(1, quantity);
        this.updateItemTotals(item);
      }
    },
    
    updateItemTotals(item) {
      const quantity = Number(item.quantity) || 1;
      const salePrice = Number(item.salePrice) || 0;
      const discount = Number(item.discount) || 0;
      
      const basePrice = salePrice * quantity;
      item.totalAmount = basePrice - discount;
      item.unitPrice = quantity > 0 ? item.totalAmount / quantity : 0;
      
      if (isNaN(item.unitPrice)) item.unitPrice = 0;
      if (isNaN(item.totalAmount)) item.totalAmount = 0;
    },
    
    addPayment() {
      if (this.currentPayment.amount <= 0) return;
      
      this.payments.push({ ...this.currentPayment });
      this.paidAmount += parseFloat(this.currentPayment.amount);
      this.currentPayment = {
        amount: 0,
        payment_type: 'cash',
        payment_note: '',
        date: new Date().toISOString().split('T')[0]
      };
    },
    
    removePayment(index) {
      this.paidAmount -= parseFloat(this.payments[index].amount);
      this.payments.splice(index, 1);
    },
    
    resetInvoice() {
      this.clearItems();
      this.otherCharges = 0;
      this.discountAll = 0;
      this.discountType = 'fixed';
      this.note = '';
      this.payments = [];
      this.currentPayment = {
        amount: 0,
        payment_type: 'cash',
        payment_note: '',
        date: new Date().toISOString().split('T')[0]
      };
      this.paidAmount = 0;
    }
  }
});