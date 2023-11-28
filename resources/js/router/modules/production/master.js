import Layout from '@/layout';

const productMaster = {
  path: '/production-master',
  component: Layout,
  redirect: '/production-master/categories',
  name: 'Master',
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
        icon: 'apps-add',
        permissions: ['manage master categories'],
      },
    },
    {
      path: 'unit',
      component: () => import('@/views/production/master/unit/List'),
      name: 'UnitList',
      meta: {
        title: 'Unit',
        icon: 'gauge-circle-bolt',
        permissions: ['manage master unit'],
      },
    },
    {
      path: 'type',
      component: () => import('@/views/production/master/type/List'),
      name: 'TypeList',
      meta: {
        title: 'Type',
        icon: 'box',
        permissions: ['manage master type'],
      },
    },
    {
      path: 'supplier',
      component: () => import('@/views/production/master/supplier/List'),
      name: 'SupplierList',
      meta: {
        title: 'Supplier',
        icon: '3m',
        permissions: ['manage master supplier'],
      },
    },
  ],
};

export default productMaster;
