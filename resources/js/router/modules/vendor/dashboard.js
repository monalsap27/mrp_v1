import Layout from '@/layout';

const vendorDashboard = {
  path: '/vendor',
  component: Layout,
  redirect: 'vendor',
  children: [
    {
      path: '',
      component: () => import('@/views/vendor/dashboard/index'),
      name: 'Dashboard',
      meta: {
        title: 'dashboard',
        icon: 'dashboard',
        noCache: true,
      },
    },
  ],
};

export default vendorDashboard;
