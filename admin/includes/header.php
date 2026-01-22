<?php
// admin/includes/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xcelrent | Admin Command Center</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/style.css?v=<?php echo time(); ?>">

    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        /* Konting fallback para hindi 'naked' ang page habang naglo-load */
        body { background-color: #f4f4f4; margin: 0; }
    </style>
</head>
<body>
    <div class="app-container">