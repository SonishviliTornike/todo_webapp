
<div class="register-container">
    <h2>Register an account</h2>
</div>

<form action="" method="post">
    <label for="user_name">Enter username:</label>
    <input type="text" name="users[user_name]" id="user_name" required maxlength="55" minlength="3" value="<?= htmlspecialchars($rawData['user_name'] ?? '', ENT_QUOTES, 'UTF-8')?>">

    <label for="email">Enter email address:</label>
    <input type="text" name="users[email]" id="email" required maxlength="254" value="<?= htmlspecialchars($rawData['email'] ?? '', ENT_QUOTES, 'UTF-8')?>">

    <label for="full_name">Enter full name:</label>
    <input type="text" name="users[full_name]" id="full_name" required maxlength="100" value="<?= htmlspecialchars($rawData['full_name'] ?? '', ENT_QUOTES, 'UTF-8')?>">

    <label for="password">Enter password</label>
    <input type="password" name="users[password]" id="password">
    
    <label for="second_password">Password confirmation:</label>
    <input type="password" name="users[second_password]" id="second_password" >

    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8')?>">

    <input type="submit" name="submit" id="submit_user" value="Register">
</form>


<?php if (!empty($errors)): ?>
    <div class="errors-container">
        <?php foreach ($errors as $err_key => $err_message_array): ?>
            <?php foreach($err_message_array as $message): ?>
                <p class="error-text">
                    <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </p>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>