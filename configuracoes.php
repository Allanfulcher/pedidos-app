<?php
$title = "Configurações";
$page = "configuracoes";
include "header.php";
include "nav.php";
?>
<div class="container mt-4">
    <h3>Configurações do Usuário</h3>

    <!-- Form to Change Email -->
    <div class="card mb-3">
        <div class="card-header">Alterar E-mail</div>
        <div class="card-body">
            <form id="alterar-email-form">
                <div class="form-group mb-3">
                    <label for="novo-email" class="form-label">Novo E-mail</label>
                    <input type="email" class="form-control" id="novo-email" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Alterar E-mail</button>
            </form>
        </div>
    </div>

    <!-- Form to Change Password -->
    <div class="card mb-3">
        <div class="card-header">Alterar Senha</div>
        <div class="card-body">
            <form id="alterar-senha-form">
                <div class="form-group mb-3">
                    <label for="nova-senha" class="form-label">Nova Senha</label>
                    <input type="password" class="form-control" id="nova-senha" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Alterar Senha</button>
            </form>
        </div>
    </div>

    <!-- System Settings -->
    <div class="card mb-3">
        <div class="card-header">Configurações do Sistema</div>
        <div class="card-body">
            <form id="config-sistema-form">
                <div class="form-group mb-3">
                    <label for="config-moeda" class="form-label">Moeda</label>
                    <select class="form-select" id="config-moeda">
                        <option value="BRL" selected>BRL - Real Brasileiro</option>
                        <option value="USD">USD - Dólar Americano</option>
                        <option value="EUR">EUR - Euro</option>
                    </select>
                </div>
                <div class="form-group mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="config-notificacoes">
                    <label class="form-check-label" for="config-notificacoes">Ativar Notificações de Estoque Baixo</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Salvar Configurações</button>
            </form>
        </div>
    </div>
</div>

<?php include "firebase_config.php"; ?>
<script>
// Configurations Functionality
document.getElementById("logout-button").addEventListener("click", () => {
    auth.signOut().then(() => {
        window.location.href = "index.php";
    });
});

auth.onAuthStateChanged(user => {
    if (!user) {
        window.location.href = "index.php";
    } else {
        carregarConfiguracoes();
    }
});

document.getElementById("alterar-email-form").addEventListener("submit", function(e) {
    e.preventDefault();
    const novoEmail = document.getElementById("novo-email").value;
    const user = auth.currentUser;
    user.updateEmail(novoEmail).then(() => {
        Swal.fire("Sucesso", "E-mail atualizado com sucesso!", "success");
        db.collection("users").doc(user.uid).update({
            email: novoEmail
        }).catch(error => {
            console.error("Erro ao atualizar e-mail no Firestore:", error);
        });
    }).catch(error => {
        Swal.fire("Erro", error.message, "error");
    });
});

document.getElementById("alterar-senha-form").addEventListener("submit", function(e) {
    e.preventDefault();
    const novaSenha = document.getElementById("nova-senha").value;
    const user = auth.currentUser;
    user.updatePassword(novaSenha).then(() => {
        Swal.fire("Sucesso", "Senha atualizada com sucesso!", "success");
    }).catch(error => {
        Swal.fire("Erro", error.message, "error");
    });
});

document.getElementById("config-sistema-form").addEventListener("submit", function(e) {
    e.preventDefault();
    const moeda = document.getElementById("config-moeda").value;
    const notificacoes = document.getElementById("config-notificacoes").checked;
    const user = auth.currentUser;
    db.collection("users").doc(user.uid).set({
        moeda: moeda,
        notificacoesEstoque: notificacoes
    }, { merge: true }).then(() => {
        Swal.fire("Sucesso", "Configurações salvas com sucesso!", "success");
    }).catch(error => {
        Swal.fire("Erro", error.message, "error");
    });
});

function carregarConfiguracoes() {
    const user = auth.currentUser;
    db.collection("users").doc(user.uid).get().then(doc => {
        if (doc.exists) {
            const config = doc.data();
            if (config.moeda) {
                document.getElementById("config-moeda").value = config.moeda;
            }
            if (config.notificacoesEstoque !== undefined) {
                document.getElementById("config-notificacoes").checked = config.notificacoesEstoque;
            }
        }
    });
}
</script>
<?php include "footer.php"; ?>
