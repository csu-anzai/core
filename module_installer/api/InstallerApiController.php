<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Installer\Api;

use Kajona\Api\System\ApiControllerInterface;
use Kajona\System\System\Database;
use Kajona\System\System\DbConnectionParams;

/**
 * InstallerApiController
 *
 * @author christoph.kappestein@gmail.com
 * @since 7.1
 */
class InstallerApiController implements ApiControllerInterface
{
    /**
     * @inject system_db
     * @var Database
     */
    protected $connection;

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

    /**
     * @api
     * @method GET
     * @path /installer/connection
     * @authorization filetoken
     */
    public function getConnection()
    {
        $extensions = [
            "mysqli",
            "pgsql",
            "sqlsrv",
            "sqlite3",
            "oci8",
        ];

        $result = [];
        foreach ($extensions as $extension) {
            $result[$extension] = in_array($extension, get_loaded_extensions());
        }

        return $result;
    }

    /**
     * @api
     * @method POST
     * @path /installer/connection
     * @authorization filetoken
     */
    public function validateConnection($body)
    {
        return [
            "check" => $this->checkConnection(
                $body["driver"] ?? null,
                $body["hostname"] ?? null,
                $body["username"] ?? null,
                $body["password"] ?? null,
                $body["dbname"] ?? null,
                $body["port"] ?? null
            ),
        ];
    }

    /**
     * @api
     * @method POST
     * @path /installer/config
     * @authorization filetoken
     */
    public function writeConfig($body)
    {
        $available = $this->checkConnection(
            $body["driver"] ?? null,
            $body["hostname"] ?? null,
            $body["username"] ?? null,
            $body["password"] ?? null,
            $body["dbname"] ?? null,
            $body["port"] ?? null
        );

        if ($available) {
            $content = "<?php\n";
            $content.= "/*\n Kajona V7 config-file.\n If you want to overwrite additional settings, copy them from /core/module_system/system/config/config.php into this file.\n*/";
            $content.= "\n\n\n";
            $content.= "  \$config['dbhost']               = '".$body["hostname"]."';                   //Server name \n";
            $content.= "  \$config['dbusername']           = '".$body["username"]."';                   //Username \n";
            $content.= "  \$config['dbpassword']           = '".$body["password"]."';                   //Password \n";
            $content.= "  \$config['dbname']               = '".$body["dbname"]."';                     //Database name \n";
            $content.= "  \$config['dbdriver']             = '".$body["driver"]."';                     //DB-Driver \n";
            $content.= "  \$config['dbport']               = '".$body["port"]."';                       //Database port \n";
            $content.= "\n";

            $configFile = _realpath_."project/module_system/system/config/config.php";
            $result = file_put_contents($configFile, $content);
        } else {
            $result = false;
        }

        return [
            "written" => $result,
        ];
    }

    private function checkConnection($driver, $hostname, $username, $password, $dbname, $port)
    {
        return $this->connection->validateDbCxData(
            $driver,
            new DbConnectionParams($hostname, $username, $password, $dbname, $port)
        );
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
