import Layout from '@/layout';

const productionProduct = {
  path: '/production-product',
  component: Layout,
  redirect: '/production-product/list',
  name: 'Product and Storage',
  meta: {
    title: 'Product and Storage',
    icon: 'form',
  },
  children: [
    {
      path: 'list',
      component: () => import('@/views/production/product/List'),
      name: 'List Product',
      meta: {
        title: 'List Product',
        icon: 'el-icon-document',
        permissions: ['view menu product'],
      },
    },
    {
      path: 'create',
      component: () => import('@/views/production/product/Create'),
      name: 'CreateProduct',
      meta: {
        title: 'createProduct',
        icon: 'edit',
        permissions: ['view menu product'],
      },
      hidden: true,
    },
    {
      path: 'edit/:id(\\d+)',
      component: () => import('@/views/production/product/Edit'),
      name: 'Edit Workstation',
      meta: {
        title: 'Edit Workstation',
        noCache: true,
        icon: 'edit',
        permissions: ['view menu product'],
      },
      hidden: true,
    },
    {
      path: 'approval',
      component: () => import('@/views/production/product/ListApproval'),
      name: 'Approval',
      meta: {
        title: 'Approval',
        icon: 'el-icon-document-checked',
        permissions: ['view menu approval product'],
      },
    },
    {
      path: 'detailApproval/:id(\\d+)',
      component: () => import('@/views/production/product/DetailApproval'),
      name: 'Detail Approval',
      meta: {
        title: 'Detail Approval',
        noCache: true,
        icon: 'edit',
        permissions: ['view menu approval product'],
      },
      hidden: true,
    },
    {
      path: 'detailProduct/:id(\\d+)',
      component: () => import('@/views/production/product/DetailProduct'),
      name: 'Detail Approval',
      meta: {
        title: 'Detail Approval',
        noCache: true,
        icon: 'edit',
        permissions: ['view menu approval product'],
      },
      hidden: true,
    },
    {
      path: 'stock',
      component: () => import('@/views/production/product/ListStock'),
      name: 'Stock',
      meta: {
        title: 'Stock',
        icon: 'box-open-full',
        permissions: ['view menu product'],
      },
    },
    {
      path: 'mutasiStock/:id(\\d+)',
      component: () => import('@/views/production/product/ListMutasiStock'),
      name: 'Mutasi Stock',
      meta: {
        title: 'Mutasi Stock',
        noCache: true,
        icon: 'edit',
        permissions: ['view menu approval product'],
      },
      hidden: true,
    },
  ],
};

export default productionProduct;
