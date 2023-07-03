<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

$app = new \Slim\App;

// GET Todos los clientes
$app->get('/api/admin/getuser/{id}', function (Request $request, Response $response) {

    $id_cliente = $request->getAttribute('id');
    $sql = "SELECT a.name, a.code, a.firstname, a.lastname, u.email, a.birthdate  FROM admin a
    left join adminuser u on (a.id = u.idadmin)
    where a.id = $id_cliente limit 1";

    try {
        $db = new db();
        $db = $db->conectDB();
        $resultado = $db->query($sql);

        if ($resultado->rowCount() > 0) {
            $clientes = $resultado->fetchAll(PDO::FETCH_OBJ);
            echo json_encode($clientes);
        } else {
            $clientes = $resultado->fetchAll(PDO::FETCH_OBJ);
            echo json_encode($clientes);
            // echo json_encode("No existen clientes en la BBDD.");
        }
        $resultado = null;
        $db = null;
    } catch (PDOException $e) {
        echo '{"error" : {"text":' . $e->getMessage() . '}';
    }
});
//
// GET LISTADO de PRODUCTO
$app->get('/api/lista+producto_admin/{id}', function (Request $request, Response $response) {
    //
    $id_cliente = $request->getAttribute('id');
    $sql = "SELECT a.id, a.name as title, a.code, a.description, a.price, a.date, a.amount, im.code as img FROM product a
    left join imag_product2 im on (a.id = im.idproduct)
    where a.idadmin = $id_cliente 
    AND a.active = 'Y'
    AND a.deleted = 'N'
    AND im.code is not null
    order by a.date desc";

    try {
        $db = new db();
        $db = $db->conectDB();
        $resultado = $db->query($sql);

        if ($resultado->rowCount() > 0) {
            $cliente = $resultado->fetchAll(PDO::FETCH_OBJ);
            echo json_encode($cliente);
        } else {
            echo json_encode("No existen cliente en la BBDD con este ID.");
        }
        $resultado = null;
        $db = null;
    } catch (PDOException $e) {
        echo '{"error" : {"text":' . $e->getMessage() . '}';
    }
});
//
// POST Crear nuevo producto
$app->post('/api/add_product/{id}', function (Request $request, Response $response) {
    //
    //
    $id_cliente = $request->getAttribute('id'); // cliente cifrado
    $com = " SELECT id FROM admin where id = $id_cliente limit 1";
    //
    $name = $request->getParam('name');
    $image = $request->getParam('code'); /// codificacion de la imagen
    $price = $request->getParam('price');
    $date = date("Y-m-d");
    $description = $request->getParam('description');
    $amount = $request->getParam('amount');
    $code = $image[0]["data_url"];
    // 
    $sql = ' INSERT INTO product (idadmin, description, name, date, price, amount) VALUES
            (:idadmin, :description, :name, :date, :price, :amount ) ';
    //
    if ($name == "" || $price == "" || $code == "" || $amount == "") {
        //
        echo json_encode(505);
        return;
    }
    //
    $sql2 = ' INSERT INTO imag_product2 (code,	idproduct,	date ) VALUES
     (:code, :idproduct, :date ) ';
    //
    try {
        $db = new db();
        $db = $db->conectDB();
        $comid = $db->query($com);
        $resultado = $db->prepare($sql);
        //
        if ($comid->rowCount() > 0) {
            //
            $id = $comid->fetchAll(PDO::FETCH_OBJ);
            $idadmin = $id[0]->id;
            //
            $resultado->bindParam(':idadmin', $idadmin);
            $resultado->bindParam(':description', $description);
            $resultado->bindParam(':name', $name);
            $resultado->bindParam(':date', $date);
            $resultado->bindParam(':price', $price);
            $resultado->bindParam(':amount', $amount);
            $resultado->execute();
            //
            $com = " SELECT id FROM product where idadmin = $idadmin order by id desc limit 1";
            $comid = $db->query($com);
            $idprod = $comid->fetchAll(PDO::FETCH_OBJ);
            $idprod = $idprod[0]->id;
            try {
                $res = $db->prepare($sql2);
                $res->bindParam(':idproduct', $idprod);
                $res->bindParam(':code', $code);
                $res->bindParam(':date', $date);
                $res->execute();
                echo json_encode($code);
            } catch (PDOException $e) {
                echo '{"error" : {"text":' . $e->getMessage() . '}';
                // echo json_encode(400);
            }
            //echo json_encode($idprod);
        } else {
            echo json_encode(404);
        }
        //
        $comid = null;
        $db = null;
        $resultado = null;
        $res = null;
        $db = null;
        //
    } catch (PDOException $e) {
        //
        echo json_encode(400);
        //
    }
});

