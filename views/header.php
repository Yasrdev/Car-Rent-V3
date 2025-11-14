<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BARIZ CARS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <!-- Favicon for modern browsers -->
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/fav-icon.png">
    <link rel="stylesheet" href="././public/css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-MrOqjI1F5K3kW8pQqTjGZpYz3rj4Zp3Hk8D5zHkgh3FZ8y5gTjZo3jK6F8F9K3G5G" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body>
<!-- Loader -->
  <div id="loader">
    <img src="././public/images/bariz-logo.png" alt="Logo">
    <div class="line"></div>
    <h3>Bienvenue sur mon site</h3>
  </div>

<div id="content">
<!-- Navbar desktop (visible seulement sur desktop) -->
<nav class="navbar" id="navbar">
  <a href="index.html">
    <img src="././public/images/bariz-logo.png" alt="Logo BARIZ CARS" id="logo">
  </a>
  <button class="burger" id="burger">
    <i class="fas fa-bars"></i>
  </button>
  <ul class="menu" id="menu">
    <li><a href="index.php?page=home">Accueil</a></li>
    <li><a href="index.php?page=Voitures">Voitures</a></li>
    <li><a href="index.php?page=A-propos">À propos</a></li>
    <li><a href="index.php?page=home#Contact" id="goToContact">Contact</a></li>
  </ul>
</nav>
<div class="fullscreen-menu" id="fullscreenMenu">
  <ul>
    <li><a href="index.html">Accueil</a></li>
    <li><a href="cars.html">Voitures</a></li>
    <li><a href="about.html">À propos</a></li>
    <li><a href="#Contact">Contact</a></li>
  </ul>
  <div class="info-nav">
    <i class="fa-brands fa-square-instagram"></i>
    <i class="fas fa-phone"></i>
    <i class="fas fa-envelope"></i>
  </div>
</div>