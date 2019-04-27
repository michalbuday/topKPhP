<?php
// move with router
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Configuration as Config;
use \Slim\App as Slim;

// load vendor autoload in config?

require '../../vendor/autoload.php';

$config = new Config();

// create file for sharing app instance in classes?

$app = new Slim(['settings' => $config->getAppConfig('test')]);

//separate DI container to different file

$container = $app->getContainer();

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler('../logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
};

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$container['view'] = new \Slim\Views\PhpRenderer('../templates/');

// separate controllers for views to separate files, router to separate file

$app->get('/', function (Request $request, Response $response) {

    // separate db functions to db class
    function getNamesFromDb($db) {
        $sql = $db->prepare("SELECT * FROM `names`");
        $names = [];
        if($sql->execute()) {
            while ($row = $sql->fetch()) {
                $names[] = $row['name'];
            }
            return $names; 
        } 
        return [];
    }


    $names = getNamesFromDb($this->db);
    $response = $this->view->render($response, 'homepage.phtml', ['names' => $names]);

    return $response;
});

$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];

    function createdbtable($db)
    {
        $sql = "CREATE TABLE IF NOT EXISTS `names` (
                `name_ID` INT NOT NULL,
                `name` varchar(200) NOT NULL)
                CHARACTER SET utf8 COLLATE utf8_general_ci";
        
        if($db->exec($sql) !== false) { return 1; } 
    }

    function addNameToDb($db, $name) {
        $sanitizedName = filter_var($name, FILTER_SANITIZE_STRING);

        $sql = $db->prepare("INSERT INTO names (name) VALUES (:name)");

        $sql->bindParam(':name', $sanitizedName);

        if($sql->execute() !== false) { return 1; }
    }

    $result = createdbtable($this->db);
    $result = addNameToDb($this->db, $name);

    if ($result !== 1) {$name = 'fail';}

    $response = $this->view->render($response, 'name.phtml', ['name' => $name]);

    return $response;
});

// set app running after all setup

$app->run();
