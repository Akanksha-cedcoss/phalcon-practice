<?php

// print_r(apache_get_modules());
// echo "<pre>"; print_r($_SERVER); die;
// $_SERVER["REQUEST_URI"] = str_replace("/phalt/","/",$_SERVER["REQUEST_URI"]);
// $_GET["_url"] = "/";
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Config\ConfigFactory;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Http\Response\Cookies;
use Phalcon\Escaper;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File;

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);

$loader->registerNamespaces(
    [
        'App\components' => APP_PATH . '/components',
    ]
);

$loader->register();

$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

$application = new Application($container);





$container->set(
    'config',
    function () {
        $file_name = '../app/etc/config.php';
        $factory  = new ConfigFactory();
        return $factory->newInstance('php', $file_name);
    }
);
$container->set(
    'db',
    function () {
        $db = $this->get('config')->db;
        return new Mysql(
            [
                'host'     => $db->host,
                'username' => $db->username,
                'password' => $db->password,
                'dbname'   => $db->dbname,
            ]
        );
    }
);

$container->set(
    'logger',
    function () {
        $adapter1 = new \Phalcon\Logger\Adapter\Stream(APP_PATH.'/storage/logs/signup.log');
        $adapter2 = new \Phalcon\Logger\Adapter\Stream(APP_PATH.'/storage/logs/login.log');

        return new Logger(
            'messages',
            [
                'signup'   => $adapter1,
                'login'  => $adapter2,
            ]
        );
    }
);

// $container->set(
//     'logger',
//     function () {
//         $adapter = new \Phalcon\Logger\Adapter\Stream(APP_PATH.'/storage/logs/signup.log');
//         return new Logger(
//             'messages',
//             [
//                 'main' => $adapter,
//             ]
//         );
//     }
// );
// $container->set(
//     'db',
//     function () {
//         return new Mysql(
//             [
//                 'host'     => 'mysql-server',
//                 'username' => 'root',
//                 'password' => 'secret',
//                 'dbname'   => 'blog',
//             ]
//         );
//     }
// );
// $container->set(
//     'mongo',
//     function () {
//         $mongo = new MongoClient();

//         return $mongo->selectDB('phalt');
//     },
//     true
// );

/**
 * my code start
 */

//Start the session the first time some component request the session service

$container->setShared('session', function () {
    $session = new Manager();
    $files = new Stream([
        'savePath' => '/tmp',
    ]);
    $session->setAdapter($files)->start();
    return $session;
});


$container->set(
    'cookies',
    function () {
        $cookies = new Cookies();

        $cookies->useEncryption(false);

        return $cookies;
    }
);
$container->set(
    'escaper',
    function () {
        return new Escaper();
    }
);
$container->set(
    'date',
    function () {
        // set default timezone
        date_default_timezone_set('Asia/Kolkata');

        return date('d/m/Y  ||  H:i:s');
    }
);
/**
 * my code end
 */
try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
