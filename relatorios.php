<?php
$title = "Relatórios";
$page = "relatorios";
$includeChartJS = true;
include "header.php";
include "nav.php";
?>
<div class="container mt-4">
    <h3>Relatórios</h3>

    <div class="card mb-3">
        <div class="card-header">Filtros</div>
        <div class="card-body">
            <form id="filtros-form" class="row g-3">
                <div class="col-md-4">
                    <label for="filtro-inicio" class="form-label">Data Início</label>
                    <input type="date" class="form-control" id="filtro-inicio" required>
                </div>
                <div class="col-md-4">
                    <label for="filtro-fim" class="form-label">Data Fim</label>
                    <input type="date" class="form-control" id="filtro-fim" required>
                </div>
                <div class="col-md-4 align-self-end">
                    <button type="submit" class="btn btn-primary w-100">Aplicar Filtros</button>
                </div>
            </form>
        </div>
    </div>

    <h4>Total de Vendas: R$ <span id="total-vendas">0.00</span></h4>
    <h4>Clientes Mais Ativos</h4>
    <div class="table-responsive">
        <table class="table table-bordered" id="clientes-ativos-table">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Quantidade de Pedidos</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be added here -->
            </tbody>
        </table>
    </div>

    <h4>Produtos Mais Vendidos</h4>
    <div class="table-responsive">
        <table class="table table-bordered" id="produtos-mais-vendidos-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade Vendida</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be added here -->
            </tbody>
        </table>
    </div>

    <h4>Gráfico de Vendas por Período</h4>
    <canvas id="vendasChart" width="400" height="200"></canvas>
</div>

<?php include "firebase_config.php"; ?>
<script>
// Reports Functionality
document.getElementById("logout-button").addEventListener("click", () => {
    auth.signOut().then(() => {
        window.location.href = "index.php";
    });
});

auth.onAuthStateChanged(user => {
    if (!user) {
        window.location.href = "index.php";
    }
});

document.getElementById("filtros-form").addEventListener("submit", function(e) {
    e.preventDefault();
    const inicio = document.getElementById("filtro-inicio").value;
    const fim = document.getElementById("filtro-fim").value;
    if (new Date(inicio) > new Date(fim)) {
        Swal.fire("Erro", "Data início não pode ser maior que data fim.", "error");
        return;
    }
    gerarRelatorios(inicio, fim);
});

function gerarRelatorios(inicio, fim) {
    // Implement report generation functionality
    Swal.fire("Info", "Funcionalidade de relatórios em desenvolvimento.", "info");
}
</script>
<?php include "footer.php"; ?>
