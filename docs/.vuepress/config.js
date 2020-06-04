module.exports = {
  title: 'Kirby Pay',
  description: 'Make online payments with Kirby',
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
  },
  head: [
    ['link', {rel: 'shortcut icon', href: '/images/favicon.ico'}],
    ['link', {rel: 'icon', type: 'image/x-icon', sizes:'16x16 32x32', href: '/images/favicon.ico'}],
    ['link', {rel: 'apple-touch-icon', sizes:'152x152', href: '/images/favicon-152-precomposed.png'}],
    ['link', {rel: 'apple-touch-icon', sizes:'144x144', href: '/images/favicon-144-precomposed.png'}],
    ['link', {rel: 'apple-touch-icon', sizes:'120x120', href: '/images/favicon-120-precomposed.png'}],
    ['link', {rel: 'apple-touch-icon', sizes:'114x114', href: '/images/favicon-114-precomposed.png'}],
    ['link', {rel: 'apple-touch-icon', sizes:'180x180', href: '/images/favicon-180-precomposed.png'}],
    ['link', {rel: 'apple-touch-icon', sizes:'72x72', href: '/images/favicon-72-precomposed.png'}],
    ['link', {rel: 'apple-touch-icon', sizes:'57x57', href: '/images/favicon-57.png'}],
    ['link', {rel: 'icon', sizes:'32x32', href: '/images/favicon-32.png'}],
    ['link', {rel: 'icon', sizes:'192x192', href: '/images/favicon-192.png'}],
  ],
  plugins: [
    [
      '@vuepress/google-analytics',
      {
        'ga': 'UA-168563459-1'
      }
    ]
  ]
}

