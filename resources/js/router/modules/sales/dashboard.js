import Layout from '@/layout';

const salesDashboard = {
  path: '/sales',
  component: Layout,
  redirect: 'sales',
  children: [
    {
      path: '',
      component: () => import('@/views/sales/dashboard/index'),
      name: 'Dashboard',
      meta: {
        title: 'dashboard',
        icon: 'dashboard',
        noCache: true,
      },
    },
  ],
};

export default salesDashboard;
