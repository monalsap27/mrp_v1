import Layout from '@/layout';

const customerDeposit = {
  path: '/customer-deposit',
  component: Layout,
  redirect: '/customer-deposit/list',
  name: 'Deposit',
  meta: {
    title: 'Deposit',
    icon: 'component',
    permissions: ['view menu customer'],
  },
  children: [
    {
      path: 'list',
      component: () => import('@/views/customer/deposit/List'),
      name: 'List Deposit',
      meta: {
        title: 'Deposit',
        icon: 'deposit',
        permissions: ['view menu customer'],
      },
    },
    // {
    //   path: 'create',
    //   component: () => import('@/views/customer/deposit/Create'),
    //   name: 'New Deposit',
    //   meta: {
    //     title: 'New Deposit',
    //     icon: 'edit',
    //     permissions: ['view menu customer'],
    //   },
    //   hidden: true,
    // },
    // {
    //   path: 'edit/:id(\\d+)',
    //   component: () => import('@/views/customer/deposit/Edit'),
    //   name: 'Edit Purchase Order',
    //   meta: {
    //     title: 'Edit Purchase Order',
    //     noCache: true,
    //     icon: 'edit',
    //     permissions: ['view menu customer'],
    //   },
    //   hidden: true,
    // },
  ],
};

export default customerDeposit;
