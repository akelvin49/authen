<div style="width: 500px; max-width: 100%; padding: 10px; font-family: 'Trebuchet MS', sans-serif; font-size: 1.2em;">
    <h4>Presado(a) <?= $user->first_name; ?>,</h4>
    <p>Sua conta em nosso site teve a senha recuperada recentemente, por favor, caso tenha solicitado ignore este e-mail. Caso contrário... </p>
    <p><a href="<?= $link; ?>" title="Recuperar Senha">CLIQUE AQUI PARA SOLICITAR RECUPERAÇÃO DE SENHA</a></p>
    <p>Atenciosamente <?= site("name"); ?></p>
</div>