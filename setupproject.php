<?php
/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/


class class_project_setup
{

    private static $strRealPath = "";

    public static function setUp()
    {

        self::$strRealPath = __DIR__ . "/../";

        echo "<b>ARTEMEON core V7 project setup.</b>\nCreates the folder-structure required to build a new project.\n\n";

        $strCurFolder = __DIR__;

        echo "core-path: " . $strCurFolder . ", folder found: " . substr($strCurFolder, -4) . "\n";

        if (substr($strCurFolder, -4) != "core") {
            echo "current folder must be named core!";
            return;
        }


        $arrIncludedModules = null;
        if (is_file(self::$strRealPath . "project/packageconfig.json")) {
            $arrIncludedModules = json_decode(file_get_contents(self::$strRealPath."project/packageconfig.json"), true);
        }

        //Module-Constants
        $arrModules = array();
        foreach (scandir(self::$strRealPath) as $strRootFolder) {
            if (!isset($arrExcludedModules[$strRootFolder])) {
                $arrExcludedModules[$strRootFolder] = array();
            }

            if (strpos($strRootFolder, "core") === false) {
                continue;
            }

            foreach (scandir(self::$strRealPath . "/" . $strRootFolder) as $strOneModule) {
                if (preg_match("/^(module|_)+.*/i", $strOneModule) && (!is_array($arrIncludedModules) || (isset($arrIncludedModules[$strRootFolder]) && in_array($strOneModule, $arrIncludedModules[$strRootFolder])))) {
                    $arrModules[] = $strRootFolder . "/" . $strOneModule;
                }
            }
        }

        self::checkDir("/bin", false);
        self::createBinReadme();
        self::checkDir("/project/log", true);
        self::checkDir("/project/dbdumps", true);
        self::checkDir("/project/module_system/system/config", true);
        self::checkDir("/project/temp", true);
        self::checkDir("/files/cache", true);
        self::checkDir("/files/downloads/default", true);
        self::checkDir("/files/images", true);
        self::checkDir("/files/extract", true);

        echo "searching for files on root/project-path...\n";
        foreach ($arrModules as $strSingleModule) {
            if (!is_dir(self::$strRealPath . "/" . $strSingleModule)) {
                continue;
            }

            $arrContent = scandir(self::$strRealPath . "/" . $strSingleModule);
            foreach ($arrContent as $strSingleEntry) {
                if (substr($strSingleEntry, -5) == ".root" && !is_file(self::$strRealPath . "/" . substr($strSingleEntry, 0, -5))) {
                    copy(self::$strRealPath . "/" . $strSingleModule . "/" . $strSingleEntry, self::$strRealPath . "/" . substr($strSingleEntry, 0, -5));
                }

                if (substr($strSingleEntry, -8) == ".project" && !is_file(self::$strRealPath . "/project/" . substr($strSingleEntry, 0, -8))) {
                    copy(self::$strRealPath . "/" . $strSingleModule . "/" . $strSingleEntry, self::$strRealPath . "/project/" . substr($strSingleEntry, 0, -8));
                }
            }

            if (is_dir(self::$strRealPath . "/" . $strSingleModule . "/files")) {
                self::copyFolder(self::$strRealPath . "/" . $strSingleModule . "/files", self::$strRealPath . "/files");
            }
        }


        echo "\n<b>htaccess setup</b>\n";
        self::createAllowHtaccess("/files/cache/.htaccess");
        self::createAllowHtaccess("/files/images/.htaccess");
        self::createAllowHtaccess("/files/extract/.htaccess");

        self::createDenyHtaccess("/project/.htaccess");
        self::createDenyHtaccess("/files/.htaccess");

        self::createTokenKey();
        self::createRootGitIgnore();
        self::createDefaultPackageconfig();
        self::createRootTsconfig();
        self::creatRootEslintConfig();
        self::loadNpmDependencies();
        self::scanComposer();

        echo "\n<b>Done.</b>\nIf everything went well, <a href=\"../installer.php\">open the installer</a>\n";
    }


    private static function createRootGitIgnore()
    {
        if (is_file(self::$strRealPath . "/.gitignore")) {
            return;
        }
        $content = <<<TEXT
project/temp
project/vendor
project/log
project/dbdumps
files/cache
.vscode
tsconfig.json
.eslintrc.json
TEXT;
        file_put_contents(self::$strRealPath . "/.gitignore", $content);
    }


