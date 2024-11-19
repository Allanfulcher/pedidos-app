<?php
$title = "Pedidos";
$page = "pedidos";
include "header.php";
include "nav.php";
?>
<div class="container mt-4">
    <h3 class="mb-4">Gerenciar Pedidos</h3>

    <!-- Form to Create New Order -->
    <div class="card mb-4">
        <div class="card-header">Criar Novo Pedido</div>
        <div class="card-body">
            <form id="add-pedido-form">
                <div class="form-group mb-3">
                    <label for="pedido-cliente" class="form-label">Cliente</label>
                    <select class="form-select" id="pedido-cliente" required>
                        <option value="">Selecione um cliente</option>
                        <!-- Clients will be loaded here -->
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="pedido-produto" class="form-label">Produto</label>
                    <select class="form-select" id="pedido-produto">
                        <option value="">Selecione um produto</option>
                        <!-- Products will be loaded here -->
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="pedido-quantidade" class="form-label">Quantidade</label>
                    <input type="number" class="form-control" id="pedido-quantidade" min="1" value="1">
                </div>
                <button type="button" class="btn btn-secondary mb-3 w-100" id="add-item-button">Adicionar Item</button>

                <h5>Itens do Pedido</h5>
                <ul class="list-group mb-3" id="itens-pedido">
                    <!-- Items will be added here -->
                </ul>

                <h5>Total: R$ <span id="total-pedido">0.00</span></h5>
                <button type="submit" class="btn btn-success w-100">Finalizar Pedido</button>
            </form>
        </div>
    </div>

    <!-- Orders List -->
    <h4 class="mb-3">Lista de Pedidos</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="pedidos-table">
            <thead class="table-light">
                <tr>
                    <th>Data</th>
                    <th>Cliente</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- Orders will be dynamically added here -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Order Details -->
<div class="modal fade" id="pedidoModal" tabindex="-1" aria-labelledby="pedidoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="pedidoModalLabel">Detalhes do Pedido</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="pedido-modal-body">
        <!-- Order details will be loaded here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        <button type="button" class="btn btn-primary" id="imprimir-pedido-button">Imprimir</button>
        <button type="button" class="btn btn-danger" id="cancelar-pedido-button">Cancelar Pedido</button>
        <button type="button" class="btn btn-success" id="editar-pedido-button">Editar Pedido</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Editing Order -->
<div class="modal fade" id="editarPedidoModal" tabindex="-1" aria-labelledby="editarPedidoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="editarPedidoModalLabel">Editar Pedido</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editar-pedido-form">
            <div class="form-group mb-3">
                <label for="editar-pedido-status" class="form-label">Status do Pedido</label>
                <select class="form-select" id="editar-pedido-status" required>
                    <option value="pendente">Pendente</option>
                    <option value="pago">Pago</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
            <h5>Itens do Pedido</h5>
            <ul class="list-group mb-3" id="editar-itens-pedido">
                <!-- Editable items will be loaded here -->
            </ul>
            <button type="button" class="btn btn-secondary mb-3 w-100" id="editar-add-item-button">Adicionar Item</button>
            <h5>Total: R$ <span id="editar-total-pedido">0.00</span></h5>
            <button type="submit" class="btn btn-success w-100">Salvar Alterações</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include "firebase_config.php"; ?>
<script>
// Orders Functionality
document.getElementById("logout-button").addEventListener("click", () => {
    auth.signOut().then(() => {
        window.location.href = "index.php";
    });
});

let itensDoPedido = [];

auth.onAuthStateChanged(user => {
    if (!user) {
        window.location.href = "index.php";
    } else {
        carregarClientes();
        carregarProdutos();
        carregarPedidos();
    }
});

