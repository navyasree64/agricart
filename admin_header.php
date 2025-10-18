<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?= isset($page_title) ? $page_title : 'Dashboard' ?></title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f6fa;
            color: #2c3e50;
        }
        
        /* Admin Sidebar */
        .admin-sidebar {
            background: #2c3e50;
            color: white;
            padding: 20px 0;
        }
        
        .admin-sidebar .logo {
            padding: 0 20px;
            margin-bottom: 30px;
        }
        
        .admin-sidebar .logo h2 {
            font-size: 24px;
            font-weight: 600;
        }
        
        .admin-sidebar .nav-menu {
            list-style: none;
        }
        
        .admin-sidebar .nav-menu li {
            margin-bottom: 5px;
        }
        
        .admin-sidebar .nav-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .admin-sidebar .nav-menu a:hover,
        .admin-sidebar .nav-menu a.active {
            background: #34495e;
            color: #fff;
        }
        
        .admin-sidebar .nav-menu i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Notifications */
        .notification {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-weight: 500;
        }
        
        .notification.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .notification.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .notification.warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
    </style>
</head>
<body> 