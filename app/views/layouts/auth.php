<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WS-ROOMS | Autenticación</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/public/css/style.css">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: url('/public/assets/images/fondo.png') center center / cover no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .auth-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 0;
        }

        .auth-container { 
            position: relative;
            z-index: 1;
            width: 100%;
            padding: 2rem 0;
        }
        
        .auth-card { 
            border: none; 
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); 
            background: #ffffff;
            overflow: hidden; 
        }

        /* Variables de colores para el rediseño */
        :root {
            --brand-red: #cc0000;
            --brand-red-hover: #a30000;
        }

        .text-brand {
            color: var(--brand-red);
        }

        .btn-brand {
            background-color: var(--brand-red);
            border-color: var(--brand-red);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-brand:hover {
            background-color: var(--brand-red-hover);
            border-color: var(--brand-red-hover);
            color: white;
        }

        .form-floating > label {
            color: #6c757d;
        }

        .form-control:focus {
            border-color: var(--brand-red);
            box-shadow: 0 0 0 0.25rem rgba(204, 0, 0, 0.25);
        }
        
        .form-select:focus {
            border-color: var(--brand-red);
            box-shadow: 0 0 0 0.25rem rgba(204, 0, 0, 0.25);
        }
    </style>
</head>
<body>
    <div class="auth-overlay"></div>
    <div class="container auth-container">
        <div class="row w-100 justify-content-center">
            <div class="col-md-10 col-lg-8 col-xl-7">
                <?php echo $content; ?>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
