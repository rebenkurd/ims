import axiosClient from '@/axios.js';
import state from '@store/cart/state.js';


export function addItem(state,item) {
  const existingItem = state.items.find(i => i.id === item.id)
  
  if (existingItem) {
    existingItem.quantity++
  } else {
    state.items.push({
      id: item.id,
      name: item.name,
      item_code: item.item_code,
      quantity: 1,
      purchase_price: item.purchase_price,
      discount: 0,
      tax_percentage: item.tax_percentage,
      tax_amount: item.tax_amount,
      unit_cost: item.unit_cost
    })
  }
}

export function removeItem(state,itemId) {
  const index = state.items.findIndex(item => item.id === itemId)
  if (index !== -1) {
    state.items.splice(index, 1)
  }
}

export function incrementQuantity(state,itemId) {
  const item = state.items.find(item => item.id === itemId)
  if (item) {
    item.quantity++
  }
}

export function decrementQuantity(itemId) {
  const item = state.items.find(item => item.id === itemId)
  if (item && item.quantity > 1) {
    item.quantity--
  } else if (item && item.quantity === 1) {
    state.removeItem(itemId)
  }
}
export function updateOtherCharges(state,amount) {
  state.otherCharges = parseFloat(amount) || 0
}

export function updateDiscountPercentage(state,percentage) {
  state.discountPercentage = parseFloat(percentage) || 0
}

export function updateNote(state,note) {
  state.note = note
}

export function clearCart() {
  this.items = []
  this.otherCharges = 0
  this.discountPercentage = 0
  this.note = ''
}

export async function submitOrder(state) {
  state.isSubmitting = true
  try {
    const orderData = {
      items: state.items,
      other_charges: state.otherCharges,
      discount_percentage: state.discountPercentage,
      note: state.note,
      subtotal: state.subtotal,
      discount_amount: state.discountAmount,
      grand_total: state.grandTotal
    }
    
    const response = await axiosClient.post('/api/orders', orderData)
    state.clearCart()
    state.error = null
    return response.data
  } catch (error) {
    state.error = error.message || 'Failed to submit order'
    throw error
  } finally {
    state.isSubmitting = false
  }
}











