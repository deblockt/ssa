SSA
===

SSA is a framework that allows to simply perform Ajax call. You can call your service as PHP.

Installation
---

Il you want use SSA you have many solution.

### Use with symphony

An other project allow to simply add this project into your symphony project. see ssa/symphony.

### Use the standalone version

The standalone version is enable. 

#### Download with Composer

The first solution is to use a project with composer, you can just add ssa/core dependencies.

#### Download without composer

If you don't want use composer, you can add ssa into you project.

+ Downlaod the project
+ Add the project into your own project
+ Update your autoloader for autoload the ssa classes.
```
  function __autoload($className)  {
    if (strpos($className, 'ssa\') == 0) {
      $file = str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
      include $YOUR_INSTALATION_DIRECTORY.DIRECTORY_SEPARATOR.$file;
    }
  }
```
+ Install doctrine/annotations and doctrine/cache
+ You are ready to use SSA

#### Controller creation

For use the standalone version of SSA you need to use two php file, one to create javascript service, one to run the php controller.
this two php files, need to use configuration php file for register your services.

*configuration.php*
```
include 'autoload.php';

use ssa\ServiceManager;
use ssa\Configuration;

// configure the ssa framework
Configuration::getInstance()->configure(array(
    'debug' => true
));
// registrer your services
ServiceManager::getInstance()->registerAllServices(array(
    'HelloWorld' => array('class' => 'ssa\toEndTest\HelloWorld')
));

```
*run.php*
```
include 'configuration.php';

use ssa\runner\ServiceRunner;
// get the service and the action : HelloWorld.sayHello
list($service, $action) = explode('.', $_GET['service']);

// create the service runner
$serviceRunner = new ServiceRunner($service);
// run the action with get parameters
echo $serviceRunner->runAction($action, $_GET);
```

*javascript.php*
```
include 'configuration.php';

use ssa\converter\JavascriptConverter;
use ssa\converter\SimpleUrlFactory;

// url use to call php services
$url = substr($_SERVER['REQUEST_URI'],0, strrpos($_SERVER['REQUEST_URI'], '/'));
// url factory use for create the service run url (your run.php file)
$factory = new SimpleUrlFactory("http://$_SERVER[HTTP_HOST]$url/run.php?service={action}&test=true");
// create the converter convert the service into Javascript service, service use $_GET parameter
$converter = new JavascriptConverter($_GET['service'], $factory);

echo $converter->convert();
```

for example for get javascript HelloWorld service the url is _http://localhost/javascript.php?service=HelloWorld_

finaly you need include ssa.js into you javascript folder.



