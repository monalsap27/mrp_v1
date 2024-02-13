import Layout from '@/layout';

const salesMaster = {
  path: '/sales-master',
  component: Layout,
  redirect: '/sales-master/member',
  name: 'Master',
  meta: {
    title: 'Master',
    icon: 'component',
    permissions: ['view menu sales'],
  },
  children: [
    {
      path: 'PaymentMethod',
      component: () => import('@/views/sales/master/PaymentMethod/List'),
      name: 'PaymentMethod',
      meta: {
        title: 'Payment Method',
        icon: 'cc-apple-pay',
        permissions: ['view menu sales'],
      },
    },
    {
      path: 'Bank',
      component: () => import('@/views/sales/master/Bank/List'),
      name: 'Bank',
      meta: {
        title: 'Bank',
        icon: 'bank',
        permissions: ['view menu sales'],
      },
    },
    {
      path: 'Price',
      component: () => import('@/views/sales/master/Price/List'),
      name: 'PriceList',
      meta: {
        title: 'Price',
        icon: 'brand',
        permissions: ['view menu sales'],
      },
    },
    {
      path: 'Tenor',
      component: () => import('@/views/sales/master/Tenor/List'),
      name: 'TenorList',
      meta: {
        title: 'Tenor',
        icon: 'credit-card',
        permissions: ['view menu sales'],
      },
    },
  ],
};

export default salesMaster;
