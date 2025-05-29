// make that code if user dose not have permesion directly send user to not found page
import {createRouter, createWebHistory} from 'vue-router'
import Dashboard from '@views/Dashboard.vue'
import Login from '@views/Login.vue'
import RequestResetPassword from '@views/RequestResetPassword.vue';
import ResetPassword from '@views/ResetPassword.vue';
import AppLayout from '@components/AppLayout.vue';
import { useStore } from '@/store'
import NotFound from '@views/NotFound.vue'
import Products from '@views/Products/Products.vue'
import ProductForm from '@views/Products/ProductForm.vue'
import Users from '@views/Users/Users.vue'
import UserForm from '@views/Users/UserForm.vue'
import Categories from '@views/Categories/Categories.vue'
import CategoryForm from '@views/Categories/CategoryForm.vue'
import Brands from '@views/Brands/Brands.vue'
import BrandForm from '@views/Brands/BrandForm.vue'
import Purchases from '@views/Purchases/Purchases.vue'
import PurchaseForm from '@views/Purchases/PurchaseForm.vue'
import PurchaseInvoice from '@views/Purchases/Invoice.vue'
import Sales from '@views/Sales/Sales.vue'
import SaleForm from '@views/Sales/SaleForm.vue'
import SaleInvoice from '@views/Sales/Invoice.vue'
import Suppliers from '@views/Suppliers/Suppliers.vue'
import SupplierForm from '@views/Suppliers/SupplierForm.vue'
import Customers from '@views/Customers/Customers.vue'
import CustomerForm from '@views/Customers/CustomerForm.vue'
import CompanyProfile from '@views/CompanyProfile.vue'
import PurchaseReport from '@views/Reports/PurchaseReport.vue';
import SaleReport from '@views/Reports/SaleReport.vue';
import ExpiredProductsReport from '@views/Reports/ExpiredProductsReport.vue';

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
                meta: { allowedRoles: [1, 2, 3] } // Admin, Saler, Purchaser
            },
            // Admin-only routes
            {
                path: 'product_list',
                name: 'app.product_list',
                component: Products,
                meta: { allowedRoles: [1] } // Admin only
            },
            {
                path: 'product_form/:id?',
                name: 'app.product_form',
                component: ProductForm,
                meta: { allowedRoles: [1] } // Admin only
            },
            {
                path: 'user_list',
                name: 'app.user_list',
                component: Users,
                meta: { allowedRoles: [1] } // Admin only
            },
            {
                path: 'user_form/:id?',
                name: 'app.user_form',
                component: UserForm,
                meta: { allowedRoles: [1] } // Admin only
            },
            {
                path: 'category_list',
                name: 'app.category_list',
                component: Categories,
                meta: { allowedRoles: [1] } // Admin only
            },
            {
                path: 'category_form/:id?',
                name: 'app.category_form',
                component: CategoryForm,
                meta: { allowedRoles: [1] } // Admin only
            },
            {
                path: 'brand_list',
                name: 'app.brand_list',
                component: Brands,
                meta: { allowedRoles: [1] } // Admin only
            },
            {
                path: 'brand_form/:id?',
                name: 'app.brand_form',
                component: BrandForm,
                meta: { allowedRoles: [1] } // Admin only
            },
            {
                path: 'purchase_list',
                name: 'app.purchase_list',
                component: Purchases,
                meta: { allowedRoles: [1, 3] } // Admin and Purchaser
            },
            {
                path: 'purchase_form/:id?',
                name: 'app.purchase_form',
                component: PurchaseForm,
                meta: { allowedRoles: [1, 3] } // Admin and Purchaser
            },
            {
                path: 'purchases/:purchaseId/invoice/:invoiceId',
                name: 'app.purchase_invoice',
                component: PurchaseInvoice,
                meta: { allowedRoles: [1, 3] } // Admin and Purchaser
            },
            {
                path: 'sale_list',
                name: 'app.sale_list',
                component: Sales,
                meta: { allowedRoles: [1, 2] } // Admin and Saler
            },
            {
                path: 'sale_form/:id?',
                name: 'app.sale_form',
                component: SaleForm,
                meta: { allowedRoles: [1, 2] } // Admin and Saler
            },
            {
                path: 'sales/:saleId/invoice/:invoiceId',
                name: 'app.sale_invoice',
                component: SaleInvoice,
                meta: { allowedRoles: [1, 2] } // Admin and Saler
            },
            {
                path: 'supplier_list',
                name: 'app.supplier_list',
                component: Suppliers,
                meta: { allowedRoles: [1, 3] } // Admin and Purchaser
            },
            {
                path: 'supplier_form/:id?',
                name: 'app.supplier_form',
                component: SupplierForm,
                meta: { allowedRoles: [1, 3] } // Admin and Purchaser
            },
            {
                path: 'customer_list',
                name: 'app.customer_list',
                component: Customers,
                meta: { allowedRoles: [1, 2] } // Admin and Saler
            },
            {
                path: 'customer_form/:id?',
                name: 'app.customer_form',
                component: CustomerForm,
                meta: { allowedRoles: [1, 2] } // Admin and Saler
            },
            {
                path: 'company_profile',
                name: 'app.company_profile',
                component: CompanyProfile,
                meta: { allowedRoles: [1] } // Admin only
            },
            {
                path: 'purchase_report',
                name: 'app.purchase_report',
                component: PurchaseReport,
                meta: { allowedRoles: [1, 3] } // Admin and Purchaser
            },
            {
                path: 'sale_report',
                name: 'app.sale_report',
                component: SaleReport,
                meta: { allowedRoles: [1, 2] } // Admin and Saler
            },
            {
                path: 'expired_product_report',
                name: 'app.expired_product_report',
                component: ExpiredProductsReport,
                meta: { allowedRoles: [1] } // Admin only
            },
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

router.beforeEach((to, from, next) => {
    const store = useStore();
    
    // Check role-based access
    if (to.meta.allowedRoles) {
        const userRole = store.user.role_id;
        if (!userRole || !to.meta.allowedRoles.includes(userRole)) {
            next({ name: 'notFound' }); 
            return;
        }
    }
    
    // Check authentication
    if (to.meta.requiresAuth && !store.user.token) {
        next({ name: 'login' });
        return;
    } else if (to.meta.requiresGuest && store.user.token) {
        next({ name: 'app.dashboard' });
        return;
    }
    
    next();
});

export default router;