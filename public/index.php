<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require './vendor/autoload.php';

require './src/chemin.php';

$app = new \Slim\App;

$app->get('/produits/{id}', function(Request $request, Response $response, array $args){
    var_dump('le parametre est : '.$args['id']);
});

$app->get('/test', function(Request $request, Response $response, array $param) use ($conn){
    
    $stmt = $conn->query('SELECT * FROM products');
    $rs=$stmt->fetchAll();
    echo(json_encode($rs));
});

$app->post('/products', function(Request $request, Response $response, array $params) use ($conn){
    // $name = $request->getParam('name');
    // $description = $request->getParam('description');
    // $price = $request->getParam('price');

    $body = $request->getBody();
    $data = json_decode($body, true);
    
    $name = $data['name'];
    $description = $data['description'];
    $price = $data['price'];

    $stmt=$conn->prepare("INSERT INTO products(name,description,price) VALUES (:name,:description,:price)");
    $stmt->execute([
    'name'=>$name,
    'description'=>$description,
    'price'=>$price
]);
echo ('Insertion reussie');
});

$app->put('/products/{id}', function(Request $request, Response $response, array $params) use ($conn)
{
        // $name = $request->getParam('name');
        // $description = $request->getParam('description');
        // $price = $request->getParam('price');

        $body = $request->getBody();
        $data = json_decode($body, true);

        $name = $data['name'];
        $description = $data['description'];
        $price = $data['price'];
        $id = $params['id'];

        $stmt=$conn->prepare("UPDATE products SET name=:name, description=:description, price=:price WHERE id=:id");
        $stmt->execute([
            'name'=>$name,
            'description'=>$description,
            'price'=>$price,
            'id'=>$id
    ]);
echo ('Modification reussie');
});

$app->delete('/products/{id}', function(Request $request, Response $response, array $params){
    echo('Suppresion reussi');
});

$app->get('/personne', function(Request $request, Response $response, array $param) use ($conn){
    
    $stmt = $conn->query('SELECT * FROM personnes');
    $rs=$stmt->fetchAll();
    echo(json_encode($rs));
});

$mw = function($request, $response, $next){
    $body = $request->getBody();
    $data = json_decode($body, true);

    if ($data['age'] <= 0) {
        $response->getBody()->write('Age not correct');
    }else{
        $response = $next($request, $response);
    }

    return $response;
};


$app->post('/personne', function(Request $request, Response $response, array $params) use ($conn){
    $body = $request->getBody();
    $data = json_decode($body, true);
    
    $nom = $data['nom'];
    $sexe = $data['sexe'];
    $age = $data['age'];

    $stmt=$conn->prepare("INSERT INTO personnes(nom,sexe,age) VALUES (:nom,:sexe,:age)");
    $stmt->execute([
    'nom'=>$nom,
    'sexe'=>$sexe,
    'age'=>$age
]);
echo ('Insertion reussie');
})->add($mw);




$app->run();