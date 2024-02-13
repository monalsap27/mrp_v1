import Layout from '@/layout';

const purchasingApproval = {
  path: '/purchasing-approval',
  component: Layout,
  redirect: '/purchasing-approval/list',
  name: 'Approval',
  meta: {
    title: 'Approval',
    icon: 'component',
    permissions: ['view menu approval purchasing'],
  },
  children: [
    {
      path: 'list',
      component: () => import('@/views/purchasing/order/ListApproval'),
      name: 'List Approval',
      meta: {
        title: 'List Approval',
        icon: 'memo-circle-check',
        permissions: ['view menu approval purchasing'],
      },
    },
    {
      path: 'detailApproval/:id(\\d+)',
      component: () => import('@/views/purchasing/order/DetailApproval'),
      name: 'Detail Approval',
      meta: {
        title: 'Detail Approval',
        noCache: true,
        icon: 'edit',
        permissions: ['view menu approval purchasing'],
      },
      hidden: true,
    },
  ],
};

export default purchasingApproval;
