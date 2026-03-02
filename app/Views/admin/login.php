<?php use App\Core\Csrf; ?>
<section class="login-wrap">
    <h1>Admin BabadoVip</h1>
    <form method="post" class="grid-form narrow">
        <?= Csrf::field() ?>
        <label>E-mail
            <input type="email" name="email" required>
        </label>
        <label>Senha
            <input type="password" name="senha" required>
        </label>
        <button type="submit" class="btn-primary">Entrar</button>
    </form>
</section>
