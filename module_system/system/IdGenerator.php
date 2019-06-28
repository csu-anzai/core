<?php
/*"******************************************************************************************************
*   (c) 2010-2016 ARTEMEON                                                                              *
********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\System\System;

use function fclose;
use function fgets;
use function file_exists;
use function fopen;
use function fseek;
use function fwrite;
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

    private const ID_LOCK_FILE = _realpath_ . '/project/temp/id-generator-lock.txt';

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
        $ormObjectlist = new OrmObjectlist();
        $ormObjectlist->addWhereRestriction(new OrmPropertyCondition('strKey', OrmComparatorEnum::Equal(), $key));

        $result = $ormObjectlist->getObjectList(get_called_class());

        if (!$idFileExists = self::idFileExists()) {
            self::createIdFile();
        }
        $lastId = $idFileExists ? self::getLastIdOfIdFile() : 1;

        if (empty($result)) {
            $id = $idFileExists ? $lastId + 1 : $lastId;
            $idGenerator = new IdGenerator();
            $idGenerator->setStrKey($key);
            $idGenerator->setIntCount($id);
            self::updateLastIdOfIdFile($id);
            ServiceLifeCycleFactory::getLifeCycle(get_class($idGenerator))->update($idGenerator);
        } else {
            /* @var IdGenerator $idGenerator */
            $idGenerator = current($result);
            $id = $idFileExists ? $lastId + 1 : $idGenerator->getIntCount() + 1;
            $idGenerator->setIntCount($id);
            self::updateLastIdOfIdFile($id);
            ServiceLifeCycleFactory::getLifeCycle(get_class($idGenerator))->update($idGenerator);
        }

        return $id;
    }


    /**
     * @return bool
     */
    private static function idFileExists(): bool
    {
        return file_exists(self::ID_LOCK_FILE);
    }

    /**
     * throws UnableToCreateIdFileException
     */
    private static function createIdFile(): void
    {
        try {
            $file = fopen(self::ID_LOCK_FILE, 'w');
            fclose($file);
        } catch (RuntimeException $exception) {
            throw new UnableToCreateIdFileException($exception->getMessage());
        }
    }

    /**
     * @return int
     * @throws UnableToReadIdFileException
     */
    private static function getLastIdOfIdFile(): int
    {
        try {
            $file = fopen(self::ID_LOCK_FILE, 'r');
            $pointer = -1;
            fseek($file, $pointer, SEEK_END);
            $line = fgets($file);
            while ($line !== "\n" || $line !== "\r") {
                fseek($file, $pointer--, SEEK_END);
                $line = fgets($file);
            }
            fclose($file);
        } catch (RuntimeException $exception) {
            throw new UnableToReadIdFileException($exception->getMessage());
        }

        return (int) $line;
    }

    /**
     * @param int $newId
     * @return bool
     * @throws UnableToWriteInIdFileException
     */
    private static function updateLastIdOfIdFile(int $newId): bool
    {
        try {
            $file = fopen(self::ID_LOCK_FILE, 'r');
            fwrite($file, $newId);
            fclose($file);
        } catch (RuntimeException $exception) {
            throw new UnableToWriteInIdFileException($exception->getMessage());
        }

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
