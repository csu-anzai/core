/**
 * Used for builds (local builds or buildserver) started via ant
 */
let SpecReporter = require('jasmine-spec-reporter').SpecReporter;

exports.config = {
    SELENIUM_PROMISE_MANAGER : 0,//disables selenium control flow
    seleniumAddress: 'http://localhost:4444/wd/hub',
    baseUrl: 'https://localhost',//set dynamically in onPrepare
    specs: [
        'install-spec.js',
        'login-spec.js',
        '../../temp/kajona/core*/module_*/tests/selenium/*-spec.js',
        '../../temp/kajona/files/extract/module_*/tests/selenium/*-spec.js'
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

        //add jesamine spec reporter
        _addJasmineSpecReporter();

        // returning the promise makes protractor wait for the reporter config (protractor-screenshoter-plugin) before executing tests
        return global.browser.getProcessedConfig().then(function (config) {
            //it is ok to be empty
        });
    }
};

const _addJasmineSpecReporter = function() {
    jasmine.getEnv().addReporter(new SpecReporter({
        spec: {
            displayDuration: true,
            displayErrorMessages: true,
            displayFailed: true,
            displayPending: true,
            displayStacktrace: true,
            displaySuccessful: true
        },
        suite: {
            displayNumber: true
        },
        summary: {
            displayDuration: true,
            displayErrorMessages: true,
            displayFailed: true,
            displayPending: true,
            displayStacktrace: true,
            displaySuccessful: true
        },
    }));
};

const _getSeleniumBasePath = function() {
    return __dirname + '/../../temp/kajona/core/_buildfiles/jstests/selenium';
};

const _getBaseUrl = function() {
    const path = require('path');
    const strPathToProject = path.join(__dirname, "/../../../../");//path to project folder
    const strProjectName = path.basename(strPathToProject);//determine project folder name
    return browser.baseUrl + "/" + strProjectName + "/core/_buildfiles/temp/kajona";
};
