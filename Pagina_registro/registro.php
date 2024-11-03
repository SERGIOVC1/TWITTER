<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Twitter</title>
    <link rel="stylesheet" href="/CSS/registro.css"> 
</head>

<body>
    <div class="register-container">
        <div class="register-box">
            <div class="logo">
                <img src="/img/Twitter-X-Logo" alt="Twitter Logo">
            </div>
            <h2>Registrarse en Twitter</h2>
            <form action="register.php" method="POST">
                <div class="input-group">
                    <input type="text" name="username" id="username" placeholder="Nombre de usuario" required>
                </div>
                <div class="input-group">
                    <input type="email" name="email" id="email" placeholder="Correo electrónico" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" id="password" placeholder="Contraseña" required>
                </div>
                <div class="input-group">
                    <button type="submit" class="register-btn">Registrarse</button>
                </div>
            </form>

            <div class="login-link">
                <p>¿Ya tienes una cuenta? <a href="../index.php">Inicia sesión en Twitter</a></p>
            </div>
        </div>
    </div>
</body>

</html>
