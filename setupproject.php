<?php

/*"******************************************************************************************************
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

class class_project_setup
{
    private const NPM_REGISTRY = 'http://packages.artemeon.int:4873/';

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


        $arrExcludedModules = array();
        if (is_file(self::$strRealPath . "project/packageconfig.php")) {
            include self::$strRealPath . "project/packageconfig.php";
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
                if (preg_match("/^(module|element|_)+.*/i", $strOneModule) && !in_array($strOneModule, $arrExcludedModules[$strRootFolder])) {
                    $arrModules[] = $strRootFolder . "/" . $strOneModule;
                }
            }
        }

        self::checkDir("/bin");
        self::createBinReadme();
        self::checkDir("/project");
        self::checkDir("/project/log");
        self::makeWritable("/project/log");
        self::checkDir("/project/dbdumps");
        self::makeWritable("/project/dbdumps");
        self::checkDir("/project/module_system");
        self::checkDir("/project/module_system/system");
        self::checkDir("/project/module_system/system/config");
        self::makeWritable("/project/module_system/system/config");
        self::checkDir("/project/temp");
        self::makeWritable("/project/temp");
        self::checkDir("/files");
        self::checkDir("/files/cache");
        self::makeWritable("/files/cache");
        self::checkDir("/files/downloads");
        self::checkDir("/files/downloads/default");
        self::makeWritable("/files/downloads/default");
        self::checkDir("/files/images");
        self::makeWritable("/files/images");
        self::checkDir("/files/extract");
        self::makeWritable("/files/extract");

        echo "searching for files on root/project-path...\n";
        foreach ($arrModules as $strSingleModule) {
            if (!is_dir(self::$strRealPath . "/" . $strSingleModule)) {
                continue;
            }

            $arrContent = scandir(self::$strRealPath . "/" . $strSingleModule);
            foreach ($arrContent as $strSingleEntry) {
                if (substr($strSingleEntry, -5) == ".root" && !is_file(self::$strRealPath . "/" . substr($strSingleEntry, 0, -5))) {
                    //echo "copy ".$strSingleEntry." to ".self::$strRealPath."/".substr($strSingleEntry, 0, -5)."\n";
                    copy(self::$strRealPath . "/" . $strSingleModule . "/" . $strSingleEntry, self::$strRealPath . "/" . substr($strSingleEntry, 0, -5));
                }

                if (substr($strSingleEntry, -8) == ".project" && !is_file(self::$strRealPath . "/project/" . substr($strSingleEntry, 0, -8))) {
                    //echo "copy ".$strSingleEntry." to ".self::$strRealPath."/".substr($strSingleEntry, 0, -5)."\n";
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
        self::creaeRootGitIgnore();
        self::loadNpmDependencies();
        self::scanComposer();
        self::buildSkinStyles();
        self::buildJavascript();

        echo "\n<b>Done.</b>\nIf everything went well, <a href=\"../installer.php\">open the installer</a>\n";
    }

    private static function creaeRootGitIgnore()
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
TEXT;
        file_put_contents(self::$strRealPath . "/.gitignore", $content);

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

    private static function checkDir($strFolder)
    {
        echo "checking dir " . self::$strRealPath . $strFolder . "\n";
        if (!is_dir(self::$strRealPath . $strFolder)) {
            mkdir(self::$strRealPath . $strFolder, 0777);
            echo " \t\t... directory created\n";
        } else {
            echo " \t\t... already existing.\n";
        }
    }

    private static function makeWritable($strFolder)
    {
        chmod(self::$strRealPath . $strFolder, 0777);
    }

    private static function copyFolder($strSourceFolder, $strTargetFolder, $arrExcludeSuffix = array())
    {
        $arrEntries = scandir($strSourceFolder);
        foreach ($arrEntries as $strOneEntry) {
            if ($strOneEntry == "." || $strOneEntry == ".." || $strOneEntry == ".svn" || in_array(substr($strOneEntry, strrpos($strOneEntry, ".")), $arrExcludeSuffix)) {
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

    private static function executeCommand(string $commandName, array $parameters): string
    {
        $command = $commandName . ' ' . implode(' ', array_map('escapeshellarg', $parameters));

        $output = [];
        \exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            throw new \RuntimeException('command exited with a non-zero status');
        }

        return \implode('', \array_map(function(string $outputLine) {
            return '   ' . $outputLine . "\n";
        }, $output));
    }

    private static function loadNpmDependencies(): void
    {
        echo 'Installing node dependencies' . \PHP_EOL;

        //only if required
        if (\is_dir(self::$strRealPath . '/core/_buildfiles/jstests/node_modules/clean-css') && \is_dir(self::$strRealPath . '/core/_buildfiles/jstests/node_modules/less') && \is_dir(self::$strRealPath . '/core/_buildfiles/jstests/node_modules/typescript/')) {
            echo '  not required' . \PHP_EOL;

            return;
        }

        try {
            \chdir(__DIR__ . '/_buildfiles/jstests');
            self::executeCommand('npm', ['config', 'set', 'registry', self::NPM_REGISTRY]);
            $output = self::executeCommand('npm', ['install']);
            \chdir(__DIR__);
        } catch (\RuntimeException $exception) {
            echo 'Error exited with a non successful status code';
            exit(1);
        }

        echo $output;
    }

    private static function buildSkinStyles(): void
    {
        if (!\is_file(__DIR__ . '/_buildfiles/bin/buildSkinStyles.php')) {
            echo '<span style=\'color: red;\'>Missing buildSkinStyles.php helper</span>';

            return;
        }

        echo 'Building skin css styles' . \PHP_EOL;

        try {
            $output = self::executeCommand('php', ['-f', self::$strRealPath . '/core/_buildfiles/bin/buildSkinStyles.php']);
        } catch (\RuntimeException $exception) {
            echo 'Error exited with a non successful status code';
            exit(1);
        }

        echo $output;
    }

    private static function buildJavascript(): void
    {
        if (!\is_file(__DIR__ . '/_buildfiles/bin/buildJavascript.php')) {
            echo '<span style=\'color: red;\'>Missing buildJavascript.php helper</span>';

            return;
        }

        echo 'Compress and merge js files' . \PHP_EOL;

        try {
            $output = self::executeCommand('php', ['-f', self::$strRealPath . '/core/_buildfiles/bin/buildJavascript.php']);
        } catch (\RuntimeException $exception) {
            echo 'Error exited with a non successful status code';
            exit(1);
        }

        echo $output;
    }

    private static function scanComposer(): void
    {
        if (!\is_file(__DIR__ . "/_buildfiles/bin/buildComposer.php")) {
            echo '<span style=\'color: red;\'>Missing buildComposer.php helper</span>';

            return;
        }

        echo 'Install composer dependencies' . \PHP_EOL;

        try {
            $output = self::executeCommand('php', ['-f', self::$strRealPath . '/core/_buildfiles/bin/buildComposer.php']);
        } catch (\RuntimeException $exception) {
            echo 'Error exited with a non successful status code';
            exit(1);
        }

        echo $output;
    }
}

echo "<pre>";

class_project_setup::setUp();

echo "</pre>";
