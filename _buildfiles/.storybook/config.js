import { configure } from '@storybook/vue';
const path = require('path')
function requireAll(requireContext) {
  return requireContext.keys().map(requireContext);
}

function loadStories() {
  requireAll(require.context("../../", true, /story\.ts?$/));
}

configure(loadStories, module);
