<?php
require_once 'config.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('location: p8.php'); 
    exit;
}

$default_login = 'user_domyslny';
$default_haslo = 'Haslo123!'; 

$login = $haslo = '';
$login_err = $haslo_err = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (empty(trim($_POST['login']))) {
        $login_err = 'Proszę wprowadzić nazwę użytkownika.';
    } else {
        $login = trim($_POST['login']);
    }

    if (empty(trim($_POST['haslo']))) {
        $haslo_err = 'Proszę wprowadzić hasło.';
    } else {
        $haslo = trim($_POST['haslo']);
    }

    if (empty($login_err) && empty($haslo_err)) {
        $sql = 'SELECT id, login, haslo_hash FROM users WHERE login = ?';

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('s', $param_login);
            $param_login = $login;

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $login, $hashed_haslo);
                    if ($stmt->fetch()) {
                        if (password_verify($haslo, $hashed_haslo)) { 
                            $_SESSION['loggedin'] = true;
                            $_SESSION['id'] = $id;
                            $_SESSION['login'] = $login;

                            header('location: p8.php');
                            exit;
                        } else {
                            $login_err = 'Nieprawidłowa nazwa użytkownika lub hasło.';
                        }
                    }
                } else {
                    $login_err = 'Nieprawidłowa nazwa użytkownika lub hasło.';
                }
            } else {
                echo 'Coś poszło nie tak. Proszę spróbować ponownie później.';
            }

            $stmt->close();
        }
    }

    $conn->close();
} else {
    $login = $default_login;
    $haslo = $default_haslo; 
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="p8style.css">
    <title>Logowanie</title>
</head>
<body>
    <div class="wrapper">
        <h2>Logowanie</h2>
        
        <?php 
        if (!empty($login_err) && strpos($login_err, 'Proszę') === false): ?>
            <div class="alert-danger"><?php echo $login_err; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <div class="form-group <?php echo (!empty($login_err) && strpos($login_err, 'Proszę') !== false) ? 'has-error' : ''; ?>">
                <label>Nazwa użytkownika</label>
                <input type="text" name="login" class="form-control" value="<?php echo htmlspecialchars($login); ?>">
                <?php if (!empty($login_err) && strpos($login_err, 'Proszę') !== false): ?>
                    <span class="help-block"><?php echo $login_err; ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group <?php echo (!empty($haslo_err)) ? 'has-error' : ''; ?>">
                <label>Hasło</label>
                <input type="password" name="haslo" class="form-control" value="<?php echo htmlspecialchars($haslo); ?>">
                <?php if (!empty($haslo_err)): ?>
                    <span class="help-block"><?php echo $haslo_err; ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-login" value="Zaloguj">
            </div>
            <p>Nie masz konta? <a href="register.php">Zarejestruj się teraz</a>.</p>
        </form>
    </div>
</body>
</html>