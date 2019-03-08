const glob = require('glob')
// glob('../../{core,core_agp}/module_*/scripts/*/*.ts', function (er, files) {
const tsPaths = glob.sync('../../{core,core_agp}/module_*/scripts', {
  realpath: true
})

console.log('paths : ', tsPaths)
