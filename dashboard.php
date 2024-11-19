<?php
$title = "Dashboard";
$page = "dashboard";
$includeChartJS = true;
include "header.php";
include "nav.php";
?>
<div class="container mt-4">
    <h3>Visão Geral</h3>
    <div class="row">
        <!-- Card: Total de Vendas -->
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-header">Total de Vendas</div>
                <div class="card-body">
                    <h5 class="card-title" id="total-vendas">R$ 0,00</h5>
                </div>
            </div>
        </div>
        <!-- Card: Pedidos Pendentes -->
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-header">Pedidos Pendentes</div>
                <div class="card-body">
                    <h5 class="card-title" id="pedidos-pendentes">0</h5>
                </div>
            </div>
        </div>
        <!-- Card: Estoque Total -->
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card text-white bg-success">
                <div class="card-header">Estoque Total</div>
                <div class="card-body">
                    <h5 class="card-title" id="estoque-total">0</h5>
                </div>
            </div>
        </div>
    </div>

    <h4>Gráficos de Vendas</h4>
    <canvas id="vendasChart" width="400" height="200"></canvas>
</div>

<?php include "firebase_config.php"; ?>
<script>
// Dashboard Functionality

// Logout
document.getElementById("logout-button").addEventListener("click", () => {
    auth.signOut().then(() => {
        window.location.href = "index.php";
    }).catch(error => {
        console.error("Erro ao realizar logout:", error);
        Swal.fire("Erro", "Erro ao realizar logout: " + error.message, "error");
    });
});

// Monitorar o estado de autenticação
auth.onAuthStateChanged(user => {
    if (!user) {
        window.location.href = "index.php";
    } else {
        carregarResumo();
        carregarGrafico();
    }
});

// Função para carregar o resumo geral
function carregarResumo() {
    // Carregar Total de Vendas
    db.collection('pedidos').where('status', '==', 'pago').get()
        .then(snapshot => {
            let total = 0;
            snapshot.forEach(doc => {
                const pedido = doc.data();
                total += pedido.total;
            });
            document.getElementById('total-vendas').innerText = 'R$ ' + total.toFixed(2);
        })
        .catch(error => {
            console.error("Erro ao carregar total de vendas:", error);
            Swal.fire("Erro", "Erro ao carregar total de vendas: " + error.message, "error");
        });

    // Carregar Pedidos Pendentes
    db.collection('pedidos').where('status', '==', 'pendente').get()
        .then(snapshot => {
            document.getElementById('pedidos-pendentes').innerText = snapshot.size;
        })
        .catch(error => {
            console.error("Erro ao carregar pedidos pendentes:", error);
            Swal.fire("Erro", "Erro ao carregar pedidos pendentes: " + error.message, "error");
        });

    // Carregar Estoque Total
    db.collection('produtos').get()
        .then(snapshot => {
            let estoque = 0;
            snapshot.forEach(doc => {
                const produto = doc.data();
                estoque += produto.quantidade;
            });
            document.getElementById('estoque-total').innerText = estoque;
        })
        .catch(error => {
            console.error("Erro ao carregar estoque total:", error);
            Swal.fire("Erro", "Erro ao carregar estoque total: " + error.message, "error");
        });
}

// Função para carregar o gráfico de vendas
function carregarGrafico() {
    db.collection('pedidos').where('status', '==', 'pago').get()
        .then(snapshot => {
            const vendasPorMes = {};

            snapshot.forEach(doc => {
                const pedido = doc.data();
                const data = pedido.data.toDate();
                const mes = data.getMonth() + 1; // Meses são zero-indexados
                const ano = data.getFullYear();
                const chave = mes + '/' + ano;

                if (vendasPorMes[chave]) {
                    vendasPorMes[chave] += pedido.total;
                } else {
                    vendasPorMes[chave] = pedido.total;
                }
            });

            // Preparar labels e dados para o gráfico
            const labels = Object.keys(vendasPorMes).sort((a, b) => {
                const [mesA, anoA] = a.split('/').map(Number);
                const [mesB, anoB] = b.split('/').map(Number);
                return anoA - anoB || mesA - mesB;
            });

            const data = labels.map(label => vendasPorMes[label]);

            // Configurar e renderizar o gráfico com Chart.js
            const ctx = document.getElementById('vendasChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Vendas por Mês',
                        data: data,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { 
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Total de Vendas (R$)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Mês/Ano'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Vendas Mensais'
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error("Erro ao carregar gráfico de vendas:", error);
            Swal.fire("Erro", "Erro ao carregar gráfico de vendas: " + error.message, "error");
        });
}
</script>
<?php include "footer.php"; ?>
