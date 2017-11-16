<?php
namespace GKA\Noctis\Service\Propel;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use FDT2k\Noctis\Core\Env as Env;
class PropelService extends \FDT2k\Noctis\Core\Service\NoctisService
{
  const SERVICE_NAME = "PropelService";

  function getServiceContainer(){
    //initialize propel  (replace config generator)
    $serviceContainer = \Propel\Runtime\Propel::getServiceContainer();
    $serviceContainer->checkVersion('2.0.0-dev');
    return $serviceContainer;
  }

  function createManager($c){
    $this->createPropelManager($c->get('connection_name'),$c->get('host'),$c->get('database'),$c->get('username'),$c->get('password'),$c->get('propel_debug'));
  }

  function createPropelManager($connection_name,$host,$database,$username,$password,$debug=false){
    $serviceContainer = $this->getServiceContainer();
    $serviceContainer->setAdapterClass($connection_name, 'mysql');
    $manager = new \Propel\Runtime\Connection\ConnectionManagerSingle();
    if($debug){
      $classname= "DebugPDO";
    }else{
      $classname= "PropelPDO";
    }

    $manager->setConfiguration(array (
      'classname' => 'Propel\\Runtime\\Connection\\'.$classname,
      'dsn' => 'mysql:host='.$host.';dbname='.$database,
      'user' => $username,
      'password' => $password,
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
    $manager->setName($connection_name);
    $serviceContainer->setConnectionManager($connection_name, $manager);
    $serviceContainer->setDefaultDatasource($connection_name);
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
