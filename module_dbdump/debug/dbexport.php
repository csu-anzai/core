<?php
declare(strict_types=1);

use Kajona\Dbdump\System\DbExport;
use Kajona\System\System\Carrier;

$objExporter = new DbExport(Carrier::getInstance()->getObjDB());
$objExporter->createExport();
