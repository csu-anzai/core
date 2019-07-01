import { configure } from '@storybook/vue'

function loadStories () {
  // const req = require.context('../stories', true, /\.stories\.js$/);
  // req.keys().forEach(filename => req(filename));
  require('./stories/index.js');
}

configure(loadStories, module)
