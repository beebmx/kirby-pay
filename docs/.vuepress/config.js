module.exports = {
  title: 'Kirby Pay',
  description: 'Make payments with Kirby',
  base: '/kirby-pay/',
  themeConfig: {
    logo: '/images/icon.png',
    title: false,
    repo: 'beebmx/kirby-pay',
    smoothScroll: false,
    nav: [
      { text: 'Home', link: '/' },
      { text: 'Guide', link: '/guide/' },
      { text: 'API', link: '/api/' },
    ],
    sidebar: {
      '/guide/': [{
          title: 'Kirby Pay',
          collapsable: false,
          children: [
            '',
            'getting-started',
            'snippets',
            'options',
            'styles',
            'languages',
            'webhooks',
            'hooks',
            'development',
          ]
        }],
      '/api/': [{
          title: 'API',
          collapsable: false,
          sidebarDepth: 3,
          children: [
            '',
            'payment',
            'customer',
            'resource',
            'elements',
          ]
        }],
    },
    extraWatchFiles: ['**/*.md', '**/*.vue'],
  }
}

