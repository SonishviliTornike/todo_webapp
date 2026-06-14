

<form action="" method="post">
    <label for="identity">Enter Username or Email:</label>
    <input type="text" name="login[identity]" id="identity" autocomplete="username" required value="<?=htmlspecialchars($identity ?? '', ENT_QUOTES, 'UTF-8')?>">

    <label for="password">Enter password:</label>
    <input type="password" name="login[password]" id="password" autocomplete="current-password" required>

    <input type="submit" name="submit" value="Log in">
</form>


<?php if (!empty($errors)): ?>
    <div class="errors-container">
        <?php foreach ($errors as $err_key => $err_message_array): ?>
            <?php foreach ($err_message_array as $message): ?>
                <p class="error-text"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8')?> </p>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif;?>
        
