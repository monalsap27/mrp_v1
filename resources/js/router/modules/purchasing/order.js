import Layout from '@/layout';

const purchasingOrders = {
  path: '/purchasing-order',
  component: Layout,
  redirect: '/purchasing-order/list',
  name: 'Order',
  meta: {
    title: 'Order',
    icon: 'component',
    permissions: ['view menu purchasing'],
  },
  children: [
    {
      path: 'list',
      component: () => import('@/views/purchasing/order/List'),
      name: 'List Purchase Order',
      meta: {
        title: 'List Purchase Order',
        icon: 'fi-fi-file-invoice-dollar',
        permissions: ['view menu purchasing'],
      },
    },
    {
      path: 'create',
      component: () => import('@/views/purchasing/order/Create'),
      name: 'New Purchase Order',
      meta: {
        title: 'New Purchase Order',
        icon: 'edit',
        permissions: ['view menu purchasing'],
      },
      hidden: true,
    },
    {
      path: 'edit/:id(\\d+)',
      component: () => import('@/views/purchasing/order/Edit'),
      name: 'Edit Purchase Order',
      meta: {
        title: 'Edit Purchase Order',
        noCache: true,
        icon: 'edit',
        permissions: ['view menu purchasing'],
      },
      hidden: true,
    },
    {
      path: 'detailOrder/:id(\\d+)',
      component: () => import('@/views/purchasing/order/DetailOrder'),
      name: 'Detail Approval',
      meta: {
        title: 'Detail Approval',
        noCache: true,
        icon: 'edit',
        permissions: ['view menu approval purchasing'],
      },
      hidden: true,
    },
    {
      path: 'pdf/download/:id(\\d+)',
      component: () => import('@/views/pdf/Download'),
      hidden: true,
    },
  ],
};

export default purchasingOrders;
