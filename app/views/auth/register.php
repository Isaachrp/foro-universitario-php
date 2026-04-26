<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card" style="max-width:550px; margin:auto;">
    
    <h2>Crear cuenta</h2>

    <p style="margin-top:5px; color:#6b7280; font-size:14px;">
        Usa tu correo institucional (ej: alumno@upatlacomulco.edu.mx)
    </p>

    <form method="POST" action="/foro-universitario-php/public/register">

        <?= csrf_input(); ?>

        <label>Nombre</label>
        <input
            type="text"
            name="nombre"
            required
            maxlength="100"
            autocomplete="name"
            placeholder="Tu nombre completo"
        >

        <label>Correo institucional</label>
        <input
            type="email"
            name="email"
            required
            autocomplete="email"
            placeholder="ejemplo@upatlacomulco.edu.mx"
        >

        <label>Contraseña</label>
        <input
            type="password"
            name="password"
            required
            autocomplete="new-password"
            placeholder="Mínimo 6 caracteres"
        >

        <button class="btn" type="submit">
            Registrarse
        </button>
    </form>

    <p style="margin-top:14px; font-size:13px; color:#6b7280;">
        Después de registrarte, recibirás un correo para activar tu cuenta.
    </p>

    <p style="margin-top:18px; color:#6b7280;">
        ¿Ya tienes cuenta?
        <a href="/foro-universitario-php/public/login">
            Inicia sesión
        </a>
    </p>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>