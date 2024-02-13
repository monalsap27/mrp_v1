import Layout from '@/layout';

const customerDashboard = {
  path: '/customer',
  component: Layout,
  redirect: 'customer',
  children: [
    {
      path: '',
      component: () => import('@/views/customer/dashboard/index'),
      name: 'Dashboard',
      meta: {
        title: 'dashboard',
        icon: 'dashboard',
        noCache: true,
      },
    },
  ],
};

export default customerDashboard;
