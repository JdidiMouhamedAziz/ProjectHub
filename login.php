<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="public/css/login.css">
</head>
<body>

    <div class="login-card">

        <div class="logo">
            🔐
        </div>

        <h2>Connexion</h2>
        <p>Entrez vos identifiants pour accéder à l'application</p>
            
        <form action="controllers/authController.php" method="POST">

            <div class="mb-3">
                <input 
                    type="text" 
                    name="username" 
                    class="form-control"
                    placeholder="Nom d'utilisateur"
                    required
                >
            </div>

            <div class="mb-4">
                <input 
                    type="password" 
                    name="password" 
                    class="form-control"
                    placeholder="Mot de passe"
                    required
                >
            </div>

            <button type="submit" class="btn btn-login">
                Se connecter
            </button>

        </form>

    </div>

</body>
</html>