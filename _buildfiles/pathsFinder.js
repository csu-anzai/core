const globby = require("globby");
// const packageConfig = require("./packageConfig.json");
const packageConfig = require("../../project/packageconfig.json");

module.exports = {
  getPaths: async () => {
    var modulesPaths = null;
    var coreModules = packageConfig.core.map(el => {
      return "../../core/".concat(el).concat("/scripts/**/*.ts");
    });
    var coreAgpModules = packageConfig.core_agp.map(el => {
      return "../../core_agp/".concat(el).concat("/scripts/**/*.ts");
    });
    var coreCustomerModules = packageConfig.core_customer.map(el => {
      return "../../core_customer/".concat(el).concat("/scripts/**/*.ts");
    });
    var modules = coreModules
      .concat(coreAgpModules)
      .concat(coreCustomerModules);
    try {
      modulesPaths = await globby(modules);
      console.log("included modules : ", modulesPaths);
      return modulesPaths;
    } catch (e) {
      console.log(e);
    }
  }
};
