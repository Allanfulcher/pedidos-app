<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">Controle de Vendas</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Alternar navegação">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse mt-2 mt-lg-0" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item <?php if ($page == "clientes") echo "active"; ?>"><a class="nav-link" href="clientes.php">Clientes</a></li>
                <li class="nav-item <?php if ($page == "produtos") echo "active"; ?>"><a class="nav-link" href="produtos.php">Produtos</a></li>
                <li class="nav-item <?php if ($page == "pedidos") echo "active"; ?>"><a class="nav-link" href="pedidos.php">Pedidos</a></li>
                <li class="nav-item <?php if ($page == "relatorios") echo "active"; ?>"><a class="nav-link" href="relatorios.php">Relatórios</a></li>
                <li class="nav-item <?php if ($page == "configuracoes") echo "active"; ?>"><a class="nav-link" href="configuracoes.php">Configurações</a></li>
            </ul>
            <button id="logout-button" class="btn btn-outline-danger">Logout</button>
        </div>
    </div>
</nav>
