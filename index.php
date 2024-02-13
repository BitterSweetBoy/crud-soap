<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD SOAP</title>
    <!-- Agregamos los estilos de Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>

<body>

    <div class="container mt-5">
        <?php
        // Consumir el servicio SOAP para obtener la lista de usuarios
        $location = "http://localhost/crud-soap/services/UsuarioSOAP.php?wsdl";

        $request = "
    <soapenv:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:usu=\"UsuarioSoap\">
       <soapenv:Header/>
       <soapenv:Body>
          <usu:UsuarioService.getAllUsuarios soapenv:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\">
          </usu:UsuarioService.getAllUsuarios>
       </soapenv:Body>
    </soapenv:Envelope>
    ";

        $action = "UsuarioService.getAllUsuarios";
        $headers = [
            'Method: POST',
            'Connection: Keep-Alive',
            'User-Agent: PHP-SOAP-CURL',
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: ' . $action,
        ];

        $ch = curl_init($location);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        $response = curl_exec($ch);
        $err_status = curl_errno($ch);

        // Extraer los resultados de la respuesta SOAP
        preg_match('/<Resultados xsi:type="xsd:string">(.*)<\/Resultados>/', $response, $matches);
        $resultados = json_decode(html_entity_decode($matches[1]), true);

        // Mostrar la tabla de usuarios con Bootstrap
        echo '<h2>Lista de Usuarios</h2>';
        echo '<table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>';
        foreach ($resultados as $usuario) {
            echo '<tr>
                <td>' . $usuario['id'] . '</td>
                <td>' . $usuario['username'] . '</td>
                <td>' . $usuario['email'] . '</td>
                <td>' . $usuario['pass'] . '</td>
                <td>
                    <a href="?update=' . $usuario['id'] . '" class="btn btn-warning btn-sm">Actualizar</a>
                    <a href="?delete=' . $usuario['id'] . '" class="btn btn-danger btn-sm">Eliminar</a>
                </td>
            </tr>';
        }
        echo '</tbody>
          </table>';

        // Verificar si se ha enviado una solicitud de actualización
        if (isset($_GET['update'])) {
            $userIdToUpdate = $_GET['update'];

            // Obtener los datos del usuario para prellenar el formulario de actualización
            // Consumir el servicio SOAP para obtener los detalles del usuario por ID
            $getUsuarioRequest = "
                <soapenv:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:usu=\"UsuarioSoap\">
                    <soapenv:Header/>
                    <soapenv:Body>
                        <usu:UsuarioService.getUsuarioById soapenv:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\">
                            <id xsi:type=\"xsd:int\">$userIdToUpdate</id>
                        </usu:UsuarioService.getUsuarioById>
                    </soapenv:Body>
                </soapenv:Envelope>";

            $getUsuarioAction = "UsuarioService.getUsuarioById";
            $getUsuarioHeaders = [
                'Method: POST',
                'Connection: Keep-Alive',
                'User-Agent: PHP-SOAP-CURL',
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: ' . $getUsuarioAction,
            ];

            $getUsuarioCh = curl_init($location);
            curl_setopt($getUsuarioCh, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($getUsuarioCh, CURLOPT_HTTPHEADER, $getUsuarioHeaders);
            curl_setopt($getUsuarioCh, CURLOPT_POST, true);
            curl_setopt($getUsuarioCh, CURLOPT_POSTFIELDS, $getUsuarioRequest);
            curl_setopt($getUsuarioCh, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

            $getUsuarioResponse = curl_exec($getUsuarioCh);
            $getUsuarioErrStatus = curl_errno($getUsuarioCh);

            // Verificar si la obtención de datos del usuario fue exitosa
            if ($getUsuarioErrStatus === 0) {
                // Convertir la respuesta SOAP en un arreglo asociativo
                preg_match('/<Resultados xsi:type="xsd:string">(.*)<\/Resultados>/', $getUsuarioResponse, $matches);
                $usuarioDetails = json_decode(html_entity_decode($matches[1]), true);

                // Verificar si la decodificación fue exitosa
                if ($usuarioDetails === null) {
                    // Manejar el error o imprimir un mensaje de depuración
                    echo '<div class="alert alert-danger" role="alert">Error al obtener detalles del usuario. JSON inválido.</div>';
                } else {
                    // Mostrar el formulario de actualización con los datos prellenados
                    echo '<h2>Actualizar Usuario</h2>';
                    echo '<form action="#" method="post" class="form-inline"> 
                    <input type="hidden" name="updateId" value="' . $usuarioDetails['id'] . '">
                    
                    <div class="form-group mb-2">
                        <label for="updateUsername" class="sr-only">Username</label>
                        <input type="text" class="form-control" id="updateUsername" name="updateUsername" placeholder="Username" value="' . $usuarioDetails['username'] . '" required>
                    </div>
                    <div class="form-group mx-sm-3 mb-2">
                        <label for="updateEmail" class="sr-only">Email</label>
                        <input type="text" class="form-control" id="updateEmail" name="updateEmail" placeholder="Email" value="' . $usuarioDetails['email'] . '" required>
                    </div>
                    <div class="form-group mx-sm-3 mb-2">
                        <label for="updatePass" class="sr-only">Password</label>
                        <input type="text" class="form-control" id="updatePass" name="updatePass" placeholder="Password" value="' . $usuarioDetails['pass'] . '" required>
                    </div>
                    <button type="submit" name="updateUser" class="btn btn-primary mb-2">Actualizar Usuario</button>
                  </form>';
                }
            } else {
                echo '<div class="alert alert-danger" role="alert">Error al obtener detalles del usuario. Por favor, inténtalo de nuevo.</div>';
            }

            curl_close($getUsuarioCh);
        }

        // Procesamiento del formulario para actualizar usuarios
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["updateUser"])) {
            $updateId = $_POST["updateId"];
            $updateUsername = $_POST["updateUsername"];
            $updateEmail = $_POST["updateEmail"];
            $updatePassword = $_POST["updatePass"];

            // Consumir el servicio SOAP para actualizar el usuario
            $updateRequest = "
            <soapenv:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:usu=\"UsuarioSoap\">
                <soapenv:Header/>
                <soapenv:Body>
                    <usu:UsuarioService.updateUsuario soapenv:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\">
                        <id xsi:type=\"xsd:int\">$updateId</id>
                        <username xsi:type=\"xsd:string\">$updateUsername</username>
                        <email xsi:type=\"xsd:string\">$updateEmail</email>
                        <pass xsi:type=\"xsd:string\">$updatePassword</pass>
                    </usu:UsuarioService.updateUsuario>
                </soapenv:Body>
            </soapenv:Envelope>";

            $updateAction = "UsuarioService.updateUsuario";
            $updateHeaders = [
                'Method: POST',
                'Connection: Keep-Alive',
                'User-Agent: PHP-SOAP-CURL',
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: ' . $updateAction,
            ];

            $updateCh = curl_init($location);
            curl_setopt($updateCh, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($updateCh, CURLOPT_HTTPHEADER, $updateHeaders);
            curl_setopt($updateCh, CURLOPT_POST, true);
            curl_setopt($updateCh, CURLOPT_POSTFIELDS, $updateRequest);
            curl_setopt($updateCh, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

            $updateResponse = curl_exec($updateCh);
            $updateErrStatus = curl_errno($updateCh);

            // Verificar si la actualización fue exitosa y mostrar un mensaje
            if ($updateErrStatus === 0) {
                echo '<div class="alert alert-success" role="alert">Usuario agregado exitosamente.</div>';
                echo '<script>
                        alert("Usuario agregado exitosamente. Redirigiendo a la página principal.");
                        window.location.href = "index.php";
                      </script>';
            } else {
                echo '<div class="alert alert-danger" role="alert">Error al agregar el usuario. Por favor, inténtalo de nuevo.</div>';
            }

            curl_close($updateCh);
        }



        // Procesamiento del formulario para agregar usuarios
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST["updateUser"])) {
            $newUsername = $_POST["username"];
            $newEmail = $_POST["email"];
            $newPassword = $_POST["pass"];

            // Consumir el servicio SOAP para insertar el nuevo usuario
            $insertRequest = "
                <soapenv:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:usu=\"UsuarioSoap\">
                    <soapenv:Header/>
                    <soapenv:Body>
                        <usu:UsuarioService.insertUsuario soapenv:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\">
                            <username xsi:type=\"xsd:string\">$newUsername</username>
                            <email xsi:type=\"xsd:string\">$newEmail</email>
                            <pass xsi:type=\"xsd:string\">$newPassword</pass>
                        </usu:UsuarioService.insertUsuario>
                    </soapenv:Body>
                </soapenv:Envelope>";

            $insertAction = "UsuarioService.insertUsuario";
            $insertHeaders = [
                'Method: POST',
                'Connection: Keep-Alive',
                'User-Agent: PHP-SOAP-CURL',
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: ' . $insertAction,
            ];

            $insertCh = curl_init($location);
            curl_setopt($insertCh, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($insertCh, CURLOPT_HTTPHEADER, $insertHeaders);
            curl_setopt($insertCh, CURLOPT_POST, true);
            curl_setopt($insertCh, CURLOPT_POSTFIELDS, $insertRequest);
            curl_setopt($insertCh, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

            $insertResponse = curl_exec($insertCh);
            $insertErrStatus = curl_errno($insertCh);

            // Verificar si la inserción fue exitosa y actualizar la lista de usuarios
            if ($insertErrStatus === 0) {
                echo '<div class="alert alert-success" role="alert">Usuario agregado exitosamente.</div>';
                // Actualizar la lista de usuarios después de la inserción
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                echo '<div class="alert alert-danger" role="alert">Error al agregar el usuario. Por favor, inténtalo de nuevo.</div>';
            }

            curl_close($insertCh);
        }

        // Verificar si se ha enviado una solicitud de eliminación
        if (isset($_GET['delete'])) {
            $userIdToDelete = $_GET['delete'];

            // Consumir el servicio SOAP para eliminar el usuario
            $deleteRequest = "
                <soapenv:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:usu=\"UsuarioSoap\">
                    <soapenv:Header/>
                    <soapenv:Body>
                        <usu:UsuarioService.deleteUsuario soapenv:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\">
                            <id xsi:type=\"xsd:int\">$userIdToDelete</id>
                        </usu:UsuarioService.deleteUsuario>
                    </soapenv:Body>
                </soapenv:Envelope>";

            $deleteAction = "UsuarioService.deleteUsuario";
            $deleteHeaders = [
                'Method: POST',
                'Connection: Keep-Alive',
                'User-Agent: PHP-SOAP-CURL',
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: ' . $deleteAction,
            ];

            $deleteCh = curl_init($location);
            curl_setopt($deleteCh, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($deleteCh, CURLOPT_HTTPHEADER, $deleteHeaders);
            curl_setopt($deleteCh, CURLOPT_POST, true);
            curl_setopt($deleteCh, CURLOPT_POSTFIELDS, $deleteRequest);
            curl_setopt($deleteCh, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

            $deleteResponse = curl_exec($deleteCh);
            $deleteErrStatus = curl_errno($deleteCh);

            // Verificar si la eliminación fue exitosa y actualizar la lista de usuarios
            if ($deleteErrStatus === 0) {
                // Redirigir al usuario a la misma página después de la eliminación
                echo '<script>
                        alert("Usuario eliminado exitosamente.");
                      </script>';
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                echo '<div class="alert alert-danger" role="alert">Error al eliminar el usuario. Por favor, inténtalo de nuevo.</div>';
            }

            curl_close($deleteCh);
        }


        // Formulario para agregar usuarios con Bootstrap
        if (!isset($_GET['update'])) {
            echo '<h2>Agregar Nuevo Usuario</h2>';
            echo '<form action="#" method="post" class="form-inline">
        <div class="form-group mb-2">
            <label for="username" class="sr-only">Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
        </div>
        <div class="form-group mx-sm-3 mb-2">
            <label for="email" class="sr-only">Email</label>
            <input type="text" class="form-control" id="email" name="email" placeholder="Email" required>
        </div>
        <div class="form-group mx-sm-3 mb-2">
            <label for="pass" class="sr-only">Password</label>
            <input type="text" class="form-control" id="pass" name="pass" placeholder="Password" required>
        </div>
        <button type="submit" class="btn btn-primary mb-2">Agregar Usuario</button>
      </form>';
        }
        ?>
    </div>

    <!-- Agregamos los scripts de Bootstrap y jQuery -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.9/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

</body>

</html>