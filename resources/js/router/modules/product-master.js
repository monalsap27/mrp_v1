import Layout from '@/layout';

const productMaster = {
  path: '/product-master',
  component: Layout,
  redirect: '/product-master/categories',
  name: 'Mater',
  meta: {
    title: 'productMaster',
    icon: 'component',
    permissions: ['view menu product master'],
  },
  children: [
    {
      path: 'categories',
      component: () => import('@/views/product/master/categories/List'),
      name: 'CategoriesList',
      meta: {
        title: 'Categories',
        icon: 'nested',
        permissions: ['manage master categories'],
      },
    },
    {
      path: 'unit',
      component: () => import('@/views/product/master/unit/List'),
      name: 'UnitList',
      meta: {
        title: 'Unit',
        icon: 'el-icon-info',
        permissions: ['manage master unit'],
      },
    },
    {
      path: 'material',
      component: () => import('@/views/product/master/material/List'),
      name: 'MaterialList',
      meta: {
        title: 'Material',
        icon: 'el-icon-question',
        permissions: ['manage master material'],
      },
    },
    {
      path: 'type',
      component: () => import('@/views/product/master/type/List'),
      name: 'TypeList',
      meta: {
        title: 'Type',
        icon: 'el-icon-s-open',
        permissions: ['manage master type'],
      },
    },
  ],
};

export default productMaster;
