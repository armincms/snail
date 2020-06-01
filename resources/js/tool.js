Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'snail',
      path: '/snail',
      component: require('./components/Tool'),
    },
  ])
})
