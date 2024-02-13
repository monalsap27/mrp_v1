import Layout from '@/layout';

const productionRequest = {
  path: '/production-request',
  component: Layout,
  redirect: 'request',
  children: [
    {
      path: 'list',
      component: () => import('@/views/production/request/List'),
      name: 'Request',
      meta: {
        title: 'Request',
        icon: 'hand-holding-box',
        noCache: true,
      },
    },
    {
      path: 'detailConfirmation/:id(\\d+)',
      component: () => import('@/views/production/request/detailConfirmation'),
      name: 'Detail Confirmation',
      meta: {
        title: 'Detail Confirmation',
        noCache: true,
        icon: '',
        permissions: ['view menu product'],
      },
      hidden: true,
    },
  ],
};

export default productionRequest;
