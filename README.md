SSA : Simple Service Access
===

SSA is a framework that allows to simply perform Ajax call. You can call your service as PHP.

Usage
---

### Example

The usage of ssa is very simple, you create your PHP service, and you can call this service in javascript code.
For example : 
*HelloWorld.php*
```php
namespace services\;

/**
 * simple example service
 *
 * @author deblock
 */
class HelloWorld {
    
    /**
     * return Hello <yourName> !!
     * @param string $yourName
     * @return string 
     */
    public function helloYou($yourName) {
        return 'Hello ' . $yourName.' !!';
    }
}
```

*example.html*
```html
<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <!-- include ssa core javascript -->
        <script type="text/javascript" src="javascript/ssa.js" ></script>
        <!-- include the autogenerate javascript service -->
        <script type="text/javascript" src="javascript.php?service=HelloWorld"></script>
        <script type="text/javascript">
            HelloWorld.helloYou('deblockt').done(function(result){
                document.getElementById('serviceResult').innerHTML = result;
            });
        </script>
    </head>
    <body>
        <div> SSA exemple </div>
        <div id="serviceResult"></div>
    </body>
</html>
```
This exemple add "Hello deblock !!" into the serviceResult div.

### Working

For convert the service into Javascript service ssa use doc comment. It's use the @param annotation for know parameters and type parameters. If a php parameter have no comment, they will not be export into javascrit service.

For run the service, the @param annotation are already use, the type is use for convert $_GET parameter into PHP type.
Type support can be primitive, complete class name (with namespace), \DateTime(inputFormat) or array.
\DateTime and array are specific.
\DateTime type have parameter input format. Example \DateTime(m/d/Y)
array can have parameter. Example array(int) array(int) array(\Path\To\My\Class) and parameters are converted.

Javascript service have multiple method for handle ajax event.
* *fail* : If a network error occurs
* *always* : Already run after ajax call
* *phpError* : Run if a php error occurs. (if the ssa mode is debug, the php error are logged)
* *done* : Run when the service call is a success. 

_serviceTest.js_
```javascript
myService.myAction('firstParameter', {attr1 : 'value1'})
         .done(function(returnValue, xhr) { alert(returnValue);})
         .fail(function(xhr) {alter('a network error occurs');})
         .phpError(function(errorPhp, xhr) {alter('an error occurs'.errorPhp.message);})
         .always(function(xhr){console.log('fin de la requête')}); 
```
each callback have the same object context, you can pass variable between this callbacks.

### Support

Ssa support multiple type parameter, and return value.
Parameters and return value can be, primitive type, object or DateTime, you can simply add an other type support.

### Configuration 

The ssa configuration is different between standalone version, and the symfony version.
For symfony version you can look the repo ssa/symfony.
The documentation of the Standalone version is under.

#### Register you service

The simple way for register your service is to create a *configuration.php* files. this file call the serviceManager for register your own services.

*configuration.php*
```php
include 'autoload.php';

use ssa\ServiceManager;

// the first way to do this is with registerAllServices method 
ServiceManager::getInstance()->registerAllServices(array(
    // the first service is the class ssa\test\Service1 and we expose all this method 
    'service1' => array(
      'class' => 'ssa\test\Service1'
    ),
    // the second service is the class ssa\test\Service2 and we expose only the action1 and action2 method 
    'service2' => array(
      'class' => 'ssa\test\Service2',
      'methods' => array('action1','action2')
    )
));

// the second way to do this is the registerService function, you can only register one service
ServiceManager::getInstance()->registerService('service3','ssa\test\Service3');
// or
ServiceManager::getInstance()->registerService('service4','ssa\test\Service4', array('action1'));
  
```

#### Configure SSA

SSA can be configured, the configuration can be in the *configuration.php* file.

*configuration.php*
```php
include 'autoload.php';

use ssa\Configuration;

Configuration::getInstance()->configure(array(
    'debug' => true, // if debug is true the generated javascript file are not minimized
    'cacheMode' => 'file', // if the cache is configured this cache mode is use, this can be file,apc,memcache,no(default)
    'cacheDirectory' => '', // if the cacheMode is file, this parameter is mandatory, this is the directory where the cache is put
    'memcacheHost' => '', // if the cacheMode is memcache is set this parameter is mandatory
    'memcachePort' => ''// if the cacheMode is memcache is set this parameter is mandatory
));

// the configuration can be do like this, example
Configuration::getInstance()->setDebug(true);
Configuration::getInstance()->setCacheMode('file');

/** service register **/
```

#### Add type support

If default type support of ssa is not suffisant, you can define your own type support.
You have two way to do this, add simple a type support (for object, or for parameter), or create a new ParameterResolver who resolve primitive and object.
A default ParameterResolver exists, it can resolve primitiveType, array, object, and \DataTime. 

The first method to add a type resolver is add directly into the default type resolver.
_configuration.php_
```php
/** register services, and configure ssa */

use ssa\runner\resolver\impl\DefaultParameterResolver;
$defaultParameter = DefaultParameterResolver::createDefaultParameterResolver();
$defaultParameter->addObjectResolver(new MyObjectResolver());
$defaultParameter->addPrimitiveResolver(new MyPrimitiveResolver());

```
Your own ObjectResolver and your ParameterResolver need impements ssa\runner\resolver\ObjectResolver, ssa\runner\resolver\PrimitiveResolver. see documentation of PrimitiveResolver and ObjectResolver.

Or you can create your own ParameterResolver (not recommended). your ParameterResolver need implement ssa\runner\resolver\ParameterResolver.

*run.php*
```php
include 'configuration.php';

use ssa\runner\ServiceRunner;
// get the service and the action : HelloWorld.sayHello
list($service, $action) = explode('.', $_GET['service']);

// create the service runner
$serviceRunner = new ServiceRunner($service, new MyParameterResolver());
// run the action with get parameters
echo $serviceRunner->runAction($action, $_GET);
```
#### Create a converter

The converter is use for convert your function return into javascipt value.
The default converter is the JsonConverter, this converter can convert primitive type, array, and object. Objects are convert with getter methods, each getter is convert into a JSON property.
If you need you can create your own converter, you can do this, it's simple. On your service action you need to add @Converter annotation.
*Service.php*
```php
class Service {
  /**
   * @Converter(\MyJsonConverter)
   *
   * @param string $firstParameter
   *
   * @return string
   */
  public function action($firstParameter) {
    return $firstParameter;
  }
}
```
The action method use MyJsonConverter for convert the return on JSON.
You serializer must implements JsonSerializable, or extends DefaultJsonSerializer.


Installation
---

Il you want use SSA you have many solution.

### Use with symfony

An other project allow to simply add this project into your symfony project. see ssa/symfony.

### Use the standalone version

The standalone version is enable. 

#### Download with Composer

The first solution is to use a project with composer, you can just add ssa/core dependencies.

#### Download without composer

If you don't want use composer, you can add ssa into you project.

+ Downlaod the project
+ Add the project into your own project
+ Update your autoloader for autoload the ssa classes.
```php
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
```php
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
```php
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
```php
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



