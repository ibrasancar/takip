<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="description" content="Responsive Admin Dashboard Template" />
  <meta name="keywords" content="admin,dashboard" />
  <meta name="author" content="stacks" />
  <link rel="shortcut icon" href="<?= site_url('favicon.png') ?>" />
  <!-- The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags -->

  <!-- Title -->
  <title><?= $page_title . ' - ' . getenv('app.title') ?></title>

  <!-- Styles -->
  <link rel="preconnect" href="https://fonts.gstatic.com" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Two+Tone|Material+Icons+Round|Material+Icons+Sharp" rel="stylesheet" />
  <link href="<?= site_url('/assets/plugins/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet" />
  <link href="<?= site_url('/assets/plugins/perfectscroll/perfect-scrollbar.css') ?>" rel="stylesheet" />
  <link href="<?= site_url('/assets/plugins/pace/pace.css') ?>" rel="stylesheet" />
  <link href="<?= site_url('/assets/css/custom.css') ?>" rel="stylesheet" />
  <?= $this->renderSection('styles') ?>

  <!-- Theme Styles -->
  <link href="<?= site_url('/assets/css/main.min.css') ?>" rel="stylesheet" />
  <link href="<?= site_url('/assets/css/custom.css') ?>" rel="stylesheet" />

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>