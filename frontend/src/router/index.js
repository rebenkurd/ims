
import {createRouter, createWebHistory} from 'vue-router'
import Dashboard from '../views/Dashboard.vue'
import Login from '../views/Login.vue'

import RequestResetPassword from '../views/RequestResetPassword.vue';
import ResetPassword from '../views/ResetPassword.vue';
import AppLayout from '../components/AppLayout.vue';
import { useStore } from '../store'
import NotFound from '../views/NotFound.vue'
import Products from '../views/Products/Products.vue'
import ProductForm from '../views/Products/ProductForm.vue'
import Users from '../views/Users/Users.vue'
import UserForm from '../views/Users/UserForm.vue'
import Categories from '../views/Categories/Categories.vue'
import CategoryForm from '../views/Categories/CategoryForm.vue'
import Brands from '../views/Brands/Brands.vue'
import BrandForm from '../views/Brands/BrandForm.vue'
import Purchases from '../views/Purchases/Purchases.vue'
import PurchaseForm from '../views/Purchases/PurchaseForm.vue'
import PurchaseInvoice from '../views/Purchases/Invoice.vue'
import Sales from '../views/Sales/Sales.vue'
import SaleForm from '../views/Sales/SaleForm.vue'
import SaleInvoice from '../views/Sales/Invoice.vue'
import Suppliers from '../views/Suppliers/Suppliers.vue'
import SupplierForm from '../views/Suppliers/SupplierForm.vue'
import Customers from '../views/Customers/Customers.vue'
import CustomerForm from '../views/Customers/CustomerForm.vue'
import CompanyProfile from '../views/CompanyProfile.vue'



const routes = [
    {
        path: '/app',
        name:'app',
        component:AppLayout,
        meta:{
            requiresAuth: true,
        },
        children:[
            {
                path: 'dashboard',
                name: 'app.dashboard',
                component: Dashboard,
            },
            {
                path: 'product_list',
                name: 'app.product_list',
                component: Products,
            },
            {
                path: 'product_form/:id?',
                name: 'app.product_form',
                component: ProductForm,
            },
            {
                path: 'user_list',
                name: 'app.user_list',
                component: Users,
            },
            {
                path: 'user_form/:id?',
                name: 'app.user_form',
                component: UserForm,
            },
            {
                path: 'category_list',
                name: 'app.category_list',
                component: Categories,
            },
            {
                path: 'category_form/:id?',
                name: 'app.category_form',
                component: CategoryForm,
            },
            {
                path: 'brand_list',
                name: 'app.brand_list',
                component: Brands,
            },
            {
                path: 'brand_form/:id?',
                name: 'app.brand_form',
                component: BrandForm,
            },
            {
                path: 'purchase_list',
                name: 'app.purchase_list',
                component: Purchases,
            },
            {
                path: 'purchase_form/:id?',
                name: 'app.purchase_form',
                component: PurchaseForm,
            },
            {
                path: 'purchases/:purchaseId/invoice/:invoiceId',
                name: 'app.purchase_invoice',
                component: PurchaseInvoice,
                meta: { requiresAuth: true }
              },
            {
                path: 'sale_list',
                name: 'app.sale_list',
                component: Sales,
            },
            {
                path: 'sale_form/:id?',
                name: 'app.sale_form',
                component: SaleForm,
            },
            {
                path: 'sales/:saleId/invoice/:invoiceId',
                name: 'app.sale_invoice',
                component: SaleInvoice,
                meta: { requiresAuth: true }
            },
            {
                path: 'supplier_list',
                name: 'app.supplier_list',
                component: Suppliers,
            },
            {
                path: 'supplier_form/:id?',
                name: 'app.supplier_form',
                component: SupplierForm,
            },
            {
                path: 'customer_list',
                name: 'app.customer_list',
                component: Customers,
            },
            {
                path: 'customer_form/:id?',
                name: 'app.customer_form',
                component: CustomerForm,
            },
            {
                path: 'company_profile',
                name: 'app.company_profile',
                component: CompanyProfile,
            }

        ]
    },
    {
        path: '/login',
        name:'login',
        meta:{
            requiresGuest:true,
        },
        component: Login,
    },
    {
        path: '/request-reset-password',
        name:'requestResetPassword',
        meta:{
            requiresGuest:true,
        },
        component: RequestResetPassword,
    },
    {
        path: '/reset-password/:token',
        name:'resetPassword',
        meta:{
            requiresGuest:true,
        },
        component: ResetPassword,
    },
    {
        path: '/:pathMatch(.*)',
        name:'notFound',
        component: NotFound,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});


router.beforeEach((to,from,next)=>{
    const store = useStore();
    if(to.meta.requiresAuth && !store.user.token){
        next({name:'login'})
    }else if(to.meta.requiresGuest && store.user.token){
        next({name:'app.dashboard'})
    }else{
        next()
    }
})

export default router