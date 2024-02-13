import Layout from '@/layout';

const customerMaster = {
  path: '/customer-master',
  component: Layout,
  redirect: '/customer-master/member',
  name: 'Master',
  meta: {
    title: 'Master',
    icon: 'component',
    permissions: ['view menu customer'],
  },
  children: [
    // {
    //   path: 'member',
    //   component: () => import('@/views/customer/master/member/List'),
    //   name: 'MemberList',
    //   meta: {
    //     title: 'Member',
    //     icon: 'hr-group',
    //     permissions: ['view menu customer'],
    //   },
    // },
    {
      path: 'postalcode',
      component: () => import('@/views/customer/master/postalcode/List'),
      name: 'PostalCodeList',
      meta: {
        title: 'Postal Code',
        icon: 'mailbox',
        permissions: ['view menu customer'],
      },
    },
  ],
};

export default customerMaster;
