<?php

namespace Kajona\Mediamanager\Tests;

use Kajona\Mediamanager\Admin\MediamanagerAdmin;
use Kajona\Mediamanager\System\MediamanagerFile;
use Kajona\Mediamanager\System\MediamanagerRepo;
use Kajona\System\System\Filesystem;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\Rights;
use Kajona\System\System\SystemModule;
use Kajona\System\Tests\Testbase;

class MediamanagerTest extends Testbase
{


    /**
     * @throws \Kajona\System\System\Exception
     * @throws \Kajona\System\System\Lifecycle\ServiceLifeCycleDeleteException
     * @throws \Kajona\System\System\Lifecycle\ServiceLifeCycleUpdateException
     */
    public function testFileSync()
    {

        $objFilesystem = new Filesystem();
        $objFilesystem->folderCreate(_filespath_ . "/images/samples");
        $objFilesystem->folderCreate(_filespath_ . "/images/autotest");

        if (!is_file(_realpath_ . "files/images/samples/IMG_3000.JPG")) {
            file_put_contents(_realpath_ . "files/images/samples/IMG_3000.JPG", "dummy");
        }


        $objFilesystem->fileCopy(_filespath_ . "/images/samples/IMG_3000.JPG", _filespath_ . "/images/autotest/IMG_3000.jpg");
        $objFilesystem->fileCopy(_filespath_ . "/images/samples/IMG_3000.JPG", _filespath_ . "/images/autotest/IMG_3000.png");
        $objFilesystem->fileCopy(_filespath_ . "/images/samples/IMG_3000.JPG", _filespath_ . "/images/autotest/PA021805.JPG");
        $objFilesystem->fileCopy(_filespath_ . "/images/samples/IMG_3000.JPG", _filespath_ . "/images/autotest/test.txt");

        $this->assertFileExists(_realpath_._filespath_ . "/images/autotest/IMG_3000.jpg");
        $this->assertFileExists(_realpath_._filespath_ . "/images/autotest/IMG_3000.png");
        $this->assertFileExists(_realpath_._filespath_ . "/images/autotest/PA021805.JPG");
        $this->assertFileExists(_realpath_._filespath_ . "/images/autotest/test.txt");

        $objRepo = new MediamanagerRepo();
        $objRepo->setStrPath(_filespath_ . "/images/autotest");
        $objRepo->setStrTitle("autotest repo");
        $objRepo->setStrViewFilter(".jpg,.png");
        ServiceLifeCycleFactory::getLifeCycle(get_class($objRepo))->update($objRepo);
        $objRepo->syncRepo();

        $arrFiles = MediamanagerFile::loadFilesDB($objRepo->getSystemid());

        $this->assertEquals(3, count($arrFiles));
        foreach ($arrFiles as $objOneFile) {
            ServiceLifeCycleFactory::getLifeCycle(get_class($objOneFile))->deleteObjectFromDatabase($objOneFile);
        }

        ServiceLifeCycleFactory::getLifeCycle(get_class($objRepo))->deleteObjectFromDatabase($objRepo);

        $arrFiles = $objFilesystem->getFilelist(_filespath_ . "/images/autotest");

        $this->assertEquals(1, count($arrFiles));
        $this->assertEquals("test.txt", array_values($arrFiles)[0]);

    }

    public function testDocumentVersioningPropertiesSaving()
    {
        Rights::getInstance()->setBitTestMode(true);

        $filesystem = new Filesystem();
        $filesystem->folderCreate(_filespath_ . "/images/autotest");

        if (!is_file(_realpath_ . "files/images/samples/test.txt")) {
            file_put_contents(_realpath_ . "files/images/samples/test.txt", "dummy");
        }

        $filesystem->fileCopy(_filespath_ . "/images/samples/IMG_3000.JPG", _filespath_ . "/images/autotest/test01/test.txt");

        $testRepo = new MediamanagerRepo();
        $testRepo->setStrPath(_filespath_ . "/images/autotest");
        $testRepo->setStrTitle("files repo");
        ServiceLifeCycleFactory::getLifeCycle(get_class($testRepo))->update($testRepo);
        $testRepo->syncRepo();

        $mark = "5";
        $subtitle = "test subtitle";
        $description = "test description";

        $tmpFile = MediamanagerFile::getFileForPath($testRepo->getSystemid(), $testRepo->getStrPath() . "/test01/test.txt");
        $tmpFile->setIntMark($mark);
        $tmpFile->setStrSubtitle($subtitle);
        $tmpFile->setStrDescription($description);
        ServiceLifeCycleFactory::getLifeCycle(get_class($tmpFile))->update($tmpFile);

        /** @var MediamanagerAdmin $mediaManager */
        $mediaManager = SystemModule::getModuleByName("mediamanager")->getAdminInstanceOfConcreteModule($testRepo->getStrSystemid());
        $mediaManager->setParam("folder", "test01");

        $method = new \ReflectionMethod(MediamanagerAdmin::class, "actionDocumentVersioning");
        $method->setAccessible(true);
        $method->invoke($mediaManager);

        $folderList = MediamanagerFile::loadFilesDB($testRepo->getSystemid(), MediamanagerFile::$INT_TYPE_FOLDER);
        foreach ($folderList as $innerFolder) {
            $innerFolderList = MediamanagerFile::loadFilesDB($innerFolder->getSystemid(), MediamanagerFile::$INT_TYPE_FOLDER);
            foreach ($innerFolderList as $folder) {
                $files = MediamanagerFile::loadFilesDB($folder->getSystemid(), MediamanagerFile::$INT_TYPE_FILE);
                foreach ($files as $file) {
                    $fileName = $file->getStrName();
                    $fileMark = $file->getIntMark();
                    $fileSubtitle = $file->getStrSubtitle();
                    $fileDescription = $file->getStrDescription();
                    $file->deleteObjectFromDatabase();
                }
            }

        }

        ServiceLifeCycleFactory::getLifeCycle(get_class($tmpFile))->deleteObjectFromDatabase($tmpFile);
        ServiceLifeCycleFactory::getLifeCycle(get_class($testRepo))->deleteObjectFromDatabase($testRepo);

        $filesystem->folderDeleteRecursive($testRepo->getStrPath() . "/test01");

        $this->assertEquals("test.txt", $fileName);
        $this->assertEquals($mark, $fileMark);
        $this->assertEquals($subtitle, $fileSubtitle);
        $this->assertEquals($description, $fileDescription);

    }
}

