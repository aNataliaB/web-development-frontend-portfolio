<?php
require_once 'config.php';

$login = $email = $haslo = $confirm_haslo = '';
$login_err = $email_err = $haslo_err = $confirm_haslo_err = ''; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (empty(trim($_POST['login']))) {
        $login_err = 'Proszę wprowadzić nazwę użytkownika.';
    } else {
        $sql = 'SELECT id FROM users WHERE login = ?';
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('s', $param_login);
            $param_login = trim($_POST['login']);

            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $login_err = 'Ta nazwa użytkownika jest już zajęta.';
                } else {
                    $login = trim($_POST['login']);
                }
            } else {
                echo 'Coś poszło nie tak. Proszę spróbować ponownie później.';
            }
            $stmt->close();
        }
    }

    if (empty(trim($_POST['email']))) {
        $email_err = 'Proszę wprowadzić adres email.';
    } elseif (!filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)) {
        $email_err = 'Nieprawidłowy format emaila.';
    } else {
        $email = trim($_POST['email']);
    }

    if (empty(trim($_POST['haslo']))) {
        $haslo_err = 'Proszę wprowadzić hasło.';
    } elseif (!preg_match(haslo_REGEX, trim($_POST['haslo']))) {
        $haslo_err = haslo_REQUIREMENTS;
    } else {
        $haslo = trim($_POST['haslo']);
    }

    if (empty(trim($_POST['confirm_haslo']))) {
        $confirm_haslo_err = 'Proszę potwierdzić hasło.';
    } else {
        $confirm_haslo = trim($_POST['confirm_haslo']);
        if (empty($haslo_err) && ($haslo != $confirm_haslo)) {
            $confirm_haslo_err = 'Hasła nie pasują.';
        }
    }

    if (empty($login_err) && empty($email_err) && empty($haslo_err) && empty($confirm_haslo_err)) {

        $sql = 'INSERT INTO users (login, email, haslo_hash) VALUES (?, ?, ?)';

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('sss', $param_login, $param_email, $param_haslo_hash);

            $param_login = $login;
            $param_email = $email;
            $param_haslo_hash = password_hash($haslo, PASSWORD_DEFAULT); 

            if ($stmt->execute()) {
                header('location: login.php');
                exit;
            } else {
                echo 'Coś poszło nie tak. Proszę spróbować ponownie.';
            }

            $stmt->close();
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="p8style.css">
    <title>Rejestracja</title>
    
</head>
<body>
    <div class="wrapper">
        <h2>Rejestracja</h2>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <div class="form-group <?php echo (!empty($login_err)) ? 'has-error' : ''; ?>">
                <label>Nazwa użytkownika</label>
                <input type="text" name="login" class="form-control" value="<?php echo htmlspecialchars($login); ?>">
                <span class="help-block"><?php echo $login_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($haslo_err)) ? 'has-error' : ''; ?>">
                <label>Hasło</label>
                <input type="password" name="haslo" class="form-control" value=""> <span class="help-block"><?php echo $haslo_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_haslo_err)) ? 'has-error' : ''; ?>">
                <label>Potwierdź Hasło</label>
                <input type="password" name="confirm_haslo" class="form-control" value=""> <span class="help-block"><?php echo $confirm_haslo_err; ?></span>
                <p style="color: blue; font-size: 0.8em;"><?php echo haslo_REQUIREMENTS; ?></p>

            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-register" value="Zarejestruj">
            </div>
            <p>Masz już konto? <a href="login.php">Zaloguj się tutaj</a>.</p>
        </form>
    </div>
</body>
</html>