# API EMAIL VALID
$app->post('/api/login', function (Request $request, Response $response) {
    //
    $email = $request->getParam('email');
    $pass = $request->getParam('password');
    //
    //
    $sql = " SELECT pass2 FROM adminuser WHERE email = '" . $email . "'";

    try {
        $db = new db();
        $db = $db->conectDB();
        $resultado = $db->query($sql);

        if ($resultado->rowCount() > 0) {

            $cliente = $resultado->fetchAll(PDO::FETCH_OBJ);
            echo json_encode($cliente);
        } else {
            $cliente = $resultado->fetchAll(PDO::FETCH_OBJ);
            echo json_encode(404);
        }
        $resultado = null;
        $db = null;
        $resultado = null;
        $db = null;
    } catch (PDOException $e) {
        echo '{"error" : {"text":' . $e->getMessage() . '}';
    }
});

# API PASS VALID
$app->post('/api/login/passvalid', function (Request $request, Response $response) {

    //
    $email = $request->getParam('email');
    $pass = $request->getParam('pass');
    //
    //
    $sql = " SELECT idadmin , email  FROM adminuser WHERE email = '" . $email . "' AND pass = '" . $pass . "'";

    try {
        $db = new db();
        $db = $db->conectDB();
        $resultado = $db->query($sql);

        if ($resultado->rowCount() > 0) {

            $cliente = $resultado->fetchAll(PDO::FETCH_OBJ);
            echo json_encode($cliente);
        } else {
            $cliente = $resultado->fetchAll(PDO::FETCH_OBJ);
            echo json_encode(404);
        }
        $resultado = null;
        $db = null;
        $resultado = null;
        $db = null;
    } catch (PDOException $e) {
        echo '{"error" : {"text":' . $e->getMessage() . '}';
    }
});
/*$app->post('/api/login', function (Request $request, Response $response) {

    //
    $email = $request->getParam('email');
    $pass = $request->getParam('password');
    //
    //

    $sql = " SELECT pass FROM adminuser WHERE email = '" . $email . "'";

    /// $sql = " SELECT * FROM adminuser WHERE email = '" . $email . "' AND pass = '" . $pass . "'";

    try {
        $db = new db();
        $db = $db->conectDB();
        $resultado = $db->query($sql);

        if ($resultado->rowCount() > 0) {
            $cliente = $resultado->fetchAll(PDO::FETCH_OBJ);
            echo json_encode($cliente);
        } else {
            $cliente = $resultado->fetchAll(PDO::FETCH_OBJ);
            echo json_encode($cliente);
            // echo json_encode("No existen cliente en la BBDD con este ID.");
        }
        $resultado = null;
        $db = null;
        $resultado = null;
        $db = null;
    } catch (PDOException $e) {
        echo '{"error" : {"text":' . $e->getMessage() . '}';
    }
});
*/

