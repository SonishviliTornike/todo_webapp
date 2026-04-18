<h2>Register an account</h2>
<form action="" method="post">
    <label for="userName">Enter username:</label>
    <input type="text" name="userName" id="userName" required maxlength="55" minlength="3" value="<?= htmlspecialchars($rawData['userName'] ?? '', ENT_QUOTES, 'UTF-8') ?? '' ?>">

    <label for="email">Enter email address:</label>
    <input type="text" name="email" id="email" required maxlength="254" value="<?= htmlspecialchars($rawData['email'] ?? '', ENT_QUOTES, 'UTF-8')?>">

    <label for="fullName">Enter full name:</label>
    <input type="text" name="fullName" id="fullName" required maxlength="100" value="<?= htmlspecialchars($rawData['fullName'] ?? '', ENT_QUOTES, 'UTF-8')?>">

    <label for="password">Enter password</label>
    <input type="password" name="password" id="password">
    
    <input type="submit" name="submit" id="submitUser" value="Register">
    <a class="btn" href="/tasks/home">Return</a>

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