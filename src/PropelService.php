<?php
namespace GKA\Noctis\Service\Propel;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use FDT2k\Noctis\Core\Env as Env;
class PropelService extends \FDT2k\Noctis\Core\Service\NoctisService
{

  function getServiceContainer(){
    //initialize propel  (replace config generator)
    $serviceContainer = \Propel\Runtime\Propel::getServiceContainer();
    $serviceContainer->checkVersion('2.0.0-dev');
    return $serviceContainer;
  }
  function createManager($config){

    $serviceContainer = $this->getServiceContainer();
    $serviceContainer->setAdapterClass($config->get('connection_name'), 'mysql');
    $manager = new \Propel\Runtime\Connection\ConnectionManagerSingle();
    if($config->get('propel_debug')){
      $classname=  "DebugPDO";
    }else{
      $classname= "PropelPDO";
    }

    $manager->setConfiguration(array (
      'classname' => 'Propel\\Runtime\\Connection\\'.$classname,
      'dsn' => 'mysql:host='.$config->get('host').';dbname='.$config->get('database'),
      'user' => $config->get('username'),
      'password' => $config->get('password'),
      'attributes' =>
      array (
        'ATTR_EMULATE_PREPARES' => false,
        'ATTR_TIMEOUT' => 30,
      ),
      'settings' =>
      array (
        'charset' => 'utf8',
        'queries' =>
        array (
          'utf8' => 'SET NAMES utf8 COLLATE utf8_unicode_ci, COLLATION_CONNECTION = utf8_unicode_ci, COLLATION_DATABASE = utf8_unicode_ci, COLLATION_SERVER = utf8_unicode_ci',
        ),
      ),
      'model_paths' =>
      array (
        0 => 'src',
        1 => 'vendor',
      ),
    ));
    $manager->setName($config->get('connection_name'));
    $serviceContainer->setConnectionManager($config->get('connection_name'), $manager);
    $serviceContainer->setDefaultDatasource($config->get('connection_name'));
  }

  function initLogger(){
    // set up logging service
    $serviceContainer = $this->getServiceContainer();
    $defaultLogger = new Logger('defaultLogger');
    $defaultLogger->pushHandler(new StreamHandler(ICE_ROOT.'/var/log/propel.log', Logger::DEBUG));

    $serviceContainer->setLogger('defaultLogger', $defaultLogger);
  }

  function runAfterFrameworkPreInit(){
    $noctisConfig = Env::getConfig('database');
    $cnx = $noctisConfig->get('connections');
    foreach($cnx as $c){


      $this->createManager(Env::getConfig($c));
    }
    if($noctisConfig->get('propel_logger')){
      $this->initLogger();
    }
  }
}
