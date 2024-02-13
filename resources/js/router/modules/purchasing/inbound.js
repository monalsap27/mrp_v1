import Layout from '@/layout';

const purchasingInbound = {
  path: '/purchasing-inbound',
  component: Layout,
  redirect: '/purchasing-inbound/list',
  name: 'Inbound',
  meta: {
    title: 'Inbound',
    icon: 'component',
    permissions: ['view menu purchasing'],
  },
  children: [
    {
      path: 'list',
      component: () => import('@/views/purchasing/inbound/List'),
      name: 'Inbound',
      meta: {
        title: 'Inbound',
        icon: 'farm',
        permissions: ['view menu purchasing'],
      },
    },
  ],
};

export default purchasingInbound;
