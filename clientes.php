<?php
$title = "Clientes";
$page = "clientes";
include "header.php";
include "nav.php";
?>
<div class="container mt-4">
    <h3>Gerenciar Clientes</h3>

    <!-- Form to Add New Client -->
    <div class="card mb-3">
        <div class="card-header">Adicionar Novo Cliente</div>
        <div class="card-body">
            <form id="add-client-form">
                <div class="form-group mb-3">
                    <label for="cliente-nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="cliente-nome" required>
                </div>
                <div class="form-group mb-3">
                    <label for="cliente-email" class="form-label">E-mail</label>
                    <input type="email" class="form-control" id="cliente-email" required>
                </div>
                <div class="form-group mb-3">
                    <label for="cliente-telefone" class="form-label">Telefone</label>
                    <input type="text" class="form-control" id="cliente-telefone" required>
                </div>
                <div class="form-group mb-3">
                    <label for="cliente-status" class="form-label">Status</label>
                    <select class="form-select" id="cliente-status">
                        <option value="ativo" selected>Ativo</option>
                        <option value="inativo">Inativo</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success w-100">Adicionar Cliente</button>
            </form>
        </div>
    </div>

    <!-- Clients List -->
    <h4>Lista de Clientes</h4>
    <div class="table-responsive">
        <table class="table table-bordered" id="clientes-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- Clients will be dynamically added here -->
            </tbody>
        </table>
    </div>
</div>

<?php include "firebase_config.php"; ?>
<script>
// Clients Functionality
document.getElementById("logout-button").addEventListener("click", () => {
    auth.signOut().then(() => {
        window.location.href = "index.php";
    });
});

auth.onAuthStateChanged(user => {
    if (!user) {
        window.location.href = "index.php";
    } else {
        carregarClientes();
    }
});

document.getElementById("add-client-form").addEventListener("submit", function(e) {
    e.preventDefault();
    const nome = document.getElementById("cliente-nome").value;
    const email = document.getElementById("cliente-email").value;
    const telefone = document.getElementById("cliente-telefone").value;
    const status = document.getElementById("cliente-status").value;

    db.collection("clientes").add({
        nome: nome,
        email: email,
        telefone: telefone,
        status: status,
        createdAt: firebase.firestore.FieldValue.serverTimestamp()
    }).then(() => {
        Swal.fire("Sucesso", "Cliente adicionado com sucesso!", "success");
        document.getElementById("add-client-form").reset();
        carregarClientes();
    }).catch(error => {
        Swal.fire("Erro", error.message, "error");
    });
});

function carregarClientes() {
    db.collection("clientes").orderBy("nome").onSnapshot(snapshot => {
        const tbody = document.querySelector("#clientes-table tbody");
        tbody.innerHTML = "";
        snapshot.forEach(doc => {
            const cliente = doc.data();
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${cliente.nome}</td>
                <td>${cliente.email}</td>
                <td>${cliente.telefone}</td>
                <td>${cliente.status}</td>
                <td>
                    <button class="btn btn-sm btn-primary edit-button" data-id="${doc.id}"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger delete-button" data-id="${doc.id}"><i class="fas fa-trash-alt"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Add event listeners to buttons
        document.querySelectorAll(".edit-button").forEach(button => {
            button.addEventListener("click", editarCliente);
        });
        document.querySelectorAll(".delete-button").forEach(button => {
            button.addEventListener("click", excluirCliente);
        });
    });
}

function editarCliente(e) {
    const id = e.currentTarget.getAttribute("data-id");
    const clienteRef = db.collection("clientes").doc(id);
    clienteRef.get().then(doc => {
        if (doc.exists) {
            const cliente = doc.data();
            Swal.fire({
                title: 'Editar Cliente',
                html:
                    `<input id="edit-nome" class="swal2-input" placeholder="Nome" value="${cliente.nome}">
                    <input id="edit-email" class="swal2-input" placeholder="E-mail" value="${cliente.email}">
                    <input id="edit-telefone" class="swal2-input" placeholder="Telefone" value="${cliente.telefone}">
                    <select id="edit-status" class="swal2-input">
                        <option value="ativo" ${cliente.status === 'ativo' ? 'selected' : ''}>Ativo</option>
                        <option value="inativo" ${cliente.status === 'inativo' ? 'selected' : ''}>Inativo</option>
                    </select>`,
                focusConfirm: false,
                preConfirm: () => {
                    const novoNome = document.getElementById('edit-nome').value;
                    const novoEmail = document.getElementById('edit-email').value;
                    const novoTelefone = document.getElementById('edit-telefone').value;
                    const novoStatus = document.getElementById('edit-status').value;
                    return { novoNome, novoEmail, novoTelefone, novoStatus };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { novoNome, novoEmail, novoTelefone, novoStatus } = result.value;
                    clienteRef.update({
                        nome: novoNome,
                        email: novoEmail,
                        telefone: novoTelefone,
                        status: novoStatus
                    }).then(() => {
                        Swal.fire("Sucesso", "Cliente atualizado com sucesso!", "success");
                    }).catch(error => {
                        Swal.fire("Erro", error.message, "error");
                    });
                }
            });
        } else {
            Swal.fire("Erro", "Cliente não encontrado.", "error");
        }
    });
}

function excluirCliente(e) {
    const id = e.currentTarget.getAttribute("data-id");
    Swal.fire({
        title: 'Confirmação',
        text: "Tem certeza que deseja excluir este cliente?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, excluir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            db.collection("clientes").doc(id).delete()
                .then(() => {
                    Swal.fire("Sucesso", "Cliente excluído com sucesso!", "success");
                })
                .catch(error => {
                    Swal.fire("Erro", error.message, "error");
                });
        }
    });
}
</script>
<?php include "footer.php"; ?>
