import Layout from '@/layout';

const salesOrders = {
  path: '/sales-order',
  component: Layout,
  redirect: '/sales-order/list',
  name: 'Order',
  meta: {
    title: 'Order',
    icon: 'component',
    permissions: ['view menu sales'],
  },
  children: [
    {
      path: 'list',
      component: () => import('@/views/sales/order/List'),
      name: 'List Sales Order',
      meta: {
        title: 'List Sales Order',
        icon: 'expense',
        permissions: ['view menu sales'],
      },
    },
    {
      path: 'create',
      component: () => import('@/views/sales/order/Create'),
      name: 'New Sales Order',
      meta: {
        title: 'New Sales Order',
        icon: 'edit',
        permissions: ['view menu sales'],
      },
      hidden: true,
    },
    {
      path: 'edit/:id(\\d+)',
      component: () => import('@/views/sales/order/Edit'),
      name: 'Edit Purchase Order',
      meta: {
        title: 'Edit Purchase Order',
        noCache: true,
        icon: 'edit',
        permissions: ['view menu sales'],
      },
      hidden: true,
    },
  ],
};

export default salesOrders;
