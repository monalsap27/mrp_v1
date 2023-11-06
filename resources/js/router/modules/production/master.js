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
        icon: '',
        permissions: ['manage master categories'],
      },
    },
    {
      path: 'unit',
      component: () => import('@/views/production/master/unit/List'),
      name: 'UnitList',
      meta: {
        title: 'Unit',
        icon: '',
        permissions: ['manage master unit'],
      },
    },
    {
      path: 'type',
      component: () => import('@/views/production/master/type/List'),
      name: 'TypeList',
      meta: {
        title: 'Type',
        icon: '',
        permissions: ['manage master type'],
      },
    },
    {
      path: 'supplier',
      component: () => import('@/views/production/master/supplier/List'),
      name: 'SupplierList',
      meta: {
        title: 'Supplier',
        icon: '',
        permissions: ['manage master supplier'],
      },
    },
  ],
};

export default productMaster;
