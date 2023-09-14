import Layout from '@/layout';

const productionRoute = {
  path: '/production',
  component: Layout,
  redirect: 'production',
  children: [
    {
      path: '',
      component: () => import('@/views/production/dashboard/index'),
      name: 'Dashboard',
      meta: { title: 'dashboard', icon: 'dashboard', noCache: false },
    },
  ],
};

export default productionRoute;
