<?php
$title = "Produtos";
$page = "produtos";
include "header.php";
include "nav.php";
?>
<div class="container mt-4">
    <h3>Gerenciar Produtos</h3>

    <!-- Form to Add New Product -->
    <div class="card mb-3">
        <div class="card-header">Adicionar Novo Produto</div>
        <div class="card-body">
            <form id="add-product-form">
                <div class="form-group mb-3">
                    <label for="produto-nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="produto-nome" required>
                </div>
                <div class="form-group mb-3">
                    <label for="produto-preco" class="form-label">Preço</label>
                    <input type="number" step="0.01" class="form-control" id="produto-preco" required>
                </div>
                <div class="form-group mb-3">
                    <label for="produto-quantidade" class="form-label">Quantidade em Estoque</label>
                    <input type="number" class="form-control" id="produto-quantidade" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Adicionar Produto</button>
            </form>
        </div>
    </div>

    <!-- Products List -->
    <h4>Lista de Produtos</h4>
    <div class="table-responsive">
        <table class="table table-bordered" id="produtos-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Quantidade em Estoque</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- Products will be dynamically added here -->
            </tbody>
        </table>
    </div>
</div>

<?php include "firebase_config.php"; ?>
<script>
// Products Functionality
document.getElementById("logout-button").addEventListener("click", () => {
    auth.signOut().then(() => {
        window.location.href = "index.php";
    });
});

auth.onAuthStateChanged(user => {
    if (!user) {
        window.location.href = "index.php";
    } else {
        carregarProdutos();
    }
});

document.getElementById("add-product-form").addEventListener("submit", function(e) {
    e.preventDefault();
    const nome = document.getElementById("produto-nome").value;
    const preco = parseFloat(document.getElementById("produto-preco").value);
    const quantidade = parseInt(document.getElementById("produto-quantidade").value);

    db.collection("produtos").add({
        nome: nome,
        preco: preco,
        quantidade: quantidade,
        createdAt: firebase.firestore.FieldValue.serverTimestamp()
    }).then(() => {
        Swal.fire("Sucesso", "Produto adicionado com sucesso!", "success");
        document.getElementById("add-product-form").reset();
        carregarProdutos();
    }).catch(error => {
        Swal.fire("Erro", error.message, "error");
    });
});

function carregarProdutos() {
    db.collection("produtos").orderBy("nome").onSnapshot(snapshot => {
        const tbody = document.querySelector("#produtos-table tbody");
        tbody.innerHTML = "";
        snapshot.forEach(doc => {
            const produto = doc.data();
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${produto.nome}</td>
                <td>R$ ${produto.preco.toFixed(2)}</td>
                <td>${produto.quantidade}</td>
                <td>
                    <button class="btn btn-sm btn-primary edit-button" data-id="${doc.id}"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger delete-button" data-id="${doc.id}"><i class="fas fa-trash-alt"></i></button>
                </td>
            `;
            if (produto.quantidade < 5) {
                tr.classList.add("table-warning");
            }
            tbody.appendChild(tr);
        });

        // Add event listeners to buttons
        document.querySelectorAll(".edit-button").forEach(button => {
            button.addEventListener("click", editarProduto);
        });
        document.querySelectorAll(".delete-button").forEach(button => {
            button.addEventListener("click", excluirProduto);
        });
    });
}

function editarProduto(e) {
    const id = e.currentTarget.getAttribute("data-id");
    const produtoRef = db.collection("produtos").doc(id);
    produtoRef.get().then(doc => {
        if (doc.exists) {
            const produto = doc.data();
            Swal.fire({
                title: 'Editar Produto',
                html:
                    `<input id="edit-nome" class="swal2-input" placeholder="Nome" value="${produto.nome}">
                    <input id="edit-preco" class="swal2-input" placeholder="Preço" type="number" step="0.01" value="${produto.preco}">
                    <input id="edit-quantidade" class="swal2-input" placeholder="Quantidade" type="number" value="${produto.quantidade}">`,
                focusConfirm: false,
                preConfirm: () => {
                    const novoNome = document.getElementById('edit-nome').value;
                    const novoPreco = parseFloat(document.getElementById('edit-preco').value);
                    const novaQuantidade = parseInt(document.getElementById('edit-quantidade').value);
                    return { novoNome, novoPreco, novaQuantidade };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { novoNome, novoPreco, novaQuantidade } = result.value;
                    produtoRef.update({
                        nome: novoNome,
                        preco: novoPreco,
                        quantidade: novaQuantidade
                    }).then(() => {
                        Swal.fire("Sucesso", "Produto atualizado com sucesso!", "success");
                    }).catch(error => {
                        Swal.fire("Erro", error.message, "error");
                    });
                }
            });
        } else {
            Swal.fire("Erro", "Produto não encontrado.", "error");
        }
    });
}

function excluirProduto(e) {
    const id = e.currentTarget.getAttribute("data-id");
    Swal.fire({
        title: 'Confirmação',
        text: "Tem certeza que deseja excluir este produto?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, excluir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            db.collection("produtos").doc(id).delete()
                .then(() => {
                    Swal.fire("Sucesso", "Produto excluído com sucesso!", "success");
                })
                .catch(error => {
                    Swal.fire("Erro", error.message, "error");
                });
        }
    });
}
</script>
<?php include "footer.php"; ?>
