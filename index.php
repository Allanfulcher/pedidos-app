<?php
$title = "Login";
include "header.php";
?>
<div class="container mt-5">
    <h2 class="mb-4 text-center">Login</h2>
    <form id="login-form">
        <div class="form-group mb-3">
            <label for="login-email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="login-email" required>
        </div>
        <div class="form-group mb-3">
            <label for="login-password" class="form-label">Senha</label>
            <input type="password" class="form-control" id="login-password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Entrar</button>
    </form>
    <p class="mt-3 text-center">Não tem uma conta? <a href="register.php">Registre-se</a></p>
    <p class="text-center"><a href="forgot-password.php">Esqueci minha senha</a></p>
</div>

<?php include "firebase_config.php"; ?>
<script>
// Login Functionality
document.getElementById("login-form").addEventListener("submit", function(e) {
    e.preventDefault();
    const email = document.getElementById("login-email").value;
    const password = document.getElementById("login-password").value;
    auth.signInWithEmailAndPassword(email, password)
        .then(() => {
            window.location.href = "dashboard.php";
        })
        .catch(error => {
            Swal.fire("Erro", error.message, "error");
        });
});

// Redirect if already logged in
auth.onAuthStateChanged(user => {
    if (user) {
        window.location.href = "dashboard.php";
    }
});
</script>
<?php include "footer.php"; ?>
