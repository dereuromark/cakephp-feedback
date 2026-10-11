import { defineConfig } from 'vitepress'

const sidebar = [
  { text: 'Guide', items: [
    { text: 'Installation', link: '/guide/installation' },
    { text: 'Getting started', link: '/guide/getting-started' },
    { text: 'Access and privacy', link: '/guide/access' },
  ] },
  { text: 'Reference', items: [
    { text: 'Configuration', link: '/reference/configuration' },
    { text: 'Stores', link: '/reference/stores' },
    { text: 'Upgrading', link: '/reference/upgrading' },
  ] },
]

export default defineConfig({
  title: 'CakePHP Feedback',
  description: 'Collect feedback and screenshots with private filesystem or database storage.',
  base: '/cakephp-feedback/',
  cleanUrls: true,
  lastUpdated: true,
  srcExclude: ['README.md', 'examples/**'],
  sitemap: { hostname: 'https://dereuromark.github.io/cakephp-feedback/' },
  head: [['link', { rel: 'icon', href: '/cakephp-feedback/favicon.svg', type: 'image/svg+xml' }]],
  themeConfig: {
    logo: '/logo.svg',
    nav: [
      { text: 'Installation', link: '/guide/installation' },
      { text: 'Getting started', link: '/guide/getting-started' },
      { text: 'Reference', link: '/reference/configuration' },
    ],
    sidebar,
    socialLinks: [{ icon: 'github', link: 'https://github.com/dereuromark/cakephp-feedback' }],
    search: { provider: 'local' },
    editLink: { pattern: 'https://github.com/dereuromark/cakephp-feedback/edit/master/docs/:path' },
    footer: { message: 'Released under the MIT License.', copyright: 'Copyright Mark Scherer' },
  },
})
