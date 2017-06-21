/**
 * Used for executing selenium tests locally via IDE
 */
exports.config = {
    SELENIUM_PROMISE_MANAGER : 0,//disables selenium control flow
    seleniumAddress: 'http://localhost:4444/wd/hub',
    baseUrl: 'https://localhost',//set dynamically in onPrepare
    specs: [
        // 'install-spec.js',
        './login-spec.js',
        // '../../../../core*/module_*/tests/selenium/*-spec.js',
        '../../../../core_agp/module_prozessverwaltung/tests/selenium/unterdmimension_range-spec.js'
    ],
    capabilities: {
        browserName: 'chrome',
        chromeOptions: {
            args: ['--no-sandbox']
        }
    },
    jasmineNodeOpts: {
        defaultTimeoutInterval: 720000 // 12 minutes
    },
    plugins: [
    {
        package: 'protractor-screenshoter-plugin',
        screenshotPath: '../build/jstests/screenshots',
        clearFoldersBeforeTest: true
    }],
    onPrepare: function () {
        /** If you are testing against a non-angular site - set ignoreSynchronization setting to true */
        browser.ignoreSynchronization = true;

        /** base path of the selenium */
        const strBasePath = _getSeleniumBasePath();

        /**add requireHelper to global variable */
        global.requireHelper = function (relativePath) {// "relativePath" - path, relative to "basePath" variable
            return require(strBasePath + relativePath);
        };

        /** Set baseUrl dynamically */
        browser.baseUrl = _getBaseUrl();

        // returning the promise makes protractor wait for the reporter config before executing tests
        return global.browser.getProcessedConfig().then(function (config) {
            //it is ok to be empty
        });
    }
};

const _getSeleniumBasePath = function() {
    return __dirname + '/../../jstests/selenium';
};

const _getBaseUrl = function() {
    const path = require('path');
    const strPathToProject = path.join(__dirname, "/../../../../");//path to project folder
    const strProjectName = path.basename(strPathToProject);//determine project folder name
    return browser.baseUrl + "/" + strProjectName;
};

