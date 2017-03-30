<?php
if(!defined(NOCTIS_NO_SERVICE_AUTOLOAD)){
  \FDT2k\Noctis\Core\Service\ServiceManager::registerService(new \GKA\Noctis\Service\Propel\PropelService());
}
