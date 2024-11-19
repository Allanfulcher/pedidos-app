<?php
$title = "Registro";
include "header.php";
?>
<div class="container mt-5">
    <h2 class="mb-4 text-center">Registro</h2>
    <form id="register-form">
        <div class="form-group mb-3">
            <label for="register-email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="register-email" required>
        </div>
        <div class="form-group mb-3">
            <label for="register-password" class="form-label">Senha</label>
            <input type="password" class="form-control" id="register-password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Registrar</button>
    </form>
    <p class="mt-3 text-center">Já tem uma conta? <a href="index.php">Faça login</a></p>
</div>

<?php include "firebase_config.php"; ?>
<script>
// Registration Functionality
document.getElementById("register-form").addEventListener("submit", function(e) {
    e.preventDefault();
    const email = document.getElementById("register-email").value;
    const password = document.getElementById("register-password").value;
    auth.createUserWithEmailAndPassword(email, password)
        .then(userCredential => {
            return db.collection("users").doc(userCredential.user.uid).set({
                email: email,
                createdAt: firebase.firestore.FieldValue.serverTimestamp()
            });
        })
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
