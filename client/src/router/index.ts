import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import Home from '@/views/Home.vue';
import RegisterMerchant from '@/views/Auth/RegisterMerchant.vue';
import RegisterCustomer from '@/views/Auth/RegisterCustomer.vue';
import Login from '@/views/Auth/Login.vue';
import DashboardMerchant from '@/views/Merchant/DashboardMerchant.vue';
import DashboardCustomer from '@/views/Customer/DashboardCustomer.vue';
import NotFound from '@/views/NotFound.vue';
import axios from 'axios';
import Cookies from 'js-cookie';
import MerchantProfile from '@/views/Merchant/MerchantProfile.vue';
import MenuManagementPage from '@/views/Merchant/MenuManagementPage.vue';
import MenuDetailPage from '@/views/Merchant/MenuDetailPage.vue';
import AddMenuPage from '@/views/Merchant/AddMenuPage.vue';
import OrderManagementPage from '@/views/Merchant/OrderManagementPage.vue';
import OrderDetailPage from '@/views/Merchant/OrderDetailPage.vue';
import MenuList from '@/views/Customer/MenuList.vue';
import OrderMenu from '@/views/Customer/OrderMenu.vue';
import OrderList from '@/views/Customer/OrderList.vue';
import OrderListDetail from '@/views/Customer/OrderListDetail.vue';

const routes: Array<RouteRecordRaw> = [
  { 
    path: '/', 
    name: 'home', 
    component: Login, 
    meta: { requiresVisitor: true }
  },
  { 
    path: '/register-merchant', 
    name: 'registerMerchant', 
    component: RegisterMerchant, 
    meta: { requiresVisitor: true }
  },
  { 
    path: '/register-customer', 
    name: 'registerCustomer', 
    component: RegisterCustomer, 
    meta: { requiresVisitor: true }
  },
  { 
    path: '/login', 
    name: 'login', 
    component: Login, 
    meta: { requiresVisitor: true }
  }, 
  { 
    path: '/dashboard-merchant', 
    name: 'dashboardMerchant', 
    component: DashboardMerchant, 
    meta: { requiresAuth: true, role: 'merchant' }
  },
  { 
    path: '/merchant-profile', 
    name: 'merchantProfile', 
    component: MerchantProfile, 
    meta: { requiresAuth: true, role: 'merchant' }
  },
  { 
    path: '/menu-management', 
    name: 'menuManagement', 
    component: MenuManagementPage, 
    meta: { requiresAuth: true, role: 'merchant' }
  },
  { 
    path: '/menu/:id',
    name: 'menuDetail', 
    component: MenuDetailPage, 
    meta: { requiresAuth: true, role: 'merchant' }
  },
  { 
    path: '/order-management',
    name: 'OrderManagement',
    component: OrderManagementPage, 
    meta: { requiresAuth: true, role: 'merchant' }
  },
  { 
    path: '/order/:id',
    name: 'OrderDetail',
    component: OrderDetailPage, 
    meta: { requiresAuth: true, role: 'merchant' }
  },
  { 
    path: '/add-menu',
    name: 'addMenu', 
    component: AddMenuPage, 
    meta: { requiresAuth: true, role: 'merchant' }
  },
  { 
    path: '/menu-list', 
    name: 'menuList', 
    component: MenuList, 
    meta: { requiresAuth: true, role: 'customer' }
  },
  { 
    path: '/order-menu', 
    name: 'orderMenu', 
    component: OrderMenu, 
    meta: { requiresAuth: true, role: 'customer' }
  },
  { 
    path: '/order-list', 
    name: 'orderList', 
    component: OrderList, 
    meta: { requiresAuth: true, role: 'customer' }
  },
  { 
    path: '/order-list/:id', 
    name: 'orderListDetail', 
    component: OrderListDetail, 
    meta: { requiresAuth: true, role: 'customer' }
  },
  { 
    path: '/dashboard-customer', 
    name: 'dashboardCustomer', 
    component: DashboardCustomer, 
    meta: { requiresAuth: true, role: 'customer' }
  },
  { 
    path: '/:catchAll(.*)', 
    name: 'notFound', 
    component: NotFound
  }
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
});

router.beforeEach(async (to, from, next) => {
  const token = Cookies.get('token');

  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!token) {
      return next({ name: 'login' });
    }

    try {
      const response = await axios.get('http://localhost:8000/api/user-details', {
        headers: {
          Authorization: `Bearer ${token}`
        }
      });
      const userRole = response.data.user.role;

      if (to.meta.role && to.meta.role === userRole) {
        next();
      } else {
        next({ name: 'notFound' });
      }
    } catch (error) {
      next({ name: 'login' });
    }
  } 
  else if (to.matched.some(record => record.meta.requiresVisitor)) {
    if (token) {
      try {
        const response = await axios.get('http://localhost:8000/api/user-details', {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        const userRole = response.data.user.role;

        if (userRole === 'merchant') {
          next({ name: 'dashboardMerchant' });
        } else if (userRole === 'customer') {
          next({ name: 'dashboardCustomer' });
        }
      } catch (error) {
        next();
      }
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router;