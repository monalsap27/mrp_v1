const productionWorkstation = {
  path: '/production-workstation',
  component: () => import('@/views/production/workstation/index'), // Parent router-view
  redirect: 'workstation',
  meta: {
    title: 'Workstation',
    icon: 'clipboard',
    permissions: ['view menu product workstation'],
  },
  children: [
    {
      path: 'workstation',
      component: () => import('@/views/production/workstation/workstationList'),
      name: 'Workstation',
      meta: {
        title: 'Workstation',
        icon: '',
        noCache: false,
      },
    },
    {
      path: 'create',
      component: () =>
        import('@/views/production/workstation/workstationCreate'),
      name: 'Create Workstation',
      meta: {
        title: 'Create Workstation',
        icon: 'edit',
        permissions: ['manage workstation'],
      },
      hidden: true,
    },
    {
      path: 'edit/:id(\\d+)',
      component: () => import('@/views/production/workstation/Edit'),
      name: 'Edit Workstation',
      meta: {
        title: 'Edit Workstation',
        noCache: true,
        icon: 'edit',
        permissions: ['manage workstation'],
      },
      hidden: true,
    },
    {
      path: 'view/:id(\\d+)',
      component: () => import('@/views/production/workstation/workstationView'),
      name: 'View Workstation',
      meta: {
        title: 'View Workstation',
        noCache: true,
        icon: 'view',
        permissions: ['manage workstation'],
      },
      hidden: true,
    },
    {
      path: 'group',
      component: () =>
        import('@/views/production/workstation/workstationGroupList'),
      name: 'Workstation Group',
      meta: {
        title: 'Workstation Group',
        icon: '',
        permissions: ['manage master workstation'],
      },
    },
    {
      path: 'create-group',
      component: () =>
        import('@/views/production/workstation/workstationGroupCreate'),
      name: 'Group Workstation',
      meta: {
        title: 'Group Workstation',
        icon: '',
        permissions: ['manage workstation'],
      },
      hidden: true,
    },
    {
      path: 'edit-group/:id(\\d+)',
      component: () =>
        import('@/views/production/workstation/workstationGroupEdit'),
      name: 'Group Workstation',
      meta: {
        title: 'Group Workstation',
        icon: '',
        permissions: ['manage workstation'],
      },
      hidden: true,
    },
  ],
};

export default productionWorkstation;
