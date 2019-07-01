<?php
/*"******************************************************************************************************
*   (c) 2010-2016 ARTEMEON                                                                              *
********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\System\System;

use Kajona\System\System\Exceptions\UnableToCreateIdFileException;
use Kajona\System\System\Exceptions\UnableToReadIdFileException;
use Kajona\System\System\Exceptions\UnableToWriteInIdFileException;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use RuntimeException;

/**
 * Model for a idgenerator record object itself
 *
 * @package module_agp_commons
 * @author christoph.kappestein@artemeon.de
 * @targetTable agp_idgenerator.id
 *
 * @module system
 * @moduleId _system_modul_id_
 */
class IdGenerator extends Model implements ModelInterface
{

    private const ID_LOCK_FILE_PATH = _realpath_ . 'project/temp/';

    /**
     * @var string
     * @tableColumn agp_idgenerator.generator_key
     * @tableColumnDatatype char20
     * @tableColumnIndex
     */
    private $strKey = '';

    /**
     * @var integer
     * @tableColumn agp_idgenerator.generator_count
     * @tableColumnDatatype int
     */
    private $intCount = 0;

    /**
     * Generates an id for an specific key. Creates a new entry if the key does not exist
     *
     * @param string $key
     * @return integer
     * @throws Exception
     * @throws Lifecycle\ServiceLifeCycleUpdateException
     */
    public static function generateNextId(string $key): int
    {
        $filepath = self::ID_LOCK_FILE_PATH . 'idFile-' . $key . '.txt';

        $ormObjectlist = new OrmObjectlist();
        $ormObjectlist->addWhereRestriction(new OrmPropertyCondition('strKey', OrmComparatorEnum::Equal(), $key));

        $result = $ormObjectlist->getObjectList(get_called_class());

        if (!$idFileExists = self::idFileExists($filepath)) {
            self::createIdFile($filepath);
        }

        $lastId = $idFileExists && is_int(self::getLastIdOfIdFile($filepath)) ? self::getLastIdOfIdFile($filepath) : 1;

        if (empty($result)) {
            $idGenerator = new IdGenerator();
            $id = $idFileExists ? $lastId + 1 : $lastId;
            $idGenerator->setStrKey($key);
        } else {
            $idGenerator = current($result);
            $id = $idFileExists ? $lastId + 1 : $idGenerator->getIntCount() + 1;
        }

        self::updateLastIdOfIdFile($filepath, $id);
        $idGenerator->setIntCount($id);
        ServiceLifeCycleFactory::getLifeCycle(get_class($idGenerator))->update($idGenerator);

        return $id;
    }


    /**
     * @param string $filepath
     * @return bool
     */
    private static function idFileExists(string $filepath): bool
    {
        return file_exists($filepath);
    }

    /**
     * @param string $filepath
     * @throws UnableToCreateIdFileException
     */
    private static function createIdFile(string $filepath): void
    {
         $file = fopen($filepath, 'w');
         if (!$file) {
             throw new UnableToCreateIdFileException('idFile could not be created');
         }
         fclose($file);
    }

    /**
     * @param string $filepath
     * @return int|null
     * @throws UnableToReadIdFileException
     */
    private static function getLastIdOfIdFile(string $filepath): ?int
    {
        try {
            $file = file($filepath);
            $line = end($file);
        } catch (RuntimeException $exception) {
            throw new UnableToReadIdFileException($exception->getMessage());
        }

        return !$line ? null : (int) $line;
    }

    /**
     * @param string $filepath
     * @param int $newId
     * @return bool
     * @throws UnableToWriteInIdFileException
     */
    private static function updateLastIdOfIdFile(string $filepath, int $newId): bool
    {
        $file = fopen($filepath, 'w+');
        if (!$file) {
            throw new UnableToWriteInIdFileException('unable to open idFile für writing');
        }
        fwrite($file, (string) $newId);
        fclose($file);

        return true;
    }

    /**
     * Returns the icon the be used in lists.
     * Please be aware, that only the filename should be returned, the wrapping by getImageAdmin() is
     * done afterwards.
     *
     * @return string the name of the icon, not yet wrapped by getImageAdmin(). Alternatively, you may return an array containing
     *         [the image name, the alt-title]
     */
    public function getStrIcon(): string
    {
        return 'icon_workflow';
    }

    /**
     * If not empty, the returned string is rendered below the common title.
     *
     * @return string
     */
    public function getStrLongDescription(): string
    {
        return $this->intCount . '';
    }

    /**
     * Returns the name to be used when rendering the current object, e.g. in admin-lists.
     *
     * @return string
     */
    public function getStrDisplayName(): string
    {
        return StringUtil::truncate($this->strKey, 150);
    }

    /**
     * @return string
     */
    public function getStrKey(): string
    {
        return $this->strKey;
    }

    /**
     * @param string $key
     */
    public function setStrKey(string $key): void
    {
        $this->strKey = $key;
    }

    /**
     * @return integer
     */
    public function getIntCount(): int
    {
        return $this->intCount;
    }

    /**
     * @param int $count
     */
    public function setIntCount(int $count): void
    {
        $this->intCount = $count;
    }

}
