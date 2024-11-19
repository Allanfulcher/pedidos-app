<?php
$title = "Redefinir Senha";
include "header.php";
?>
<div class="container mt-5">
    <h2 class="mb-4 text-center">Redefinir Senha</h2>
    <form id="forgot-password-form">
        <div class="form-group mb-3">
            <label for="forgot-email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="forgot-email" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Enviar Link de Redefinição</button>
    </form>
    <p class="mt-3 text-center"><a href="index.php">Voltar para o Login</a></p>
</div>

<?php include "firebase_config.php"; ?>
<script>
// Password Reset Functionality
document.getElementById("forgot-password-form").addEventListener("submit", function(e) {
    e.preventDefault();
    const email = document.getElementById("forgot-email").value;
    auth.sendPasswordResetEmail(email)
        .then(() => {
            Swal.fire("Sucesso", "Link de redefinição de senha enviado para o e-mail.", "success");
            window.location.href = "index.php";
        })
        .catch(error => {
            Swal.fire("Erro", error.message, "error");
        });
});
</script>
<?php include "footer.php"; ?>
