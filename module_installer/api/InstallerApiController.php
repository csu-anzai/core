<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Installer\Api;

use Kajona\Api\System\ApiControllerInterface;

/**
 * InstallerApiController
 *
 * @author christoph.kappestein@gmail.com
 * @since 7.1
 */
class InstallerApiController implements ApiControllerInterface
{
    /**
     * @api
     * @method GET
     * @path /installer/systeminfo
     * @authorization filetoken
     */
    public function getSystemInfo()
    {
        return [
            "version" => $this->getPHPVersion(),
            "extensions" => $this->getPHPExtensions(),
            "folders" => $this->getFolders(),
        ];
    }

    private function getPHPVersion()
    {
        $requiredPHPVersion = "7.2";
        $actualPHPVersion = phpversion();

        return [
            "required" => $requiredPHPVersion,
            "actual" => $actualPHPVersion,
            "match" => version_compare($requiredPHPVersion, $actualPHPVersion, "<"),
        ];
    }

    private function getPHPExtensions()
    {
        $neededExtensions = array(
            "curl",
            "exif",
            "fileinfo",
            "gd",
            "iconv",
            "json",
            "ldap",
            "libxml",
            "mbstring",
            "openssl",
            "zend opcache",
            "pcre",
            "phar",
            "reflection",
            "session",
            "simplexml",
            "sockets",
            "spl",
            "xml",
            "xmlreader",
            "xmlwriter",
            "xsl",
            "zip"
        );

        $availableExtensions = array_map(function(string $val) {
            return strtolower($val);
        }, get_loaded_extensions());

        $result = [];
        foreach ($neededExtensions as $extension) {
            $result[$extension] = in_array($extension, $availableExtensions);
        }

        return $result;
    }

    private function getFolders()
    {
        $folders = array(
            "/project/module_system/system/config",
            "/project/dbdumps",
            "/project/log",
            "/project/temp",
            "/files/cache",
            "/files/images",
            "/files/downloads",
            "/files/temp",
        );

        $result = [];
        foreach ($folders as $file) {
            if (is_writable(_realpath_.$file)) {
                $result[$file] = true;
            } else {
                $result[$file] = false;
            }
        }

        return $result;
    }
}
