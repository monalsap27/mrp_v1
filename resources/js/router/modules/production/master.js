import Layout from '@/layout';

const productMaster = {
  path: '/production-master',
  component: Layout,
  redirect: '/production-master/categories',
  name: 'Mater',
  meta: {
    title: 'Master',
    icon: 'component',
    permissions: ['view menu product master'],
  },
  children: [
    {
      path: 'categories',
      component: () => import('@/views/production/master/categories/List'),
      name: 'CategoriesList',
      meta: {
        title: 'Categories',
        icon: 'nested',
        permissions: ['manage master categories'],
      },
    },
    {
      path: 'unit',
      component: () => import('@/views/production/master/unit/List'),
      name: 'UnitList',
      meta: {
        title: 'Unit',
        icon: 'el-icon-info',
        permissions: ['manage master unit'],
      },
    },
    {
      path: 'type',
      component: () => import('@/views/production/master/type/List'),
      name: 'TypeList',
      meta: {
        title: 'Type',
        icon: 'el-icon-s-open',
        permissions: ['manage master type'],
      },
    },
    {
      path: 'workstation',
      component: () => import('@/views/production/master/workstation/List'),
      name: 'WorkstationList',
      meta: {
        title: 'Workstation',
        icon: 'el-icon-question',
        permissions: ['manage master workstation'],
      },
    },
  ],
};

export default productMaster;
