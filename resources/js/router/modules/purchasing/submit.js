import Layout from '@/layout';

const purchasingSubmit = {
  path: '/purchasing-submit',
  component: Layout,
  redirect: '/purchasing-submit/list',
  name: 'Submit',
  meta: {
    title: 'Submit',
    icon: 'component',
    permissions: ['view menu purchasing'],
  },
  children: [
    {
      path: 'list',
      component: () => import('@/views/purchasing/submit/List'),
      name: 'List Request',
      meta: {
        title: 'List Request',
        icon: 'file-circle-info',
        permissions: ['view menu purchasing'],
      },
    },
  ],
};

export default purchasingSubmit;
