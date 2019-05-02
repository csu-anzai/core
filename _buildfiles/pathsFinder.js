const globby = require("globby");
const fs = require("fs");
// const packageConfig = require("./packageConfig.json");
const packageConfig = require("./../../project/packageconfig.json");

module.exports = {

    getTsPaths: async () => {
        let modulesPaths = null;

        try {
            let moduleMap = [];
            for (let coreEntry in packageConfig) {
                for (let i = 0; i < packageConfig[coreEntry].length; i++) {
                    moduleMap.push(
                        "../../" + coreEntry + "/" + packageConfig[coreEntry][i] + "/scripts/**/*.ts"
                    )
                }
            }
            modulesPaths = await globby(moduleMap);
            //console.log("included ts files : ", modulesPaths);
            return modulesPaths;
        } catch (e) {
            console.log(e);
        }
    },

    getLessPaths: async () => {
        let lessPaths = null;

        try {
            let moduleMap = [];
            moduleMap.push("../../core/module_v4skin/admin/skins/kajona_v4/less/bootstrap.less");

            for (let coreEntry in packageConfig) {
                for (let i = 0; i < packageConfig[coreEntry].length; i++) {

                    if (packageConfig[coreEntry][i] === "_buildfiles") {
                        continue;
                    }

                    if (packageConfig[coreEntry][i] === "module_v4skin") {
                        continue;
                    }

                    moduleMap.push(
                        "../../" + coreEntry + "/" + packageConfig[coreEntry][i] + "/**/less/**/*.less"
                    )
                }
            }
            moduleMap.push("../../project/**/less/*.less");

            lessPaths = await globby(moduleMap);

            let file = ' /* auto generated, do not change */\n';
            for (let i = 0; i < lessPaths.length; i++) {
                file += ' @import "../../../../'+lessPaths[i]+'";\n';
            }

            await fs.writeFile(__dirname+'/../module_v4skin/admin/skins/kajona_v4/less/styles.less', file, function(er) {
                if (er !== null) {
                    console.log(er);
                }
            });

            return lessPaths;
        } catch (e) {
            console.log(e);
        }
    }
};
