import Layout from '@/layout';

const pokemonPage = {
  path: '/pokemon',
  component: Layout,
  redirect: 'pokemon',
  children: [
    {
      path: '',
      component: () => import('@/views/pokemon/index'),
      name: 'Pokemon',
      meta: {
        title: 'Pokemon',
        icon: 'dashboard',
        noCache: true,
      },
    },
  ],
};

export default pokemonPage;
