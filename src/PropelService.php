<?php
namespace GKA\Noctis\Service\Propel;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use FDT2k\Noctis\Core\Env as Env;
class PropelService extends \FDT2k\Noctis\Core\Service\NoctisService
{
  function runAfterFrameworkPreInit(){
    $noctisConfig = Env::getConfig('database');

    //initialize propel  (replace config generator)
    $serviceContainer = \Propel\Runtime\Propel::getServiceContainer();
    $serviceContainer->checkVersion('2.0.0-dev');
    $serviceContainer->setAdapterClass($noctisConfig->get('database'), 'mysql');
    $manager = new \Propel\Runtime\Connection\ConnectionManagerSingle();
    $manager->setConfiguration(array (
      'classname' => 'Propel\\Runtime\\Connection\\DebugPDO',
      'dsn' => 'mysql:host='.$noctisConfig->get('host').';dbname='.$noctisConfig->get('database'),
      'user' => $noctisConfig->get('username'),
      'password' => $noctisConfig->get('password'),
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
    $manager->setName($noctisConfig->get('database'));
    $serviceContainer->setConnectionManager($noctisConfig->get('database'), $manager);
    $serviceContainer->setDefaultDatasource($noctisConfig->get('database'));


    // loading propel



    $defaultLogger = new Logger('defaultLogger');
    $defaultLogger->pushHandler(new StreamHandler(ICE_ROOT.'/var/log/propel.log', Logger::DEBUG));

    $serviceContainer->setLogger('defaultLogger', $defaultLogger);
  }
}
