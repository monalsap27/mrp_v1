import Layout from '@/layout';

const purchasingMaster = {
  path: '/purchasing-master',
  component: Layout,
  redirect: '/purchasing-master/unit',
  name: 'Master',
  meta: {
    title: 'Master',
    icon: 'component',
    permissions: ['view menu purchasing'],
  },
  children: [
    {
      path: 'unit',
      component: () => import('@/views/purchasing/master/unit/List'),
      name: 'UnitList',
      meta: {
        title: 'Unit',
        icon: 'gauge-circle-bolt',
        permissions: ['view menu purchasing'],
      },
    },
    {
      path: 'setting',
      component: () => import('@/views/purchasing/master/setting/List'),
      name: 'Setting',
      meta: {
        title: 'Setting',
        icon: 'it',
        permissions: ['view menu purchasing'],
      },
    },
    {
      path: 'supplier',
      component: () => import('@/views/purchasing/master/supplier/List'),
      name: 'SupplierList',
      meta: {
        title: 'Supplier',
        icon: '3m',
        permissions: ['manage master supplier'],
      },
    },
  ],
};

export default purchasingMaster;
