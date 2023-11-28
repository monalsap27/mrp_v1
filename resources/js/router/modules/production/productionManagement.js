import Layout from '@/layout';
import productionWorkstation from './workstation';

const productionManagement = {
  path: '/production-management',
  component: Layout,
  redirect: 'workstation',
  name: 'Production Management',
  meta: {
    title: 'Production Management',
    icon: 'industry-alt',
    noCache: true,
  },
  children: [
    productionWorkstation,
    {
      path: 'manufacturing-order',
      component: () => import('@/views/production/ManufacturingOrder/List'),
      name: 'Manufaturing Order',
      meta: {
        title: 'Manufacturing Order',
        icon: 'conveyor-belt',
        permissions: ['view menu manufaturing order'],
      },
    },
    {
      path: 'approval',
      component: () =>
        import('@/views/production/ManufacturingOrder/ListApproval'),
      name: 'Approval',
      meta: {
        title: 'Approval',
        icon: 'el-icon-document-checked',
        permissions: ['view menu approval manufaturing'],
      },
    },
    {
      path: 'detailProgress/:id(\\d+)',
      component: () =>
        import('@/views/production/ManufacturingOrder/detailProgress'),
      name: 'Detail Approval',
      meta: {
        title: 'Detail Progress',
        noCache: true,
        icon: 'edit',
        permissions: ['view menu approval product'],
      },
      hidden: true,
    },
  ],
};

export default productionManagement;
