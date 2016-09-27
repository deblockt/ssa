SSA : Simple Service Access
===

SSA is a framework to simply perform Ajax call. You can call your service as PHP into you javascript.

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

For run the service, @param annotations are used, the type is use for convert $_POST parameters into PHP type.
Type support can be primitive, complete class name (with namespace), \DateTime(inputFormat, file or array.
\DateTime and array are specific.
\DateTime type have parameter input format. Example \DateTime(m/d/Y)
array can have parameter. Example array(int) array(int) array(\Path\To\My\Class) array(file) ...


Javascript service have multiple method for handle ajax event.
* *fail* : If a network error occurs
* *always* : Already run after ajax call
* *phpError* : Run if a php error occurs. (if the ssa mode is debug, the php error are logged)
* *done* : Run when the service call is a success. 

ssa.js has two default handler :
* *defaultFailHandler* : default handler used if no specific handler are specified. It can be overrided by fail handler.
* *defaultPhpErrorHandler* : default handler used if no specific handler are specified. It can be overrided by phpError handler.
* *addStartCallListener* : Listener call before each ajax call
* *addEndCallListener* : Listener call after each ajax call

_serviceTest.js_
```javascript
myService.myAction('firstParameter', {attr1 : 'value1'})
         .done(function(returnValue, xhr) { alert(returnValue);})
         .fail(function(xhr) {alter('a network error occurs');})
         .phpError(function(errorPhp, xhr) {alert('an error occurs'.errorPhp.message);})
         .always(function(xhr){console.log('fin de la requête')}); 
```
each callback have the same object context, you can pass variable between this callbacks.

### Support

Ssa support multiple type parameter, and return value.
Parameters and return value can be, primitive type, object or DateTime, array, you can simply add an other type support.

Ssa support file uploaded, if you want upload a file you must use the file, or array(file) types.

*service.php*
```php
class FileService {

    /**
     * @param file $file
     */
    public function upload($file) {
        // file is like this
        // array(
        //  'name' => 'string',
        //  'size' => int,
        //  'error' => int,
        //  'tmp_name' => 'string',
        //  'type' => 'string'
        // )
    }
    
    /**
     * @param array(file) $files
     */
    public function uploadMultiple($files) {
        foreach ($files as $file) {
            $this->upload($file);
        }
    }
}
```

*FileUpload.js*
```javascript
// upload one file
FileService.upload(document.getElementById('simpleFileUploadInput').files);
// upload multiple file
FileService.uploadMultipleFile(document.getElementById('multipleFileUploadInput').files);
```

Warning the file uploaded is not support by all navigator, it use FormData class.
The method ssa.supportFileUpload return true if the navigator support this function.
When you run a service with file upload the callback formDataError is call is this function is not supported.
```javascript
FileService.upload(document.getElementById('simpleFileUploadInput').files)
           .formDataError(function(){
                alert('Your navigator is too old for this function');
           });
```


Ssa support multiple javascript framework :
  - you can use ssa standolone only on include ssa.js file
  - you can use ssa with angular js you just need to include ssa.js file and your service generated file. After use injection dependencies for get your service.
    for exemple : 
```javascript
  // add ssa as module dependencies
  var controller = angular.module('ssa.test', ['ssa']);
  // get simply your service on your controller with the service name. here the service is helloWorldService
  controller.controller('controller', function($scope, helloWorldService){   
  });
```
  - you can use ssa with requirejs you just need to include ssa on your configuration of requirejs.
```javascript
// configuration must containe a ssa link
require.config({
  paths: {
      // you must add ssa srcipt on your configuration
      "ssa": "path/to/ssa/javascript/file",
      // warning if you don't use htaccess service param is like this /serviceName
      // if you use htacess you can have url like this /javascript/ssa/service/servicename.js who redirect on javascript.php
      // path of your javscript service generator
      "ssaService" : "javascript.php?type=requirejs&service=" 
  }
});

// juste require your service
require( ["ssaService/helloWorldService"],
  function(helloWorldService) {
    // helloWorldService is you php service
  }
);
```

If you want see ssa exemple you can look on test/ssa/toEndTest directory

### Configuration 

The ssa configuration is different between standalone version, and the symfony version.

#### Symfony version

For symfony version you can look this [bundle project](https://github.com/deblockt/ssaSymfony)

#### Standalone

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
#### Create an encoder

The encoder is use for encode your function return into javascipt value.
The default encoder is the JsonEncoder, this encoder can convert primitive type, array, and object. Objects are convert with getter methods, each getter is convert into a JSON property.
If you need you can create your own encoder, you can do this, it's simple. On your service action you need to add @Encoder annotation.
*Service.php*
```php
class Service {
  /**
   * @Encoder(\MyEncoder)
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
The action method use MyEncoder for convert the return on JSON or other format.
Your encoder must implements ssa\runner\converter\Encoder, or extends ssa\runner\converter\DefaultJsonEncoder.


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
echo $serviceRunner->runAction($action, array_merge($_POST, $_FILES));
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

More usages
--

## Add Own Javascript on generated service
 You can add your own javascript on the generated services.
 If you wand inject your own code, you need use ssa\converter\annotations\AddJavascript annotation. It have own parameter : the js file to add, the path is a relative path.
 
 *AuthenticateService.php*
 ``` php
 use ssa\converter\annotations\AddJavascript;
/**
 * @AddJavascript("../../javascript/ssaSecureModule.js")
 * @author thomas
 */	
class AuthenticateService {
}
 ```
 
 If you want add methods on generated service, you can create function named "module", this function have one parameter : the generated service. this function is call when the service is generating.
 
## Add serviceRunner handler
 
 You can add handler for serviceRunner, the handler called before and after the service runned. The handler is an Annotation, if the annotation is on the function comment, handlers are called.
 
 You need to create an annotation like this : 
 
 *Secure.php*
 ``` php
use ssa\runner\annotations\RunnerHandler;
use Doctrine\Common\Annotations\Annotation;


/**
 *
 * @Annotation
 * 
 * @author thomas
 */
class Secure implements RunnerHandler {
    /**
     * set state magic method for cache method
     * @param type $array
     * @return \ssa\secure\annotations\Secure
     */
    public static function __set_state($array) {
        $secure = new Secure();
        return $secure;
    }
	
	/**
	 * call before service call
	 *
	 * @param string $method the action name
	 * @param array $inputParameters service parameter, (service => the service call, service.method)
	 * @param ServiceMetadata $metaData
	 *
	 * @throw Exception if action must no be call
	 */
	public function before($method,array &$inputParameters,ServiceMetadata $metaData) {
	
	}
	
    /**
	 * call before service call
	 *
	 * @param string $method the action name
	 * @param array $inputParameters service parameter, (service => the service call, service.method)
	 * @param mixed the service result before encoding
	 * @param ServiceMetadata $metaData
	 *
	 * can return value tranformed $result, encoder is call after this method
	 */
	public function after($method,array &$inputParameters, $result, ServiceMetadata $metaData) {
		
	}

}

 ```
 
 And you can call your handler like this : 
 
 *service.php*
 ``` php
    /**
	 * @Secure
	 *
     * @param string $yourName
     * @return string 
     */
    public function helloYou($userId) {
        return 'hello ' .$userId.'!!!';
    }
 ```
 
