import Layout from '@/layout';

const salesShipping = {
  path: '/sales-shipping',
  component: Layout,
  redirect: '/sales-shipping/list',
  name: 'Shipping',
  meta: {
    title: 'Shipping',
    icon: 'component',
    permissions: ['view menu request'],
  },
  children: [
    {
      path: 'list',
      component: () => import('@/views/sales/shipping/List'),
      name: 'Delivery Order',
      meta: {
        title: 'Delivery Order',
        icon: 'shipping-fast',
        permissions: ['view menu request'],
      },
    },
    {
      path: 'create',
      component: () => import('@/views/sales/shipping/Create'),
      name: 'New Delivery Order',
      meta: {
        title: 'New Delivery Order',
        icon: 'edit',
        permissions: ['view menu request'],
      },
      hidden: true,
    },
    {
      path: 'detailShipment/:id(\\d+)',
      component: () => import('@/views/sales/shipping/DetailShipment'),
      name: 'Detail Shipment',
      meta: {
        title: 'Detail Shipment',
        noCache: true,
        icon: 'edit',
        permissions: ['view menu request'],
      },
      hidden: true,
    },
  ],
};

export default salesShipping;
