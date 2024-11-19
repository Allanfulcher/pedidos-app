<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?> - Controle de Vendas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-firestore-compat.js"></script>
    <!-- Chart.js for graphs -->
    <?php if (isset($includeChartJS)) { ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php } ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS for mobile responsiveness -->
    <style>
        body {
            padding-bottom: 60px;
        }
        .navbar-nav .nav-item .nav-link {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1rem;
            }
        }
    </style>
    <?php if (isset($additionalHeadContent)) echo $additionalHeadContent; ?>
</head>
<body>
