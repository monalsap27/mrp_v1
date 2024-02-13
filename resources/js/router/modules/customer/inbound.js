import Layout from '@/layout';

const customerInbound = {
  path: '/customer-inbound',
  component: Layout,
  redirect: '/customer-inbound/list',
  name: 'Inbound',
  meta: {
    title: 'Inbound',
    icon: 'component',
    permissions: ['view menu customer'],
  },
  children: [
    {
      path: 'list',
      component: () => import('@/views/customer/inbound/List'),
      name: 'Inbound',
      meta: {
        title: 'Inbound',
        icon: 'farm',
        permissions: ['view menu customer'],
      },
    },
  ],
};

export default customerInbound;