// PUT Modificar cliente
$app->post('/api/admin/update/{id}', function (Request $request, Response $response) {


    $id_cliente = $request->getAttribute('id');
    $firstname = $request->getParam('firstname');
    $lastname = $request->getParam('lastname');
    $birthdate = $request->getParam('birthdate');

    $birthdate = date("Y-m-d", strtotime($birthdate));

    $sql = "UPDATE admin SET
          firstname = :firstname,
          lastname = :lastname,
          birthdate = :birthdate
        WHERE id = $id_cliente";

    $arr = array(
        $id_cliente,
        $firstname,
        $lastname,
        $birthdate,
    );


    try {
        $db = new db();
        $db = $db->conectDB();
        $resultado = $db->prepare($sql);

        $resultado->bindParam(':firstname', $firstname);
        $resultado->bindParam(':lastname', $lastname);
        $resultado->bindParam(':birthdate', $birthdate);

        $resultado->execute();

        echo json_encode($arr);

        $resultado = null;
        $db = null;
    } catch (PDOException $e) {
        echo '{"error" : {"text":' . $e->getMessage() . '}';
    }
});

// PUT Modificar cliente
$app->post('/api/admin/update/email/{id}', function (Request $request, Response $response) {

    $id_cliente = $request->getAttribute('id');
    $code = $request->getParam('code');
    $name = $request->getParam('name');
    $email = $request->getParam('email');
    $pass = $request->getParam('pass');
    $pass2 = $request->getParam('pass2');


    $sql = "UPDATE adminuser U,  admin A SET
          U.code = :code,
          A.code = :code,
          U.name = :name,
          A.name = :name,
          U.email = :email,
          U.pass = :pass,
          U.pass2 = :pass2
        WHERE U.idadmin = $id_cliente and A.id = $id_cliente";

    try {
        $db = new db();
        $db = $db->conectDB();
        $resultado = $db->prepare($sql);

        $resultado->bindParam(':code', $code);
        $resultado->bindParam(':name', $name);
        $resultado->bindParam(':email', $email);
        $resultado->bindParam(':pass', $pass);
        $resultado->bindParam(':pass2', $pass2);

        $resultado->execute();

        echo json_encode("Cliente modificado.");

        $resultado = null;
        $db = null;
    } catch (PDOException $e) {
        echo json_encode($sql);
    }
});








// PUT Modificar cliente
$app->put('/api/clientes/modificar/{id}', function (Request $request, Response $response) {
    $id_cliente = $request->getAttribute('id');
    $title = $request->getParam('title');
    $description = $request->getParam('description');
    $img = $request->getParam('img');
    $leftColor = $request->getParam('leftColor');
    $rightColor = $request->getParam('rightColor');
    $ciudad = $request->getParam('ciudad');

    $sql = "UPDATE clientes SET
          title = :title,
          description = :description,
          img = :img,
          leftColor = :leftColor,
          rightColor = :rightColor,
          ciudad = :ciudad
        WHERE id = $id_cliente";

    try {
        $db = new db();
        $db = $db->conectDB();
        $resultado = $db->prepare($sql);

        $resultado->bindParam(':title', $title);
        $resultado->bindParam(':description', $description);
        $resultado->bindParam(':img', $img);
        $resultado->bindParam(':leftColor', $leftColor);
        $resultado->bindParam(':rightColor', $rightColor);
        $resultado->bindParam(':ciudad', $ciudad);

        $resultado->execute();
        echo json_encode("Cliente modificado.");

        $resultado = null;
        $db = null;
    } catch (PDOException $e) {
        echo '{"error" : {"text":' . $e->getMessage() . '}';
    }
});

// DELETE borrar cliente
$app->delete('/api/clientes/delete/{id}', function (Request $request, Response $response) {
    $id_cliente = $request->getAttribute('id');
    $sql = "DELETE FROM clientes WHERE id = $id_cliente";

    try {
        $db = new db();
        $db = $db->conectDB();
        $resultado = $db->prepare($sql);
        $resultado->execute();

        if ($resultado->rowCount() > 0) {
            echo json_encode("Cliente eliminado.");
        } else {
            echo json_encode("No existe cliente con este ID.");
        }

        $resultado = null;
        $db = null;
    } catch (PDOException $e) {
        echo '{"error" : {"text":' . $e->getMessage() . '}';
    }
});
