<?php

declare(strict_types=1);

namespace Kajona\Benchmark\System\Bench;

use Kajona\Benchmark\System\AbstractBench;
use Kajona\System\System\Filesystem;

class FileystemBench extends AbstractBench
{

    const ITERATIONS = 2000;

    public function bench()
    {
        $this->createFilesAndFolders();
        $this->deleteFilesAndFolders();
    }


    private function createFilesAndFolders()
    {
        $fs = new Filesystem();
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $dir = generateSystemid();
            $fs->folderCreate("project/temp/bench/".$dir, true);
            file_put_contents(_realpath_."project/temp/bench/".$dir."/".$dir.".txt", $dir);
        }
    }

    private function deleteFilesAndFolders()
    {
        $fs = new Filesystem();
        foreach ($fs->getFilelist("project/temp/bench/", [".txt"]) as $file) {
            $fs->fileDelete("project/temp/bench/".$file);
        }

        $fs->folderDeleteRecursive("project/temp/bench/");

    }

}