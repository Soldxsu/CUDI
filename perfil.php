<?php
session_start();
include 'conexion.php';

/*
 Nota: el código original obtiene el primer usuario (LIMIT 1).
 En un sistema real deberías usar el id del usuario en sesión, p.ej. $_SESSION['user_id'].
 Mantengo tu lógica original para no romper el flujo.
*/

// Obtener el primer registro
$sql = "SELECT id_usuario, nombre_usuario, contraseña FROM usuarios LIMIT 1";
$resultado = $conn->query($sql);

if ($resultado && $row = $resultado->fetch_assoc()) {
    $id_usuario = $row['id_usuario'];
    $nombre_usuario = $row['nombre_usuario'];
    $contraseña = $row['contraseña'];
} else {
    die("No se encontró el usuario.");
}

/*
  Endpoint AJAX para verificar contraseña sin exponerla al cliente.
  Se espera POST { action: 'check_password', current_password: '...' }
  Responde JSON { ok: true } o { ok:false, msg: '...' }
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'check_password') {
    $current = isset($_POST['current_password']) ? $_POST['current_password'] : '';
    // Comparamos en texto plano como en tu BD original. Si usas hashing, cambiar aquí.
    if ($current === $contraseña) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => true]);
        exit;
    } else {
        header('Content-Type: application/json', true, 401);
        echo json_encode(['ok' => false, 'msg' => 'Contraseña incorrecta.']);
        exit;
    }
}

/* Manejo de actualización del perfil (form tradicional) */
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $nuevo_nombre = $_POST['nombre_usuario'] ?? '';
    $nueva_contraseña = $_POST['contraseña'] ?? '';

    $stmt = $conn->prepare("UPDATE usuarios SET nombre_usuario = ?, contraseña = ? WHERE id_usuario = ?");
    $stmt->bind_param("ssi", $nuevo_nombre, $nueva_contraseña, $id_usuario);

    if ($stmt->execute()) {
        $nombre_usuario = $nuevo_nombre;
        $contraseña = $nueva_contraseña;
        $mensaje = "Datos actualizados correctamente.";
    } else {
        $mensaje = "Error al actualizar los datos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Perfil de Usuario</title>
    <style>
        /* ---------- Tipografía y fondo ---------- */
        :root{
            --accent-1: #3B6CDC;
            --accent-2: #6BD4E2;
            --card-bg: #ffffff;
            --muted: #6b7280;
        }
        html,body{ height:100%; }
        body {
            font-family: "Inter", "Segoe UI", Roboto, Arial, sans-serif;
            margin: 0;
            background-color: #ffffffff;
            min-height: 100vh;
            background: linear-gradient(180deg, rgba(59,108,220,0.08) 0%, rgba(107,212,226,0.04) 40%, rgba(255,255,255,1) 100%);
            color: #0f172a;
            -webkit-font-smoothing:antialiased;
            -moz-osx-font-smoothing:grayscale;
            padding-top: 110px; /* espacio para el nav */
            box-sizing: border-box;
        }

        /* ---------- Navbar (mantuve estructura pero mejoré aspecto sutilmente) ---------- */
        nav.nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        
        width: 100%;
        height: 80px;
        padding: 30px;
        box-sizing: border-box;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
    }

    nav.nav::after {
        content: "";
        position: absolute;
        top: 90px;
        right: 0px;
        left: 0px;
        height: 10px;
        width: 100%;
        background: linear-gradient(to right, #3B6CDC, #6BD4E2);
    }

    #logo {
        width: 100px;
        font-weight: bold;
        color: rgb(0, 0, 0);
        font-family: 'Segoe UI';
        text-decoration: none;
    }
    .nav-links a {
            color: black;
            text-decoration: none;
            margin-left: 20px;
            font-size: 18px;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
    .nav-links {
        display: flex;
        list-style: none;
        gap: 35px;
        margin: 0;
        margin-left: 700px;
        padding: 0;
    }

    .nav-links {
        color: rgb(0, 0, 0);
        cursor: pointer;
        font-size: 20px;
        font-family: 'Segoe UI';
    }
    .nav-links a:hover {
        background: linear-gradient(to right, #3B6CDC, #6BD4E2);
        color:white;
        }

        /* ---------- Layout principal ---------- */
        .wrapper {
            max-width: 1100px;
            margin: 0 auto;
            padding: 28px;
            display: flex;
            gap: 28px;
            align-items: flex-start;
            justify-content: center;
            box-sizing: border-box;
        }

        /* Centrar la tarjeta en pantalla */
        .perfil {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 18px;
        }

        /* ---------- Card ---------- */
        .card {
            width: 100%;
            max-width: 720px;
            background: linear-gradient(180deg,#ffffff, #fbfdff);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 8px 30px rgba(12,18,40,0.08);
            border: 1px solid rgba(12,18,40,0.04);
            overflow: hidden;
        }

        .card-grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 18px;
            align-items: start;
        }

        /* Header de la card */
        .header-card {
            display: flex;
            align-items: center;
            gap: 14px;
            border-bottom: 1px solid rgba(12,18,40,0.04);
            padding-bottom: 14px;
        }

        .header-left h2 {
            margin: 0;
            font-size: 20px;
            letter-spacing: -0.2px;
        }
        .header-left p {
            margin: 4px 0 0 0;
            color: var(--muted);
            font-size: 14px;
        }

        #profile {
            width: 96px;
            height: 96px;
            border-radius: 12px;
            object-fit: cover;
            border: 4px solid rgba(59,108,220,0.08);
            box-shadow: 0 6px 18px rgba(59,108,220,0.06);
        }

        /* ---------- Info y formulario ---------- */
        .info {
            padding: 12px 6px;
        }
        .info p { margin: 8px 0; color:#111827; font-weight:600; }
        .info small { color: var(--muted); font-weight: 500; display:block; margin-top:4px; }

        .info .actions {
            margin-top: 14px;
            display:flex;
            gap: 12px;
            align-items:center;
        }

        .btn {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            padding: 10px 14px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-weight: 700;
        }
        .btn-primary {
            background: linear-gradient(90deg, var(--accent-1), var(--accent-2));
            color: #fff;
            box-shadow: 0 8px 20px rgba(59,108,220,0.12);
        }
        .btn-ghost {
            background: transparent;
            border: 1px solid rgba(12,18,40,0.06);
            color: #0f172a;
        }
        .btn-danger {
            background: #ef4444;
            color: white;
        }

        /* Formulario de edición */
        .formulario-editar {
            display: none;
            margin-top: 6px;
        }
        .form-row {
            display:flex;
            flex-direction: column;
            gap:8px;
            margin: 10px 0;
        }
        label { font-size: 14px; color: #374151; font-weight:600; }
        input[type="text"], input[type="password"] {
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid rgba(12,18,40,0.08);
            background: #fff;
            outline: none;
            font-size: 15px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.6);
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border-color: rgba(59,108,220,0.55);
            box-shadow: 0 6px 18px rgba(59,108,220,0.06);
        }

        .mensaje {
            margin: 12px 0;
            color: #059669;
            font-weight: 700;
        }

        /* Logout link estilo */
        #salir {
            display: inline-block;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 10px;
            background: #fff;
            border: 1px solid rgba(12,18,40,0.06);
            color: #ef4444;
            font-weight: 700;
            transition: all .12s ease;
        }
        #salir:hover {
            transform: translateY(-2px);
            background: #fff;
            box-shadow: 0 8px 20px rgba(239,68,68,0.06);
        }

        /* ---------- Modal de verificación de contraseña ---------- */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            display: none;
            justify-content: center;
            align-items: center;
            background: rgba(2,6,23,0.45);
            z-index: 1200;
        }
        .modal-backdrop.show { display:flex; }

        .modal-card {
            background: #fff;
            border-radius: 12px;
            padding: 18px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 12px 40px rgba(2,6,23,0.18);
        }
        .modal-card h3 { margin: 0 0 8px 0; font-size: 18px; }
        .modal-row { display:flex; gap:8px; margin-top:12px; }
        .modal-row input { flex:1; }

        .modal-actions { margin-top: 14px; display:flex; gap:10px; justify-content: flex-end; }

        /* ---------- Responsive ---------- */
        @media (max-width: 880px) {
            .card-grid { grid-template-columns: 1fr; }
            #logo { width: 80px; }
            .wrapper { padding: 12px; }
            .nav-links { display:none; } /* simplificamos en móvil */
        }
    </style>
