<?php require_once __DIR__ . '/layouts/header.php'; ?>

<div class="card">
    <h1>
        Bienvenido, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋
    </h1>

    <!-- <p style="color:#6b7280; margin-bottom:20px;">
        Rol:
        <strong><?= htmlspecialchars($_SESSION['user_role']) ?></strong>
    </p> -->

    <div style="display:flex; gap:12px; flex-wrap:wrap;">
        <a class="btn" href="/foro-universitario-php/public/posts">
            Ver publicaciones
        </a>

        <a class="btn" href="/foro-universitario-php/public/posts/create">
            Crear publicación
        </a>

        <a class="btn secondary" href="/foro-universitario-php/public/logout">
            Cerrar sesión
        </a>
    </div>
</div>

<?php if (Auth::isAdmin()): ?>

    <div class="card">
        <h3>Administrar usuarios</h3>

        <table id="usersTable" class="display">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['nombre']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= $u['rol'] ?></td>
                        <td>
                            <?= $u['is_banned'] ? 'Baneado' : 'Activo' ?>
                        </td>
                        <td style="display:flex; gap:6px; flex-wrap:wrap;">

                            <!-- BAN / UNBAN -->
                            <?php if (!$u['is_banned']): ?>
                                <form method="POST" action="/foro-universitario-php/public/admin/ban">
                                    <?= csrf_input(); ?>
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <button class="btn">Ban</button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="/foro-universitario-php/public/admin/unban">
                                    <?= csrf_input(); ?>
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <button class="btn secondary">Unban</button>
                                </form>
                            <?php endif; ?>

                            <!-- DELETE -->
                            <form method="POST" action="/foro-universitario-php/public/admin/users/delete"
                                onsubmit="return confirm('¿Eliminar usuario?')">
                                <?= csrf_input(); ?>
                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                <button class="btn danger">Delete</button>
                            </form>

                            <!-- EDIT (simple inline form) -->
                            <form method="POST" action="/foro-universitario-php/public/admin/users/update">
                                <?= csrf_input(); ?>
                                <input type="hidden" name="id" value="<?= $u['id'] ?>">

                                <input type="text" name="nombre" value="<?= htmlspecialchars($u['nombre']) ?>" required>
                                <input type="email" name="email" value="<?= htmlspecialchars($u['email']) ?>" required>

                                <select name="rol">
                                    <option value="user" <?= $u['rol'] === 'user' ? 'selected' : '' ?>>user</option>
                                    <option value="admin" <?= $u['rol'] === 'admin' ? 'selected' : '' ?>>admin</option>
                                </select>

                                <button class="btn">Update</button>
                            </form>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php endif; ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    $('#usersTable').DataTable({
        pageLength: 10,
        order: [[0, "desc"]],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json"
        }
    });
});
</script>
<?php require_once __DIR__ . '/layouts/footer.php'; ?>