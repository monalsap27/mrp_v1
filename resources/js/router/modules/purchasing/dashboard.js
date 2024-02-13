import Layout from '@/layout';

const purchasingDashboard = {
  path: '/purchasing',
  component: Layout,
  redirect: 'purchasing',
  children: [
    {
      path: '',
      component: () => import('@/views/purchasing/dashboard/index'),
      name: 'Dashboard',
      meta: {
        title: 'dashboard',
        icon: 'dashboard',
        noCache: true,
      },
    },
  ],
};

export default purchasingDashboard;