</head>
<body>
    <nav class="nav">
        <a href="index.php"><img id="logo" src="img/logo.png"  alt="logo"></a>
        <div class="nav-links">
            <a href="disposicionaulica.php">Disposición Áulica</a>
            <a href="#">Insumos</a>
        </div>
        <a href="perfil.php"><img id="perfil" src="img/perfil.webp" width='65px' alt="perfil"></a>
    </nav>

    <main class="wrapper" role="main">
        <section class="perfil">
            <div class="card" aria-labelledby="perfil-title">
                <div class="card-grid">
                    <div>
                        <div class="header-card">
                            <img id="profile" src="img/perfil.webp" alt="Imagen de perfil">
                            <div class="header-left">
                                <h2 id="perfil-title">Perfil de usuario</h2>
                                <p>Revisa y actualiza tus datos personales</p>
                            </div>
                        </div>

                        <?php if (!empty($mensaje)): ?>
                            <div class="mensaje"><?php echo htmlspecialchars($mensaje); ?></div>
                        <?php endif; ?>

                        <div class="info" id="infoBlock">
                            <p>Usuario: <span style="font-weight:800;"><?php echo htmlspecialchars($nombre_usuario); ?></span></p>
                            <small>Contraseña: ••••••••</small>

                            <div class="actions">
                                <button class="btn btn-primary" id="btnEditar">Editar</button>
                                <button class="btn btn-ghost" id="btnCambiarImagen" title="Cambiar imagen (no implementado)">Cambiar imagen</button>
                            </div>
                        </div>

                        <div class="formulario-editar" id="formularioEditar">
                            <form method="POST" novalidate>
                                <input type="hidden" name="update_profile" value="1">
                                <div class="form-row">
                                    <label for="nombre_usuario">Nombre de usuario</label>
                                    <input id="nombre_usuario" name="nombre_usuario" type="text" required value="<?php echo htmlspecialchars($nombre_usuario); ?>">
                                </div>

                                <div class="form-row">
                                    <label for="contraseña">Contraseña</label>
                                    <input id="contraseña" name="contraseña" type="text" required value="<?php echo htmlspecialchars($contraseña); ?>">
                                </div>

                                <div style="display:flex; gap:10px; margin-top:12px;">
                                    <button type="submit" class="btn btn-primary">Actualizar</button>
                                    <button type="button" class="btn btn-ghost" id="btnCancelarEditar">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Panel derecho resumen / logout -->
                    <aside style="padding:12px;">
                        <div style="background: linear-gradient(180deg, rgba(59,108,220,0.06), rgba(107,212,226,0.03)); padding:12px; border-radius:10px;">
                            <p style="margin:0; color:#0f172a; font-weight:700;">Cuenta</p>
                            <small style="color:var(--muted);">ID: <?php echo htmlspecialchars($id_usuario); ?></small>
                        </div>

                        <div style="margin-top:16px;">
                            <!-- confirm simple -->
                            <a id="salir" href="logout.php" onclick="return confirm('¿Quieres cerrar sesión?')">Cerrar sesión</a>
                        </div>
                    </aside>
                </div>
            </div>
        </section>
    </main>

    <!-- Modal verificación contraseña -->
    <div class="modal-backdrop" id="passwordModal" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="modal-card" role="document" aria-labelledby="modal-title">
            <h3 id="modal-title">Verificar contraseña</h3>
            <p style="margin:0; color:var(--muted);">Ingresa tu contraseña actual para editar tu perfil.</p>

            <div class="modal-row" style="margin-top:12px;">
                <input id="modalPassword" type="password" placeholder="Contraseña actual" aria-label="Contraseña actual">
            </div>

            <div class="modal-actions">
                <button class="btn btn-ghost" id="modalCancel">Cancelar</button>
                <button class="btn btn-primary" id="modalConfirm">Verificar</button>
            </div>

            <div id="modalError" style="color:#ef4444; margin-top:10px; display:none;"></div>
        </div>
    </div>

    <script>
        (function(){
            const btnEditar = document.getElementById('btnEditar');
            const infoBlock = document.getElementById('infoBlock');
            const formBlock = document.getElementById('formularioEditar');
            const btnCancelar = document.getElementById('btnCancelarEditar');

            const passwordModal = document.getElementById('passwordModal');
            const modalPassword = document.getElementById('modalPassword');
            const modalConfirm = document.getElementById('modalConfirm');
            const modalCancel = document.getElementById('modalCancel');
            const modalError = document.getElementById('modalError');

            function showModal() {
                passwordModal.classList.add('show');
                passwordModal.removeAttribute('aria-hidden');
                modalPassword.value = '';
                modalError.style.display = 'none';
                modalPassword.focus();
                document.body.style.overflow = 'hidden';
            }
            function hideModal() {
                passwordModal.classList.remove('show');
                passwordModal.setAttribute('aria-hidden','true');
                modalError.style.display = 'none';
                document.body.style.overflow = '';
            }

            btnEditar.addEventListener('click', function(e){
                e.preventDefault();
                showModal();
            });

            modalCancel.addEventListener('click', function() {
                hideModal();
            });

            // click fuera del modal para cerrarlo
            passwordModal.addEventListener('click', function(e){
                if (e.target === passwordModal) hideModal();
            });

            // Verificar contraseña vía AJAX (fetch) para no exponerla en el cliente
            modalConfirm.addEventListener('click', function() {
                const pwd = modalPassword.value.trim();
                if (!pwd) {
                    modalError.textContent = 'Ingresá tu contraseña.';
                    modalError.style.display = 'block';
                    modalPassword.focus();
                    return;
                }
                modalConfirm.disabled = true;
                modalConfirm.textContent = 'Verificando...';

                // Enviamos al mismo archivo con action=check_password
                fetch('', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'check_password',
                        current_password: pwd
                    })
                }).then(async (res) => {
                    modalConfirm.disabled = false;
                    modalConfirm.textContent = 'Verificar';
                    if (res.ok) {
                        const json = await res.json();
                        if (json.ok) {
                            hideModal();
                            // mostrar form y ocultar info
                            infoBlock.style.display = 'none';
                            formBlock.style.display = 'block';
                            document.getElementById('nombre_usuario').focus();
                        } else {
                            modalError.textContent = json.msg || 'Error';
                            modalError.style.display = 'block';
                        }
                    } else {
                        // fallback mostrar mensaje del servidor
                        let data;
                        try { data = await res.json(); } catch(e) { data = null; }
                        modalError.textContent = data && data.msg ? data.msg : 'Contraseña incorrecta.';
                        modalError.style.display = 'block';
                    }
                }).catch((err) => {
                    modalConfirm.disabled = false;
                    modalConfirm.textContent = 'Verificar';
                    modalError.textContent = 'Error de red. Intentá de nuevo.';
                    modalError.style.display = 'block';
                });
            });

            // cancelar edición
            btnCancelar.addEventListener('click', function(){
                formBlock.style.display = 'none';
                infoBlock.style.display = 'block';
            });

            // permitir Enter en el input para verificar
            modalPassword.addEventListener('keydown', function(e){
                if (e.key === 'Enter') {
                    e.preventDefault();
                    modalConfirm.click();
                }
            });

        })();
    </script>
</body>
</html>
