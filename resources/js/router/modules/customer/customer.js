import Layout from '@/layout';

const customerCustomer = {
  path: '/customer-customer',
  component: Layout,
  redirect: '/customer-customer/list',
  name: 'Customer',
  meta: {
    title: 'Customer',
    icon: 'component',
    permissions: ['view menu customer'],
  },
  children: [
    {
      path: 'list',
      component: () => import('@/views/customer/customer/List'),
      name: 'List Customer',
      meta: {
        title: 'List Customer',
        icon: 'refund',
        permissions: ['view menu customer'],
      },
    },
    {
      path: 'create',
      component: () => import('@/views/customer/customer/Create'),
      name: 'New Customer',
      meta: {
        title: 'New Customer',
        icon: 'edit',
        permissions: ['view menu customer'],
      },
      hidden: true,
    },
    {
      path: 'edit/:id(\\d+)',
      component: () => import('@/views/customer/customer/Edit'),
      name: 'Edit Customer Order',
      meta: {
        title: 'Edit Customer Order',
        noCache: true,
        icon: 'edit',
        permissions: ['view menu customer'],
      },
      hidden: true,
    },
  ],
};

export default customerCustomer;
