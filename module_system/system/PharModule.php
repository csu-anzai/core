<?php
/*"******************************************************************************************************
*   (c) 2007-2015 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\System\System;

/**
 * Class to load and install modules packaged as Phar archives.
 *
 * @package module_system
 * @author ph.wolfer@gmail.com
 * @since 4.8
 */
class PharModule
{
    private $strPharPath;
    private $objPhar;

    /**
     * @param string Path to the Phar file relative to Kajona root.
     */
    public function __construct($strPharPath)
    {
        $this->strPharPath = $strPharPath;
        $this->objPhar = new \Phar(_realpath_."/".$this->strPharPath, 0);
    }

    /**
     * Load and intialize a Phar module.
     *
     * @param string[] List of folders supposed to contain code.
     * @return string[] A list of class names and corresponding absolute file paths.
     */
    public function load($arrCodeFolders)
    {
        $arrCodeFiles = [];
        foreach (new \RecursiveIteratorIterator($this->objPhar) as $objFile) {
            // Make sure the file is a PHP file and is inside the requested folder
            $strArchivePath = $this->getRelativeFilePath($objFile);

            foreach ($arrCodeFolders as $strFolder) {
              $strFolder = str_replace("\\", "/", $strFolder);

              if (substr($strArchivePath, -4) === ".php"
                && substr($strArchivePath, 0, strlen($strFolder)) === $strFolder) {
                  $strFilename = substr($objFile->getFileName(), 0, -4);
                  if (!isset($arrCodeFiles[$strFilename])) {
                      $arrCodeFiles[$strFilename] = $objFile->getPathName();
                  }
              }
            }

            // Include the module ID
            if (preg_match("/module\_([a-z0-9\_])+\_id\.php/", $objFile->getFileName())) {
                include_once $objFile->getPathName();
            }
        }

        return $arrCodeFiles;
    }

    // public function install($strPath)
    // {
    //
    // }

    /**
     * Check whether a given path is a Phar file.
     *
     * @param string Path to Phar file.
     * @return bool
     */
    public static function isPhar($strPath)
    {
        return uniStrpos($strPath, ".phar") !== false;
    }

    /**
     * Returns a Phar file's name withou extension.
     *
     * @param string Path to Phar file.
     * @return string
     */
    public static function getPharBasename($strPath)
    {
        $intExtensionPos = uniStrpos($strPath, ".phar");
        if ($intExtensionPos !== false) {
            return substr($strPath, 0, $intExtensionPos);
        }

        return false;
    }

    /**
     * Returns a file's path relative to the Phar archive.
     *
     * @param PharFileInfo A phar file
     * @return string
     */
    private function getRelativeFilePath($objFile)
    {
        $strArchivePath = DIRECTORY_SEPARATOR.substr($objFile->getPathName(),
            strlen("phar://"._realpath_."/".$this->strPharPath));
        $strArchivePath = str_replace("\\", "/", $strArchivePath);
        return $strArchivePath;
    }
}
