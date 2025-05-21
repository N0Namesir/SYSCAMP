<?php
include("configuracion/conexion.php");
// Procesar envío del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_rol = $_POST['rol_id']; // ¡Esta línea es crucial y ahora debería estar correcta!
    $codigo_persona = $_POST['codigo_persona'];
    $correo_persona = $_POST['correo_persona'];
    $clave_persona = $_POST['clave_persona'];
    $nombre_persona = $_POST['nombre_persona'];
    $apellido_persona = $_POST['apellido_persona'];
    $documento_de_identificacion = $_POST['documento_de_identificacion'];
    $id_distrito_reside = $_POST['id_distrito_reside'];
    $id_departamento_labora = $_POST['id_departamento_labora'];

    // Insertar en la base de datos
    $query = "INSERT INTO persona (id_rol, codigo_persona, correo_persona, clave_persona, nombre_persona, apellido_persona, documento_de_identificacion, id_distrito_reside, id_departamento_labora ) VALUES ('$id_rol','$codigo_persona', '$correo_persona', '$clave_persona', '$nombre_persona', '$apellido_persona','$documento_de_identificacion', '$id_distrito_reside', '$id_departamento_labora')";
    
    $resultado = pg_query($conexion, $query);

    if ($resultado) {
        $mensaje = "✅ Usuario insertado correctamente.";
    } else {
        $mensaje = "❌ Error al insertar: " . pg_last_error($conexion);
    }
}
include_once("header.php");
?>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Registro de Gestores</h2>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-info text-center">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <form method="post" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label for="codigo_persona" class="form-label">Codigo de gestor</label>
                <input type="number" class="form-control" id="codigo_persona" name="codigo_persona" required>
            </div>

             <div class="mb-3">
                <label for="correo_persona" class="form-label">Correo</label>
                <input type="email" class="form-control" id="correo_persona" name="correo_persona" required>
            </div>

             <div class="mb-3">
                <label for="clave_persona" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="clave_persona" name="clave_persona" required>
            </div>
        
            <div class="mb-3">
                <label for="nombre_persona" class="form-label">Nombres</label>
                <input type="text" class="form-control" id="nombre_persona" name="nombre_persona" required>
            </div>
            
            <div class="mb-3">
                <label for="apellido_persona" class="form-label">Apellidos</label>
                <input type="text" class="form-control" id="apellido_persona" name="apellido_persona" required>
            </div>

            <div class="mb-3">
                <label for="documento_de_identificacion" class="form-label">DUI</label>
                <input type="text" class="form-control" id="documento_de_identificacion" name="documento_de_identificacion" required>
            </div>

            
            <div class="mb-3">
                <label for="id_departamento_labora" class="form-label">Departamento en que labora</label>
                <select class="form-select" id="id_departamento_labora" name="id_departamento_labora" required>
                <option value="">--Selecciona un departamento--</option>
                <?php
                $query = "SELECT * FROM departamento";
                $resultado = pg_query($conexion, $query);
                while ($fila = pg_fetch_assoc($resultado)){
                echo "<option value='".$fila['id_departamento']."'>".$fila['nombre_departamento']."</option>";
                }
                ?>
               </select>
         </div>

          <div class="mb-3">
               <label for="id_distrito_reside" class="form-label">Distrito en que reside</label>
            <select class="form-select" id="id_distrito_reside" name="id_distrito_reside" required>
            <option value="">--Selecciona un distrito--</option>
            <?php
            $query = "SELECT * FROM distrito";
            $resultado = pg_query($conexion, $query);
            while ($fila = pg_fetch_assoc($resultado)){
                echo "<option value='".$fila['id_distrito']."'>".$fila['nombre_distrito']."</option>";
            }
            ?>
         </select>
         </div>


            <div class="mb-3">
                <label for="rol_id" class="form-label">Rol</label>
                <select class="form-select" id="rol_id" name="rol_id" required>
            <?php
            $query = "SELECT * FROM rol";
            $resultado = pg_query($conexion, $query);
            while ($fila = pg_fetch_assoc($resultado)){
                echo "<option value='".$fila['id_rol']."'>".$fila['nombre_rol']."</option>";
            }
            ?>
            </select>

            <button type="submit" value="Registrar" class="btn btn-primary w-100">Guardar Gestor</button>
        </form>
    </div>

<?php include_once("footer.php") ?>