<?php

/** @var \Kajona\Api\System\EndpointScanner $scanner */
$scanner = \Kajona\System\System\Carrier::getInstance()->getContainer()->offsetGet(\Kajona\Api\System\ServiceProvider::STR_ENDPOINT_SCANNER);
$endpoints = $scanner->getEndpoints();

foreach ($endpoints as $endpoint) {
    echo sprintf("%s %s => %s@%s\n", implode("|", $endpoint["httpMethod"]), $endpoint["path"], $endpoint["class"], $endpoint["methodName"]);
}

