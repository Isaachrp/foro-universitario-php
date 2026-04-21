<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card" style="max-width:550px; margin:auto;">
    <h2>Crear cuenta</h2>

    <form <?= csrf_input(); ?> method="POST" action="/foro-universitario-php/public/register">

        <label>Nombre</label>
        <input
            type="text"
            name="nombre"
            required
            maxlength="100"
            autocomplete="name"
        >

        <label>Correo institucional</label>
        <input
            type="email"
            name="email"
            required
            autocomplete="email"
        >

        <label>Contraseña</label>
        <input
            type="password"
            name="password"
            required
            autocomplete="new-password"
        >

        <button class="btn" type="submit">
            Registrarse
        </button>
    </form>

    <p style="margin-top:18px; color:#6b7280;">
        ¿Ya tienes cuenta?
        <a href="/foro-universitario-php/public/login">
            Inicia sesión
        </a>
    </p>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>   