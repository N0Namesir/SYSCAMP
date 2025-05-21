<?php
include_once('header.php');
include_once('configuracion/conexion.php')
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertar Usuario</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin=""/>
    
    <link rel="stylesheet" href="estilos.css">
    
    <style>
        body {
            padding-top: 80px !important; /* Valor ajustado para evitar superposición */
        }
        
        .navbar-custom {
            position: fixed !important;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 1030 !important;
        }
        
        .container {
            margin-top: 20px;
        }
        
        h2, h1 {
            padding-top: 15px;
        }
        
        /* Add margin-top to the form card for spacing */
        .card.p-4 {
            margin-top: 20px; 
        }
         /* Style specific to the form card */
        .card.shadow-sm {
            box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
        }
    </style>
</head>
<body class="bg-light">


<?php
$mensaje = ""; // Initialize message variable

// Procesar envío del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input (basic example, enhance for production)
    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $nombres = htmlspecialchars(trim($_POST['nombres']));
    $apellidos = htmlspecialchars(trim($_POST['apellidos']));
    // IMPORTANT: Do NOT store plain text passwords. Use password_hash()
    // This example uses a plain text field named password_hash, which is confusing and insecure.
    // You should hash the password before storing it.
    // Example: $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $password_input = $_POST['password_hash']; // Using the provided field name, but strongly recommend hashing
    $hashed_password = password_hash($password_input, PASSWORD_DEFAULT); // Example of how to hash

    // Validate rol_id is a number and exists in your roles table
    $rol_id = filter_var($_POST['rol_id'], FILTER_VALIDATE_INT);

    if ($rol_id === false || $rol_id <= 0) { // Simple validation
         $mensaje = "❌ Error: Rol seleccionado inválido.";
    } else {
        // Insertar en la base de datos
        // Using prepared statements is strongly recommended to prevent SQL injection
        // This is an example using pg_query with escaped strings for simplicity, but it's less secure.
        $username_escaped = pg_escape_string($conexion, $username);
        $email_escaped = pg_escape_string($conexion, $email);
        $nombres_escaped = pg_escape_string($conexion, $nombres);
        $apellidos_escaped = pg_escape_string($conexion, $apellidos);
        // Use the hashed password here:
        $password_hash_escaped = pg_escape_string($conexion, $hashed_password); // Escaping the HASHED password

        $query = "INSERT INTO public.usuarios (username, email, nombres, apellidos, password_hash, rol_id) 
                  VALUES ('$username_escaped', '$email_escaped', '$nombres_escaped', '$apellidos_escaped', '$password_hash_escaped', $rol_id)"; // rol_id is int, no quotes needed

        $resultado = pg_query($conexion, $query);

        if ($resultado) {
            $mensaje = "✅ Usuario insertado correctamente.";
        } else {
            $mensaje = "❌ Error al insertar usuario: " . pg_last_error($conexion);
        }
    }
}

?>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Formulario para Insertar Usuario</h2>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-info text-center">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <form method="post" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label for="username" class="form-label">Nombre de usuario</label>
                <input type="text" class="form-control" id="username" name="username" required>
                 <div class="invalid-feedback">
                    Por favor, ingrese el nombre de usuario.
                </div>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required>
                 <div class="invalid-feedback">
                    Por favor, ingrese un correo electrónico válido.
                </div>
            </div>

            <div class="mb-3">
                <label for="nombres" class="form-label">Nombres</label>
                <input type="text" class="form-control" id="nombres" name="nombres" required>
                 <div class="invalid-feedback">
                    Por favor, ingrese los nombres.
                </div>
            </div>

            <div class="mb-3">
                <label for="apellidos" class="form-label">Apellidos</label>
                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                 <div class="invalid-feedback">
                    Por favor, ingrese los apellidos.
                </div>
            </div>

            <div class="mb-3">
                 <label for="password" class="form-label">Contraseña</label>
                 <input type="password" class="form-control" id="password" name="password_hash" required>
                 <div class="invalid-feedback">
                    Por favor, ingrese la contraseña.
                </div>
            </div>

            <div class="mb-3">
                <label for="rol_id" class="form-label">Rol</label>
                <select class="form-select" id="rol_id" name="rol_id" required>
                    <option value="">-- Selecciona un rol --</option>
                    <option value="1">Administrador</option>
                    <option value="2">Editor</option>
                    <option value="3">Visor</option>
                </select>
                 <div class="invalid-feedback">
                    Por favor, seleccione un rol.
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Guardar Usuario</button>
        </form>
    </div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<script>
    // Basic form validation (optional, but good with Bootstrap)
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>


</body>
</html>