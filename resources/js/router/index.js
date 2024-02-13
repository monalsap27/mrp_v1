import Vue from 'vue';
import Router from 'vue-router';

/**
 * Layzloading will create many files and slow on compiling, so best not to use lazyloading on devlopment.
 * The syntax is lazyloading, but we convert to proper require() with babel-plugin-syntax-dynamic-import
 * @see https://doc.laravue.dev/guide/advanced/lazy-loading.html
 */

Vue.use(Router);

/* Layout */
import Layout from '@/desktop';

/* Router for modules */
import adminRoutes from './modules/admin/admin';
import errorRoutes from './modules/error';
import permissionRoutes from './modules/admin/permission';
import productionDashboard from './modules/production/dashboard';
import productionMaster from './modules/production/master';
import productionProduct from './modules/production/product';
import productionManagement from './modules/production/productionManagement';
import productionRequest from './modules/production/request';
import purchasingDashboard from './modules/purchasing/dashboard';
import purchasingMaster from './modules/purchasing/master';
import purchasingSubmit from './modules/purchasing/submit';
import purchasingOrders from './modules/purchasing/order';
import purchasingApproval from './modules/purchasing/approval';
import purchasingInbound from './modules/purchasing/inbound';
import vendorDashboard from './modules/vendor/dashboard';
import vendorOrders from './modules/vendor/request';
import vendorShipping from './modules/vendor/shipping';
import customerDashboard from './modules/customer/dashboard';
import customerMaster from './modules/customer/master';
import customerCustomer from './modules/customer/customer';
import customerDeposit from './modules/customer/deposit';
import customerInbound from './modules/customer/inbound';
import salesDashboard from './modules/sales/dashboard';
import salesMaster from './modules/sales/master';
import salesOrders from './modules/sales/order';
import salesShipping from './modules/sales/shipping';

/**
 * Sub-menu only appear when children.length>=1
 * @see https://doc.laravue.dev/guide/essentials/router-and-nav.html
 **/

/**
* hidden: true                   if `hidden:true` will not show in the sidebar(default is false)
* alwaysShow: true               if set true, will always show the root menu, whatever its child routes length
*                                if not set alwaysShow, only more than one route under the children
*                                it will becomes nested mode, otherwise not show the root menu
* redirect: noredirect           if `redirect:noredirect` will no redirect in the breadcrumb
* name:'router-name'             the name is used by <keep-alive> (must set!!!)
* meta : {
    roles: ['admin', 'editor']   Visible for these roles only
    permissions: ['view menu zip', 'manage user'] Visible for these permissions only
    title: 'title'               the name show in sub-menu and breadcrumb (recommend set)
    icon: 'svg-name'             the icon show in the sidebar
    noCache: true                if true, the page will no be cached(default is false)
    breadcrumb: false            if false, the item will hidden in breadcrumb (default is true)
    affix: true                  if true, the tag will affix in the tags-view
  }
**/

export const constantRoutes = [
  {
    path: '/redirect',
    component: Layout,
    hidden: true,
    children: [
      {
        path: '/redirect/:path*',
        component: () => import('@/views/redirect/index'),
      },
    ],
  },
  {
    path: '/login',
    component: () => import('@/views/login/index'),
    hidden: true,
  },
  {
    path: '/auth-redirect',
    component: () => import('@/views/login/AuthRedirect'),
    hidden: true,
  },
  {
    path: '/404',
    redirect: { name: 'Page404' },
    component: () => import('@/views/error-page/404'),
    hidden: true,
  },
  {
    path: '/401',
    component: () => import('@/views/error-page/401'),
    hidden: true,
  },
  {
    path: '',
    component: Layout,
    redirect: 'desktop',
    hidden: true,
    children: [
      {
        path: 'desktop',
        component: () => import('@/views/desktop/index'),
        name: 'Dashboard',
        meta: { title: 'dashboard', icon: 'dashboard', noCache: false },
      },
    ],
  },
  errorRoutes,
  {
    path: '/profile',
    component: Layout,
    redirect: '/profile/edit',
    hidden: true,
    children: [
      {
        path: 'edit',
        component: () => import('@/views/users/SelfProfile'),
        name: 'SelfProfile',
        meta: { title: 'userProfile', icon: 'user', noCache: true },
      },
    ],
  },
];
export const asyncRoutesAdmin = [adminRoutes, permissionRoutes];
export const asyncRoutesProduction = [
  productionDashboard,
  productionMaster,
  productionProduct,
  productionManagement,
  productionRequest,
  salesShipping,
];
export const asyncRoutesPurchasing = [
  purchasingDashboard,
  purchasingMaster,
  purchasingSubmit,
  purchasingOrders,
  purchasingApproval,
  purchasingInbound,
];
export const asyncRoutesVendor = [
  vendorDashboard,
  vendorOrders,
  vendorShipping,
];
export const asyncRoutesCustomer = [
  customerDashboard,
  customerMaster,
  customerCustomer,
  customerDeposit,
  customerInbound,
];
export const asyncRoutesSales = [
  salesDashboard,
  salesMaster,
  salesOrders,
  salesShipping,
];

export const asyncRoutes = [{ path: '*', redirect: '/404', hidden: true }];

const createRouter = () =>
  new Router({
    scrollBehavior: () => ({ y: 0 }),
    base: process.env.MIX_LARAVUE_PATH,
    routes: constantRoutes,
  });

const router = createRouter();

export function resetRouter() {
  const newRouter = createRouter();
  router.matcher = newRouter.matcher; // reset router
}

export default router;
