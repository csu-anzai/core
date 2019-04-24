const globby = require("globby");
const packageConfig = require("./packageConfig");

module.exports = {
  getPaths: async () => {
    var modulesPaths = null;
    var coreModules = packageConfig.whiteList.core.map(el => {
      return "../../core/".concat(el).concat("/scripts/**/*.ts");
    });
    var coreAgpModules = packageConfig.whiteList.core_agp.map(el => {
      return "../../core_agp/".concat(el).concat("/scripts/**/*.ts");
    });
    var coreCustomerModules = packageConfig.whiteList.core_customer.map(el => {
      return "../../core_customer/".concat(el).concat("/scripts/**/*.ts");
    });
    var modules = coreModules
      .concat(coreAgpModules)
      .concat(coreCustomerModules);
    try {
      modulesPaths = await globby(modules);
      return modulesPaths;
    } catch (e) {
      console.log(e);
    }
  }
};