    private static function createRootTsconfig()
    {
        if (is_file(self::$strRealPath . "/tsconfig.json")) {
            return;
        }
        $content = <<<JSON
{
  "extends": "./core/_buildfiles/tsconfig"
}
JSON;
        file_put_contents(self::$strRealPath . "/tsconfig.json", $content);
    }

    private static function creatRootEslintConfig(){
        if (is_file(self::$strRealPath . "/.eslintrc.json")) {
            return;
        }
        $content = <<<JSON
{
  "parserOptions": {
  "parser" : "@typescript-eslint/parser",
    "ecmaVersion": 2018,
    "ecmaFeatures": {
      "jsx": true
    },
    "sourceType": "module"
  },

  "env": {
    "es6": true,
    "node": true
  },

  "globals": {
    "document": false,
    "navigator": false,
    "window": false
  },

  "rules": {
    "accessor-pairs": "error",
    "arrow-spacing": ["error", { "before": true, "after": true }],
    "block-spacing": ["error", "always"],
    "brace-style": ["error", "1tbs", { "allowSingleLine": true }],
    "camelcase": ["error", { "properties": "never" }],
    "comma-dangle": [
      "error",
      {
        "arrays": "never",
        "objects": "never",
        "imports": "never",
        "exports": "never",
        "functions": "never"
      }
    ],
    "comma-spacing": ["error", { "before": false, "after": true }],
    "comma-style": ["error", "last"],
    "constructor-super": "error",
    "curly": ["error", "multi-line"],
    "dot-location": ["error", "property"],
    "eol-last": "error",
    "eqeqeq": ["error", "always", { "null": "ignore" }],
    "func-call-spacing": ["error", "never"],
    "generator-star-spacing": ["error", { "before": true, "after": true }],
    "handle-callback-err": ["error", "^(err|error)$"],
    "indent": [
      "error",
      4,
      {
        "SwitchCase": 1,
        "VariableDeclarator": 1,
        "outerIIFEBody": 1,
        "MemberExpression": 1,
        "FunctionDeclaration": { "parameters": 1, "body": 1 },
        "FunctionExpression": { "parameters": 1, "body": 1 },
        "CallExpression": { "arguments": 1 },
        "ArrayExpression": 1,
        "ObjectExpression": 1,
        "ImportDeclaration": 1,
        "flatTernaryExpressions": false,
        "ignoreComments": false
      }
    ],
    "key-spacing": ["error", { "beforeColon": false, "afterColon": true }],
    "keyword-spacing": ["error", { "before": true, "after": true }],
    "new-cap": ["error", { "newIsCap": true, "capIsNew": false }],
    "new-parens": "error",
    "no-array-constructor": "error",
    "no-caller": "error",
    "no-class-assign": "error",
    "no-compare-neg-zero": "error",
    "no-cond-assign": "error",
    "no-const-assign": "error",
    "no-constant-condition": ["error", { "checkLoops": false }],
    "no-control-regex": "error",
    "no-debugger": "error",
    "no-delete-var": "error",
    "no-dupe-args": "error",
    "no-dupe-class-members": "error",
    "no-dupe-keys": "error",
    "no-duplicate-case": "error",
    "no-empty-character-class": "error",
    "no-empty-pattern": "error",
    "no-eval": "error",
    "no-ex-assign": "error",
    "no-extend-native": "error",
    "no-extra-bind": "error",
    "no-extra-boolean-cast": "error",
    "no-extra-parens": ["error", "functions"],
    "no-fallthrough": "error",
    "no-floating-decimal": "error",
    "no-func-assign": "error",
    "no-global-assign": "error",
    "no-implied-eval": "error",
    "no-inner-declarations": ["error", "functions"],
    "no-invalid-regexp": "error",
    "no-irregular-whitespace": "error",
    "no-iterator": "error",
    "no-label-var": "error",
    "no-labels": ["error", { "allowLoop": false, "allowSwitch": false }],
    "no-lone-blocks": "error",
    "no-mixed-operators": [
      "error",
      {
        "groups": [
          ["==", "!=", "===", "!==", ">", ">=", "<", "<="],
          ["&&", "||"],
          ["in", "instanceof"]
        ],
        "allowSamePrecedence": true
      }
    ],
    "no-mixed-spaces-and-tabs": "error",
    "no-multi-spaces": "error",
    "no-multi-str": "error",
    "no-multiple-empty-lines": ["error", { "max": 1, "maxEOF": 0 }],
    "no-negated-in-lhs": "error",
    "no-new": "error",
    "no-new-func": "error",
    "no-new-object": "error",
    "no-new-require": "error",
    "no-new-symbol": "error",
    "no-new-wrappers": "error",
    "no-obj-calls": "error",
    "no-octal": "error",
    "no-octal-escape": "error",
    "no-path-concat": "error",
    "no-proto": "error",
    "no-redeclare": "error",
    "no-regex-spaces": "error",
    "no-return-assign": ["error", "except-parens"],
    "no-return-await": "error",
    "no-self-assign": "error",
    "no-self-compare": "error",
    "no-sequences": "error",
    "no-shadow-restricted-names": "error",
    "no-sparse-arrays": "error",
    "no-tabs": "error",
    "no-template-curly-in-string": "error",
    "no-this-before-super": "error",
    "no-throw-literal": "error",
    "no-trailing-spaces": "error",
    "no-undef": "error",
    "no-undef-init": "error",
    "no-unexpected-multiline": "error",
    "no-unmodified-loop-condition": "error",
    "no-unneeded-ternary": ["error", { "defaultAssignment": false }],
    "no-unreachable": "error",
    "no-unsafe-finally": "error",
    "no-unsafe-negation": "error",
    "no-unused-expressions": [
      "error",
      {
        "allowShortCircuit": true,
        "allowTernary": true,
        "allowTaggedTemplates": true
      }
    ],
    "no-unused-vars": [
      "error",
      { "vars": "all", "args": "none", "ignoreRestSiblings": true }
    ],
    "no-use-before-define": [
      "error",
      { "functions": false, "classes": false, "variables": false }
    ],
    "no-useless-call": "error",
    "no-useless-computed-key": "error",
    "no-useless-constructor": "error",
    "no-useless-escape": "error",
    "no-useless-rename": "error",
    "no-useless-return": "error",
    "no-whitespace-before-property": "error",
    "no-with": "error",
    "object-curly-spacing": ["error", "always"],
    "object-property-newline": [
      "error",
      { "allowMultiplePropertiesPerLine": true }
    ],
    "one-var": ["error", { "initialized": "never" }],
    "operator-linebreak": [
      "error",
      "after",
      { "overrides": { "?": "before", ":": "before" } }
    ],
    "padded-blocks": [
      "error",
      { "blocks": "never", "switches": "never", "classes": "never" }
    ],
    "prefer-promise-reject-errors": "error",
    "quotes": [
      "error",
      "single",
      { "avoidEscape": true, "allowTemplateLiterals": true }
    ],
    "rest-spread-spacing": ["error", "never"],
    "semi": ["error", "never"],
    "semi-spacing": ["error", { "before": false, "after": true }],
    "space-before-blocks": ["error", "always"],
    "space-before-function-paren": ["error", "always"],
    "space-in-parens": ["error", "never"],
    "space-infix-ops": "error",
    "space-unary-ops": ["error", { "words": true, "nonwords": false }],
    "spaced-comment": [
      "error",
      "always",
      {
        "line": { "markers": ["*package", "!", "/", ",", "="] },
        "block": {
          "balanced": true,
          "markers": ["*package", "!", ",", ":", "::", "flow-include"],
          "exceptions": ["*"]
        }
      }
    ],
    "symbol-description": "error",
    "template-curly-spacing": ["error", "never"],
    "template-tag-spacing": ["error", "never"],
    "unicode-bom": ["error", "never"],
    "use-isnan": "error",
    "valid-typeof": ["error", { "requireStringLiterals": true }],
    "wrap-iife": ["error", "any", { "functionPrototypeMethods": true }],
    "yield-star-spacing": ["error", "both"],
    "yoda": ["error", "never"],

    "import/export": "error",
    "import/first": "error",
    "import/no-duplicates": "error",
    "import/no-named-default": "error",
    "import/no-webpack-loader-syntax": "error",

    "node/no-deprecated-api": "error",
    "node/process-exit-as-throw": "error",

    "promise/param-names": "error",

    "standard/array-bracket-even-spacing": ["error", "either"],
    "standard/computed-property-even-spacing": ["error", "even"],
    "standard/no-callback-literal": "error",
    "standard/object-curly-even-spacing": ["error", "either"]
  }
}

JSON;
        file_put_contents(self::$strRealPath . "/.eslintrc.json", $content);
    }


