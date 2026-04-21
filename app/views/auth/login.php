<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card" style="max-width:500px; margin:auto;">
    <h2>Iniciar Sesión</h2>

    <form method="POST" action="/foro-universitario-php/public/login">

        <?= csrf_input(); ?>

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
            autocomplete="current-password"
        >

        <button class="btn" type="submit">
            Ingresar
        </button>
    </form>

    <p style="margin-top:18px; color:#6b7280;">
        ¿No tienes cuenta?
        <a href="/foro-universitario-php/public/register">
            Regístrate aquí
        </a>
    </p>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>