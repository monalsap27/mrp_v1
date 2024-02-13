import Layout from '@/layout';

const vendorRequest = {
  path: '/vendor-request',
  component: Layout,
  redirect: '/vendor-request/list',
  name: 'Request',
  meta: {
    title: 'Request',
    icon: 'component',
    permissions: ['view menu vendor'],
  },
  children: [
    {
      path: 'list',
      component: () => import('@/views/vendor/request/List'),
      name: 'Request',
      meta: {
        title: 'Request',
        icon: 'hand-holding-box',
        permissions: ['view menu request'],
      },
    },
    {
      path: 'detailTransaction/:id(\\d+)',
      component: () => import('@/views/vendor/request/DetailTransaction'),
      name: 'Detail Transaction',
      meta: {
        title: 'Detail Transaction',
        noCache: true,
        icon: 'edit',
        permissions: ['view menu request'],
      },
      hidden: true,
    },
    {
      path: 'detailConfirmation/:id(\\d+)',
      component: () => import('@/views/vendor/request/DetailConfirmation'),
      name: 'Detail Confirmation',
      meta: {
        title: 'Detail Confirmation',
        noCache: true,
        icon: 'edit',
        permissions: ['view menu request'],
      },
      hidden: true,
    },
  ],
};

export default vendorRequest;
