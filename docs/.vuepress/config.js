module.exports = {
  title: 'Kirby Pay',
  description: 'Make payments with Kirby',
  base: '/kirby-pay/',
  themeConfig: {
    logo: '/images/icon.png',
    title: false,
    nav: [
      { text: 'Home', link: '/' },
      { text: 'Guide', link: '/guide/' },
      { text: 'API', link: '/api/' },
      { text: 'Github', link: 'https://github.com/beebmx/kirby-pay' }
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
            'webhooks',
            'hooks',
            'development',
          ]
        }],
      '/api/': [{
          title: 'Kirby Pay API',
          collapsable: false,
          children: [
            ''
          ]
        }],
    },


  },
  extraWatchFiles: ["**/*.md", "**/*.styl", "**/*.vue"],
}