    private static function createDefaultPackageconfig()
    {
        if (is_file(self::$strRealPath . "/project/packageconfig.json")) {
            return;
        }

        $cfg = new class {
            public $core = [];
        };
        foreach (scandir(self::$strRealPath."/core") as $strOneEntry) {
            if ($strOneEntry == "." || $strOneEntry == "..") {
                continue;
            }

            if (is_dir(self::$strRealPath."/core" . "/" . $strOneEntry)) {
                $cfg->core[] = $strOneEntry;
            }
        }

        file_put_contents(self::$strRealPath . "/project/packageconfig.json", json_encode($cfg, JSON_PRETTY_PRINT));
    }


    private static function createBinReadme()
    {
        $strContent = <<<TEXT

This folder should contain the following external binaries:

module_fileindexer
* `tika-app-1.17.jar` (https://tika.apache.org/)

TEXT;

        file_put_contents(self::$strRealPath . "/bin/README.md", $strContent);
    }

    private static function checkDir($strFolder, $writeable)
    {
        echo "checking dir " . self::$strRealPath . $strFolder . "\n";
        if (!is_dir(self::$strRealPath . $strFolder)) {
            mkdir(self::$strRealPath . $strFolder, 0777, true);
            echo " \t\t... directory created\n";
        } else {
            echo " \t\t... already existing.\n";
        }
        if ($writeable) {
            chmod(self::$strRealPath . $strFolder, 0777);
        }
    }

    private static function copyFolder($strSourceFolder, $strTargetFolder, $arrExcludeSuffix = array())
    {
        $arrEntries = scandir($strSourceFolder);
        foreach ($arrEntries as $strOneEntry) {
            if ($strOneEntry == "." || $strOneEntry == ".."  || in_array(substr($strOneEntry, strrpos($strOneEntry, ".")), $arrExcludeSuffix)) {
                continue;
            }

            if (is_file($strSourceFolder . "/" . $strOneEntry) && !is_file($strTargetFolder . "/" . $strOneEntry)) {
                //echo "copying file ".$strSourceFolder."/".$strOneEntry." to ".$strTargetFolder."/".$strOneEntry."\n";
                if (!is_dir($strTargetFolder)) {
                    mkdir($strTargetFolder, 0777, true);
                }

                copy($strSourceFolder . "/" . $strOneEntry, $strTargetFolder . "/" . $strOneEntry);
                chmod($strTargetFolder . "/" . $strOneEntry, 0777);
            } elseif (is_dir($strSourceFolder . "/" . $strOneEntry)) {
                self::copyFolder($strSourceFolder . "/" . $strOneEntry, $strTargetFolder . "/" . $strOneEntry, $arrExcludeSuffix);
            }
        }
    }

    private static function createTokenKey()
    {
        // generate also token file for the installer api
        echo "Generate token key\n";

        $tokenFile = self::$strRealPath . "project/token.key";
        file_put_contents($tokenFile, bin2hex(random_bytes(16)));
    }

    private static function createDenyHtaccess($strPath)
    {
        if (is_file(self::$strRealPath . $strPath)) {
            return;
        }

        echo "placing deny htaccess in " . $strPath . "\n";
        $strContent = "\n\nRequire all denied\n\n";
        file_put_contents(self::$strRealPath . $strPath, $strContent);
    }

    private static function createAllowHtaccess($strPath)
    {
        if (is_file(self::$strRealPath . $strPath)) {
            return;
        }

        echo "placing allow htaccess in " . $strPath . "\n";
        $strContent = "\n\nRequire all granted\n\n";
        file_put_contents(self::$strRealPath . $strPath, $strContent);
    }

    private static function loadNpmDependencies()
    {
        echo "Installing node dependencies" . PHP_EOL;

        $arrOutput = array();
        exec("ant -f ".escapeshellarg(self::$strRealPath."/core/_buildfiles/build.xml")." installNpmBuildDependencies ", $arrOutput, $exitCode);
        if ($exitCode !== 0) {
            echo "Error exited with a non successful status code";
            exit(1);
        }
        echo "   " . implode("\n   ", $arrOutput);
    }


    private static function scanComposer()
    {
        if (is_file(__DIR__ . "/_buildfiles/bin/buildComposer.php")) {
            echo "Install composer dependencies" . PHP_EOL;
            $arrOutput = array();
            exec("php -f " . escapeshellarg(self::$strRealPath . "/core/_buildfiles/bin/buildComposer.php"), $arrOutput, $exitCode);
            if ($exitCode !== 0) {
                echo "Error exited with a non successful status code";
                exit(1);
            }
            echo "   " . implode("\n   ", $arrOutput);
        } else {
            echo "<span style='color: red;'>Missing buildComposer.php helper</span>";
        }
    }
}

echo "<pre>";

class_project_setup::setUp();

echo "</pre>";