// Carregar Clientes
function carregarClientes() {
    db.collection("clientes").where("status", "==", "ativo").get()
        .then(snapshot => {
            const select = document.getElementById("pedido-cliente");
            select.innerHTML = `<option value="">Selecione um cliente</option>`; // Resetar lista
            snapshot.forEach(doc => {
                const cliente = doc.data();
                const option = document.createElement("option");
                option.value = doc.id;
                option.text = cliente.nome;
                select.appendChild(option);
            });
            console.log("Clientes carregados:", snapshot.size); // Verifique no console
        })
        .catch(error => {
            console.error("Erro ao carregar clientes:", error);
            Swal.fire("Erro", "Erro ao carregar clientes: " + error.message, "error");
        });
}

// Carregar Produtos
function carregarProdutos() {
    db.collection("produtos").get()
        .then(snapshot => {
            const select = document.getElementById("pedido-produto");
            snapshot.forEach(doc => {
                const produto = doc.data();
                const option = document.createElement("option");
                option.value = doc.id;
                option.text = `${produto.nome} - R$ ${produto.preco.toFixed(2)}`;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error("Erro ao carregar produtos:", error);
            Swal.fire("Erro", "Erro ao carregar produtos: " + error.message, "error");
        });
}

// Adicionar Item ao Pedido
document.getElementById("add-item-button").addEventListener("click", () => {
    const produtoId = document.getElementById("pedido-produto").value;
    const quantidade = parseInt(document.getElementById("pedido-quantidade").value);
    if (!produtoId || quantidade < 1) {
        Swal.fire("Erro", "Selecione um produto e uma quantidade válida.", "error");
        return;
    }
    db.collection("produtos").doc(produtoId).get()
        .then(doc => {
            if (doc.exists) {
                const produto = doc.data();
                itensDoPedido.push({
                    produtoId: produtoId,
                    nome: produto.nome,
                    preco: produto.preco,
                    quantidade: quantidade
                });
                atualizarListaItens();
            } else {
                Swal.fire("Erro", "Produto não encontrado.", "error");
            }
        })
        .catch(error => {
            console.error("Erro ao obter produto:", error);
            Swal.fire("Erro", "Erro ao obter produto: " + error.message, "error");
        });
});

// Atualizar Lista de Itens no Pedido
function atualizarListaItens() {
    const lista = document.getElementById("itens-pedido");
    lista.innerHTML = "";
    let total = 0;
    itensDoPedido.forEach((item, index) => {
        const li = document.createElement("li");
        li.className = "list-group-item d-flex justify-content-between align-items-center flex-wrap";
        li.innerHTML = `
            <div>
                <strong>${item.nome}</strong><br>
                R$ ${item.preco.toFixed(2)} x ${item.quantidade}
            </div>
            <button class="btn btn-sm btn-danger mt-2 mt-md-0" data-index="${index}"><i class="fas fa-trash-alt"></i></button>
        `;
        lista.appendChild(li);
        total += item.preco * item.quantidade;
    });
    document.getElementById("total-pedido").innerText = total.toFixed(2);

    // Adicionar event listeners aos botões de remover
    document.querySelectorAll("#itens-pedido .btn-danger").forEach(button => {
        button.addEventListener("click", removerItem);
    });
}

// Remover Item do Pedido
function removerItem(e) {
    const index = e.currentTarget.getAttribute("data-index");
    itensDoPedido.splice(index, 1);
    atualizarListaItens();
}

// Finalizar Pedido
document.getElementById("add-pedido-form").addEventListener("submit", function(e) {
    e.preventDefault();
    const clienteId = document.getElementById("pedido-cliente").value;
    if (!clienteId) {
        Swal.fire("Erro", "Selecione um cliente.", "error");
        return;
    }
    if (itensDoPedido.length === 0) {
        Swal.fire("Erro", "Adicione pelo menos um item ao pedido.", "error");
        return;
    }

    const total = itensDoPedido.reduce((acc, item) => acc + (item.preco * item.quantidade), 0);

    db.collection("pedidos").add({
        clienteId: clienteId,
        itens: itensDoPedido,
        total: total,
        status: "pendente",
        data: firebase.firestore.FieldValue.serverTimestamp()
    }).then(() => {
        Swal.fire("Sucesso", "Pedido criado com sucesso!", "success");
        document.getElementById("add-pedido-form").reset();
        itensDoPedido = [];
        atualizarListaItens();
        atualizarEstoque();
    }).catch(error => {
        Swal.fire("Erro", error.message, "error");
    });
});

// Atualizar Estoque
function atualizarEstoque() {
    itensDoPedido.forEach(item => {
        const produtoRef = db.collection("produtos").doc(item.produtoId);
        produtoRef.update({
            quantidade: firebase.firestore.FieldValue.increment(-item.quantidade)
        }).catch(error => {
            console.error("Erro ao atualizar estoque:", error);
            Swal.fire("Erro", "Erro ao atualizar estoque: " + error.message, "error");
        });
    });
}

// Carregar Pedidos
function carregarPedidos() {
    db.collection("pedidos").orderBy("data", "desc").onSnapshot(snapshot => {
        const tbody = document.querySelector("#pedidos-table tbody");
        tbody.innerHTML = "";
        snapshot.forEach(doc => {
            const pedido = doc.data();
            const tr = document.createElement("tr");
            const data = pedido.data ? pedido.data.toDate().toLocaleString() : "Sem data";

            db.collection("clientes").doc(pedido.clienteId).get().then(clienteDoc => {
                const clienteNome = clienteDoc.exists ? clienteDoc.data().nome : "Desconhecido";
                tr.innerHTML = `
                    <td>${data}</td>
                    <td>${clienteNome}</td>
                    <td>${pedido.status.charAt(0).toUpperCase() + pedido.status.slice(1)}</td>
                    <td>R$ ${pedido.total.toFixed(2)}</td>
                    <td>
                        <button class="btn btn-sm btn-info detalhes-pedido-button" data-id="${doc.id}" title="Ver Detalhes"><i class="fas fa-eye"></i></button>
                    </td>
                `;
                tbody.appendChild(tr);

                // Adicionar event listener ao botão de detalhes
                tr.querySelector(".detalhes-pedido-button").addEventListener("click", () => {
                    mostrarDetalhesPedido(doc.id);
                });
            }).catch(error => {
                console.error("Erro ao obter cliente:", error);
                Swal.fire("Erro", "Erro ao obter cliente: " + error.message, "error");
            });
        });
    }, error => {
        console.error("Erro ao carregar pedidos:", error);
        Swal.fire("Erro", "Erro ao carregar pedidos: " + error.message, "error");
    });
}

// Mostrar Detalhes do Pedido no Modal
function mostrarDetalhesPedido(pedidoId) {
    const pedidoRef = db.collection("pedidos").doc(pedidoId);
    pedidoRef.get().then(doc => {
        if (doc.exists) {
            const pedido = doc.data();
            let modalBody = `<p><strong>Data:</strong> ${pedido.data ? pedido.data.toDate().toLocaleString() : "Sem data"}</p>`;
            db.collection("clientes").doc(pedido.clienteId).get().then(clienteDoc => {
                const clienteNome = clienteDoc.exists ? clienteDoc.data().nome : "Desconhecido";
                modalBody += `<p><strong>Cliente:</strong> ${clienteNome}</p>`;
                modalBody += `<p><strong>Status:</strong> ${pedido.status.charAt(0).toUpperCase() + pedido.status.slice(1)}</p>`;
                modalBody += `<h5>Itens do Pedido:</h5>`;
                modalBody += `<ul class="list-group mb-3">`;
                pedido.itens.forEach(item => {
                    modalBody += `<li class="list-group-item d-flex justify-content-between align-items-center">
                        ${item.nome} - R$ ${item.preco.toFixed(2)} x ${item.quantidade}
                        <span>R$ ${(item.preco * item.quantidade).toFixed(2)}</span>
                    </li>`;
                });
                modalBody += `</ul>`;
                modalBody += `<h5>Total: R$ ${pedido.total.toFixed(2)}</h5>`;
                document.getElementById("pedido-modal-body").innerHTML = modalBody;

                // Definir ações dos botões do modal
                document.getElementById("imprimir-pedido-button").onclick = () => imprimirPedido(pedidoId);
                document.getElementById("cancelar-pedido-button").onclick = () => cancelarPedido(pedidoId);
                document.getElementById("editar-pedido-button").onclick = () => editarPedido(pedidoId);

                // Mostrar o modal
                const pedidoModal = new bootstrap.Modal(document.getElementById('pedidoModal'));
                pedidoModal.show();
            }).catch(error => {
                console.error("Erro ao obter cliente:", error);
                Swal.fire("Erro", "Erro ao obter cliente: " + error.message, "error");
            });
        } else {
            Swal.fire("Erro", "Pedido não encontrado.", "error");
        }
    }).catch(error => {
        console.error("Erro ao obter pedido:", error);
        Swal.fire("Erro", "Erro ao obter pedido: " + error.message, "error");
    });
}

// Imprimir Pedido
function imprimirPedido(pedidoId) {
    const pedidoRef = db.collection("pedidos").doc(pedidoId);
    pedidoRef.get().then(doc => {
        if (doc.exists) {
            const pedido = doc.data();
            db.collection("clientes").doc(pedido.clienteId).get().then(clienteDoc => {
                const clienteNome = clienteDoc.exists ? clienteDoc.data().nome : "Desconhecido";

                let printContent = `
                    <html>
                    <head>
                        <title>Imprimir Pedido</title>
                        <style>
                            body { font-family: Arial, sans-serif; padding: 20px; }
                            h2 { text-align: center; }
                            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                            th, td { border: 1px solid #000; padding: 8px; text-align: left; }
                            th { background-color: #f2f2f2; }
                            .total { text-align: right; font-weight: bold; }
                        </style>
                    </head>
                    <body>
                        <h2>Detalhes do Pedido</h2>
                        <p><strong>Data:</strong> ${pedido.data ? pedido.data.toDate().toLocaleString() : "Sem data"}</p>
                        <p><strong>Cliente:</strong> ${clienteNome}</p>
                        <p><strong>Status:</strong> ${pedido.status.charAt(0).toUpperCase() + pedido.status.slice(1)}</p>
                        <table>
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Preço Unitário</th>
                                    <th>Quantidade</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                pedido.itens.forEach(item => {
                    printContent += `
                        <tr>
                            <td>${item.nome}</td>
                            <td>R$ ${item.preco.toFixed(2)}</td>
                            <td>${item.quantidade}</td>
                            <td>R$ ${(item.preco * item.quantidade).toFixed(2)}</td>
                        </tr>
                    `;
                });

                printContent += `
                            </tbody>
                        </table>
                        <p class="total">Total: R$ ${pedido.total.toFixed(2)}</p>
                    </body>
                    </html>
                `;

                const printWindow = window.open('', '', 'height=600,width=800');
                printWindow.document.write(printContent);
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            }).catch(error => {
                console.error("Erro ao obter cliente para impressão:", error);
                Swal.fire("Erro", "Erro ao obter cliente para impressão: " + error.message, "error");
            });
        } else {
            Swal.fire("Erro", "Pedido não encontrado para impressão.", "error");
        }
    }).catch(error => {
        console.error("Erro ao obter pedido para impressão:", error);
        Swal.fire("Erro", "Erro ao obter pedido para impressão: " + error.message, "error");
    });
}

// Cancelar Pedido
function cancelarPedido(pedidoId) {
    const pedidoRef = db.collection("pedidos").doc(pedidoId);
    pedidoRef.get().then(doc => {
        if (doc.exists) {
            const pedido = doc.data();
            if (pedido.status !== "cancelado") {
                Swal.fire({
                    title: 'Confirmação',
                    text: "Tem certeza que deseja cancelar este pedido?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, cancelar',
                    cancelButtonText: 'Não'
                }).then((result) => {
                    if (result.isConfirmed) {
                        pedidoRef.update({
                            status: "cancelado"
                        }).then(() => {
                            Swal.fire("Sucesso", "Pedido cancelado com sucesso!", "success");
                            // Reverter estoque
                            pedido.itens.forEach(item => {
                                const produtoRef = db.collection("produtos").doc(item.produtoId);
                                produtoRef.update({
                                    quantidade: firebase.firestore.FieldValue.increment(item.quantidade)
                                }).catch(error => {
                                    console.error("Erro ao reverter estoque:", error);
                                    Swal.fire("Erro", "Erro ao reverter estoque: " + error.message, "error");
                                });
                            });
                        }).catch(error => {
                            Swal.fire("Erro", error.message, "error");
                        });
                    }
                });
            } else {
                Swal.fire("Info", "Pedido já está cancelado.", "info");
            }
        } else {
            Swal.fire("Erro", "Pedido não encontrado.", "error");
        }
    }).catch(error => {
        console.error("Erro ao obter pedido para cancelamento:", error);
        Swal.fire("Erro", "Erro ao obter pedido para cancelamento: " + error.message, "error");
    });
}

// Editar Pedido
function editarPedido(pedidoId) {
    const pedidoRef = db.collection("pedidos").doc(pedidoId);
    pedidoRef.get().then(doc => {
        if (doc.exists) {
            const pedido = doc.data();
            let editarItens = [...pedido.itens]; // Clone the itens array

            // Carregar dados no formulário de edição
            document.getElementById("editar-pedido-status").value = pedido.status;
            const editarItensLista = document.getElementById("editar-itens-pedido");
            editarItensLista.innerHTML = "";
            let editarTotal = 0;

            editarItens.forEach((item, index) => {
                const li = document.createElement("li");
                li.className = "list-group-item d-flex justify-content-between align-items-center flex-wrap";
                li.innerHTML = `
                    <div class="w-100 w-md-75">
                        <strong>${item.nome}</strong><br>
                        R$ ${item.preco.toFixed(2)} x 
                        <input type="number" class="form-control form-control-sm mt-1" id="editar-quantidade-${index}" value="${item.quantidade}" min="1" style="width: 80px; display: inline-block;">
                    </div>
                    <button class="btn btn-sm btn-danger mt-2 mt-md-0" data-index="${index}"><i class="fas fa-trash-alt"></i></button>
                `;
                editarItensLista.appendChild(li);
                editarTotal += item.preco * item.quantidade;
            });

            document.getElementById("editar-total-pedido").innerText = editarTotal.toFixed(2);

            // Adicionar event listeners aos botões de remover
            document.querySelectorAll("#editar-itens-pedido .btn-danger").forEach(button => {
                button.addEventListener("click", function(e) {
                    const index = e.currentTarget.getAttribute("data-index");
                    editarItens.splice(index, 1);
                    atualizarEditarItens();
                });
            });

            // Função para atualizar a lista de itens no modal de edição
            function atualizarEditarItens() {
                
                editarItensLista.innerHTML = "";
                editarTotal = 0;
                editarItens.forEach((item, index) => {
                    const li = document.createElement("li");
                    li.className = "list-group-item d-flex justify-content-between align-items-center flex-wrap";
                    li.innerHTML = `
                        <div class="w-100 w-md-75">
                            <strong>${item.nome}</strong><br>
                            R$ ${item.preco.toFixed(2)} x 
                            <input type="number" class="form-control form-control-sm mt-1" id="editar-quantidade-${index}" value="${item.quantidade}" min="1" style="width: 80px; display: inline-block;">
                        </div>
                        <button class="btn btn-sm btn-danger mt-2 mt-md-0" data-index="${index}"><i class="fas fa-trash-alt"></i></button>
                    `;
                    editarItensLista.appendChild(li);
                    editarTotal += item.preco * item.quantidade;
                });
                document.getElementById("editar-total-pedido").innerText = editarTotal.toFixed(2);

                // Re-adicionar event listeners aos botões de remover
                document.querySelectorAll("#editar-itens-pedido .btn-danger").forEach(button => {
                    button.addEventListener("click", function(e) {
                        const index = e.currentTarget.getAttribute("data-index");
                        editarItens.splice(index, 1);
                        atualizarEditarItens();
                    });
                });
            }

            // Adicionar item no modal de edição
            document.getElementById("editar-add-item-button").onclick = () => {
                Swal.fire({
                    title: 'Adicionar Item',
                    html:
                        `<select id="editar-novo-produto" class="swal2-select">
                            <option value="">Selecione um produto</option>
                        </select>
                        <input id="editar-nova-quantidade" class="swal2-input" placeholder="Quantidade" type="number" min="1" value="1">`,
                    preConfirm: () => {
                        const produtoId = document.getElementById('editar-novo-produto').value;
                        const quantidade = parseInt(document.getElementById('editar-nova-quantidade').value);
                        return { produtoId, quantidade };
                    },
                    didOpen: () => {
                        // Carregar produtos no select
                        db.collection("produtos").get().then(snapshot => {
                            const select = document.getElementById('editar-novo-produto');
                            snapshot.forEach(doc => {
                                const produto = doc.data();
                                const option = document.createElement("option");
                                option.value = doc.id;
                                option.text = `${produto.nome} - R$ ${produto.preco.toFixed(2)}`;
                                select.appendChild(option);
                            });
                        }).catch(error => {
                            console.error("Erro ao carregar produtos para edição:", error);
                            Swal.fire("Erro", "Erro ao carregar produtos: " + error.message, "error");
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const { produtoId, quantidade } = result.value;
                        if (!produtoId || quantidade < 1) {
                            Swal.fire("Erro", "Selecione um produto e uma quantidade válida.", "error");
                            return;
                        }
                        db.collection("produtos").doc(produtoId).get()
                            .then(doc => {
                                if (doc.exists) {
                                    const produto = doc.data();
                                    editarItens.push({
                                        produtoId: produtoId,
                                        nome: produto.nome,
                                        preco: produto.preco,
                                        quantidade: quantidade
                                    });
                                    atualizarEditarItens();
                                } else {
                                    Swal.fire("Erro", "Produto não encontrado.", "error");
                                }
                            })
                            .catch(error => {
                                console.error("Erro ao obter produto para edição:", error);
                                Swal.fire("Erro", "Erro ao obter produto: " + error.message, "error");
                            });
                    }
                });
            };

            // Mostrar o modal de edição
            const editarPedidoModal = new bootstrap.Modal(document.getElementById('editarPedidoModal'));
            editarPedidoModal.show();

            // Salvar Alterações
            document.getElementById("editar-pedido-form").onsubmit = function(e) {
                e.preventDefault();
                const novoStatus = document.getElementById("editar-pedido-status").value;
                const novosItens = editarItens.map((item, index) => {
                    const novaQuantidade = parseInt(document.getElementById(`editar-quantidade-${index}`).value);
                    return {
                        produtoId: item.produtoId,
                        nome: item.nome,
                        preco: item.preco,
                        quantidade: novaQuantidade
                    };
                });

                const novoTotal = novosItens.reduce((acc, item) => acc + (item.preco * item.quantidade), 0);

                // Atualizar pedido no Firestore
                pedidoRef.update({
                    status: novoStatus,
                    itens: novosItens,
                    total: novoTotal
                }).then(() => {
                    Swal.fire("Sucesso", "Pedido atualizado com sucesso!", "success");
                    editarPedidoModal.hide();
                    // Atualizar estoque
                    ajustarEstoqueParaEdicao(pedido, novosItens);
                }).catch(error => {
                    Swal.fire("Erro", error.message, "error");
                });
            };
        } else {
            Swal.fire("Erro", "Pedido não encontrado para edição.", "error");
        }
    }).catch(error => {
        console.error("Erro ao obter pedido para edição:", error);
        Swal.fire("Erro", "Erro ao obter pedido para edição: " + error.message, "error");
    });
}

// Ajustar Estoque após Edição
function ajustarEstoqueParaEdicao(pedidoOriginal, novosItens) {
    // Calcular diferenças de quantidade para cada produto
    const estoquePromises = novosItens.map(novoItem => {
        const originalItem = pedidoOriginal.itens.find(item => item.produtoId === novoItem.produtoId);
        const diferenca = (novoItem.quantidade || 0) - (originalItem ? originalItem.quantidade : 0);
        if (diferenca !== 0) {
            const produtoRef = db.collection("produtos").doc(novoItem.produtoId);
            return produtoRef.update({
                quantidade: firebase.firestore.FieldValue.increment(diferenca)
            }).catch(error => {
                console.error("Erro ao ajustar estoque após edição:", error);
                Swal.fire("Erro", "Erro ao ajustar estoque: " + error.message, "error");
            });
        }
    });

    Promise.all(estoquePromises).then(() => {
        carregarPedidos(); // Recarregar a lista de pedidos
    });
}

// Imprimir Pedido
function imprimirPedido(pedidoId) {
    const pedidoRef = db.collection("pedidos").doc(pedidoId);
    pedidoRef.get().then(doc => {
        if (doc.exists) {
            const pedido = doc.data();
            db.collection("clientes").doc(pedido.clienteId).get().then(clienteDoc => {
                const clienteNome = clienteDoc.exists ? clienteDoc.data().nome : "Desconhecido";

                let printContent = `
                    <html>
                    <head>
                        <title>Imprimir Pedido</title>
                        <style>
                            body { font-family: Arial, sans-serif; padding: 20px; }
                            h2 { text-align: center; }
                            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                            th, td { border: 1px solid #000; padding: 8px; text-align: left; }
                            th { background-color: #f2f2f2; }
                            .total { text-align: right; font-weight: bold; }
                        </style>
                    </head>
                    <body>
                        <h2>Detalhes do Pedido</h2>
                        <p><strong>Data:</strong> ${pedido.data ? pedido.data.toDate().toLocaleString() : "Sem data"}</p>
                        <p><strong>Cliente:</strong> ${clienteNome}</p>
                        <p><strong>Status:</strong> ${pedido.status.charAt(0).toUpperCase() + pedido.status.slice(1)}</p>
                        <table>
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Preço Unitário</th>
                                    <th>Quantidade</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                pedido.itens.forEach(item => {
                    printContent += `
                        <tr>
                            <td>${item.nome}</td>
                            <td>R$ ${item.preco.toFixed(2)}</td>
                            <td>${item.quantidade}</td>
                            <td>R$ ${(item.preco * item.quantidade).toFixed(2)}</td>
                        </tr>
                    `;
                });

                printContent += `
                            </tbody>
                        </table>
                        <p class="total">Total: R$ ${pedido.total.toFixed(2)}</p>
                    </body>
                    </html>
                `;

                const printWindow = window.open('', '', 'height=600,width=800');
                printWindow.document.write(printContent);
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            }).catch(error => {
                console.error("Erro ao obter cliente para impressão:", error);
                Swal.fire("Erro", "Erro ao obter cliente para impressão: " + error.message, "error");
            });
        } else {
            Swal.fire("Erro", "Pedido não encontrado para impressão.", "error");
        }
    }).catch(error => {
        console.error("Erro ao obter pedido para impressão:", error);
        Swal.fire("Erro", "Erro ao obter pedido para impressão: " + error.message, "error");
    });
}
</script>
<?php include "footer.php"; ?>
