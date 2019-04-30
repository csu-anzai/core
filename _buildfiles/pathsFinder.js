const globby = require("globby");
// const packageConfig = require("./packageConfig.json");
const packageConfig = require("../../project/packageconfig.json");

module.exports = {
  getPaths: async () => {
    var modulesPaths = null;

    try {
        var moduleMap = [];
        for (var core in packageConfig) {
            for (var i = 0; i < packageConfig[core].length; i++) {
                moduleMap.push(
                    "../../"+core+"/"+packageConfig[core][i]+"/scripts/**/*.ts"
                )
            }
        }
      console.log("map", moduleMap);
      modulesPaths = await globby(moduleMap);
      console.log("included ts files : ", modulesPaths);
      return modulesPaths;
    } catch (e) {
      console.log(e);
    }
  }
};
