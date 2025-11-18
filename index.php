<?php 
include('./views/header.php');

$page = isset($_GET['page']) ? $_GET['page'] : '';

?>


<?php if ($page == '') : ?>
    <?php
require_once './config/db-config.php';
require_once './models/Car.php';
require_once './models/CarBrand.php';
require_once './models/CarCategory.php';


    // Initialiser les modèles
    $carModel = new Car($pdo);
    $brandModel = new CarBrand($pdo);
    $categoryModel = new CarCategory($pdo);
    
    // Récupérer toutes les données nécessaires
    $cars = $carModel->getAllCars();
    $brands = $brandModel->getAllBrands();
    $categories = $categoryModel->getAllCategories();
    
    $totalCars = count($cars);
    

?>
<!-- Section Accueil -->
  <section class="hero">
    <video autoplay muted loop id="bg-video">
      <source src="././public/videos/CAR_HD.mp4" type="video/mp4">
    </video>

    <div class="hero-content">
       <div class="hero-text">
        <p class="hero-logo">BARIZ MOTORSPORT</p>
        <h1>Machines<br>Extraordinaire</h1>
       </div>
        <div class="btn-group">
            <a href="index.php?page=Voitures" class="btn btn-primary" anim="sheen">EXPLORER<i class="fa-solid fa-arrow-right"></i></a>
            <a href="about.html#Section-header-about" class="btn btn-secondary" anim="sheen">EN SAVOIR PLUS<i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </div>
    <div class="defiler">
        <p>DEFILER</p>
        <img src="././public/images/fleche.png" alt="fleshe">
    </div>
  </section>
  
  <!-- Section Car Swiper -->
  <section class="car-swiper">
    <div class="swiper-header">
      <h1>COLLECTIONS SIGNATURE</h1>
      <h2>Machines Extraordinaires</h2>
    </div>
    
    <div class="swiper">
      <div class="swiper-wrapper">
        <!-- Slide 1 -->
         <?php if (isset($error)): ?>
          <div class="error-message"><?php echo $error; ?></div>
      <?php elseif (empty($cars)): ?>
          <div class="no-cars-message" style="color: white;">Aucune voiture disponible pour le moment.</div>
      <?php else: ?>
          <?php foreach ($cars as $car): ?>
                      <?php
          $brand = $brandModel->getBrandById($car['brand_id']);
          $category = $categoryModel->getCategoryById($car['category_id']);
          
          // Déterminer le statut et la classe CSS
          $statusText = 'Disponible';
          $statusClass = 'available';
          if ($car['status'] === 'réservé') {
              $statusText = 'Réservé';
              $statusClass = 'reserved';
          } elseif ($car['status'] === 'en maintenance') {
              $statusText = 'Maintenance';
              $statusClass = 'maintenance';
          } elseif ($car['status'] === 'indisponible') {
              $statusText = 'Indisponible';
              $statusClass = 'unavailable';
          }
          
          $carImage = !empty($car['main_image_url']) ? '././public/' . $car['main_image_url'] : '././public/images/mercedess.png';
          ?>
        <div class="swiper-slide">
          <div class="slide-image">
           <img src="<?php echo $carImage; ?>" alt="<?php echo htmlspecialchars($brand ? $brand['name'] : 'Marque'); ?> <?php echo htmlspecialchars($car['model']); ?>">
          </div>
          <div class="slide-content">
            <div>
              <h3 class="slide-title"><?php echo htmlspecialchars($brand ? $brand['name'] : 'Marque inconnue'); ?></h3>
              <p class="slide-description"><?php echo htmlspecialchars($car['model']); ?> • <?php echo htmlspecialchars($car['year']); ?></p>
            </div>
            <div class="slide-footer">
              <div class="slide-price"><?php echo number_format($car['daily_price'], 0, ',', ' '); ?> €/jour</div>
                <button class="slide-button-home view-details-btn-home" data-car-id="<?php echo $car['id']; ?>">
                    <?php echo $car['status'] === 'disponible' ? 'Découvrir' : 'Indisponible'; ?>
                </button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
      </div>
      
      <!-- Pagination -->
      <div class="swiper-pagination"></div>
      
      <!-- Navigation buttons -->
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
    </div>
  </section>
  <section class="Contact" id="Contact">
    <div class="Section-header">
      <h1>Contactez-nous</h1>
      <h2>Nous Joindre</h2>
    </div>
  <div class="cards-container">
    <a href="https://maps.app.goo.gl/Bt2JjcFFiWS7PYCG8" target="_blank">
    <div class="card">
      <i class="fas fa-map-marker-alt"></i>
      <h3>Localisation</h3>
      <p>Voir Sur google map</p>
    </div>
    </a>
    <div class="card">
      <i class="fas fa-phone"></i>
      <h3>Telephone</h3>
      <p>+212600000000</p>
    </div>
    <a href="mailto:bariz.car@gmail.com" target="_blank">
    <div class="card">
      <i class="fas fa-envelope"></i>
      <h3>Email</h3>
      <p>bariz.car@gmail.com</p>
    </div>
    </a>
    <a href="https://www.instagram.com/rami_r.ez/" target="_blank">
    <div class="card">
      <i class="fa-brands fa-square-instagram"></i>
      <h3>Instagram</h3>
      <p>@BARIZ_CAR</p>
    </div>
    </a>
  </div>

  <div class="map-container">
    <iframe
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d6639.038152866971!2d-7.392315495689843!3d33.695514943881726!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xda7b6f11d7e29a5%3A0x9a3d76b2ba1c3662!2sGare%20de%20Mohamm%C3%A9dia!5e0!3m2!1sfr!2sma!4v1761778144949!5m2!1sfr!2sma" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
      allowfullscreen="" 
      loading="lazy" 
      referrerpolicy="no-referrer-when-downgrade">
    </iframe>
  </div>
</section>
<?php elseif ($page == 'Voitures') :?>
<?php
require_once './config/db-config.php';
require_once './models/Car.php';
require_once './models/CarBrand.php';
require_once './models/CarCategory.php';


    // Initialiser les modèles
    $carModel = new Car($pdo);
    $brandModel = new CarBrand($pdo);
    $categoryModel = new CarCategory($pdo);
    
    // Récupérer toutes les données nécessaires
    $cars = $carModel->getAllCars();
    $brands = $brandModel->getAllBrands();
    $categories = $categoryModel->getAllCategories();
    
    $totalCars = count($cars);
    

?>
<!-- Section Hero -->
<section class="hero-cars">
    <div class="image-container">
      <img src="././public/images/car-1.png" alt="" id="CarBackground">
    </div>
    <div class="hero-cars-content">
       <div class="hero-cars-text">
        <h1>Voitures de Luxe</h1>
        <p>Explorez notre collection exclusive des voitures les plus prestigieuses au monde, des bolides sportifs ultra-performants aux modèles classiques intemporels.</p>
        </div>
    </div>
  </section>

  <section class="drop-filter">
      <div class="custom-dropdown-mobile">
          <span id="mobile-result-count"><?php echo $totalCars; ?> Résultats</span>
          <i class="fa-solid fa-filter"></i>
      </div>
      <div class="custom-dropdown-card">
          <!-- Dropdown 1 - Marques -->
          <div class="custom-dropdown">
              <div class="dropdown-title">Marques :</div>
              <input type="checkbox" id="dropdown-toggle-brand">
              <label for="dropdown-toggle-brand"></label>
              <ul id="brand-filter">
                  <li data-brand="all">Toutes les marques <i class="fa-solid fa-check drop-car"></i></li>
                  <?php foreach ($brands as $brand): ?>
                  <li data-brand="<?php echo $brand['id']; ?>">
                      <?php echo htmlspecialchars($brand['name']); ?> 
                      <i class="fa-solid fa-check drop-car"></i>
                  </li>
                  <?php endforeach; ?>
              </ul>
          </div>

          <!-- Dropdown 2 - Catégories -->
          <div class="custom-dropdown custom-dropdown-2">
              <div class="dropdown-title">Catégories :</div>
              <input type="checkbox" id="dropdown-toggle-category">
              <label for="dropdown-toggle-category"></label>
              <ul id="category-filter">
                  <li data-category="all">Toutes les catégories <i class="fa-solid fa-check drop-car"></i></li>
                  <?php foreach ($categories as $category): ?>
                  <li data-category="<?php echo $category['id']; ?>">
                      <?php echo htmlspecialchars($category['name']); ?> 
                      <i class="fa-solid fa-check drop-car"></i>
                  </li>
                  <?php endforeach; ?>
              </ul>
          </div>
          
          <div class="custom-dropdownnn">
              <div class="filter-results">
                  <button id="search-btn">
                      <span id="result-count"><?php echo $totalCars; ?></span>
                      <span>Résultats</span>
                      <i class="fa-solid fa-magnifying-glass"></i>
                  </button>
              </div>
          </div>
      </div>
  </section>

  <section class="filter-menu">
      <div class="collection">
          <h2>Notre collection</h2>
      </div>
    <div class="filter-options" id="status-buttons">
        <button class="filter-btn status-btn active" data-status="all">Tous</button>
        <button class="filter-btn status-btn" data-status="disponible">Disponible</button>
        <button class="filter-btn status-btn" data-status="réservé">Réservé</button>
        <button class="filter-btn status-btn" data-status="en maintenance">En maintenance</button>
        <button class="filter-btn status-btn" data-status="indisponible">Indisponible</button>
    </div>
  </section>

    <!-- <section class="filter-menu-btn">
    <div class="collection">
        <h2>Notre collection</h2>
    </div>
    <div class="filter-options" id="status-buttons">
        <button class="filter-btn status-btn active" data-status="all">Tous</button>
        <button class="filter-btn status-btn" data-status="disponible">Disponible</button>
        <button class="filter-btn status-btn" data-status="réservé">Réservé</button>
        <button class="filter-btn status-btn" data-status="en maintenance">En maintenance</button>
        <button class="filter-btn status-btn" data-status="indisponible">Indisponible</button>
    </div>
    </section> -->

  <section class="car-card-container" id="cars-container">
      <?php if (isset($error)): ?>
          <div class="error-message"><?php echo $error; ?></div>
      <?php elseif (empty($cars)): ?>
          <div class="no-cars-message" style="color: white;">Aucune voiture disponible pour le moment.</div>
      <?php else: ?>
          <?php foreach ($cars as $car): ?>
          <?php
          $brand = $brandModel->getBrandById($car['brand_id']);
          $category = $categoryModel->getCategoryById($car['category_id']);
          
          // Déterminer le statut et la classe CSS
          $statusText = 'Disponible';
          $statusClass = 'available';
          if ($car['status'] === 'réservé') {
              $statusText = 'Réservé';
              $statusClass = 'reserved';
          } elseif ($car['status'] === 'en maintenance') {
              $statusText = 'Maintenance';
              $statusClass = 'maintenance';
          } elseif ($car['status'] === 'indisponible') {
              $statusText = 'Indisponible';
              $statusClass = 'unavailable';
          }
          
          $carImage = !empty($car['main_image_url']) ? '././public/' . $car['main_image_url'] : '././public/images/mercedess.png';
          ?>
          <div class="car-card" 
               data-category="<?php echo $car['category_id']; ?>" 
               data-brand="<?php echo $car['brand_id']; ?>"
               data-status="<?php echo $car['status']; ?>">
              <div class="car-status <?php echo $statusClass; ?>"><?php echo $statusText; ?></div>
              <img src="<?php echo $carImage; ?>" alt="<?php echo htmlspecialchars($brand ? $brand['name'] : 'Marque'); ?> <?php echo htmlspecialchars($car['model']); ?>">
              <div class="car-info">
                  <div class="car-info-text">
                      <h3><?php echo htmlspecialchars($brand ? $brand['name'] : 'Marque inconnue'); ?></h3>
                      <p><?php echo htmlspecialchars($car['model']); ?> • <?php echo htmlspecialchars($car['year']); ?></p>
                      <p class="car-price"><?php echo number_format($car['daily_price'], 0, ',', ' '); ?> €/jour</p>
                  </div>
                  <button class="slide-button view-details-btn" data-car-id="<?php echo $car['id']; ?>">
                      <?php echo $car['status'] === 'disponible' ? 'Découvrir' : 'Indisponible'; ?>
                  </button>
              </div>
          </div>
          <?php endforeach; ?>
      <?php endif; ?>
  </section>

  <!-- Modal de chargement -->
  <div id="loading-modal" class="loading-modal" style="display: none;">
      <div class="loading-content">
          <div class="loading-spinner"></div>
          <p>Chargement des voitures...</p>
      </div>
  </div>

<?php elseif ($page == 'Voitures-Detailles') :?>
<?php
require_once './config/db-config.php';
require_once './models/Car.php';
require_once './models/CarBrand.php';
require_once './models/CarCategory.php';
require_once './models/CarImage.php';

// Récupérer l'ID de la voiture depuis l'URL
$car_id = isset($_GET['car_id']) ? intval($_GET['car_id']) : 0;

// Initialiser les modèles
$carModel = new Car($pdo);
$brandModel = new CarBrand($pdo);
$categoryModel = new CarCategory($pdo);
$carImageModel = new CarImage($pdo);

// Récupérer les données de la voiture
$car = $carModel->getCarById($car_id);

if (!$car) {
    // Rediriger si la voiture n'existe pas
    header('Location: index.php?page=Voitures');
    exit;
}

// Récupérer les informations liées
$brand = $brandModel->getBrandById($car['brand_id']);
$category = $categoryModel->getCategoryById($car['category_id']);
$carImages = $carImageModel->getImagesByCarId($car_id);

// Si pas d'images spécifiques, utiliser l'image principale
if (empty($carImages) && !empty($car['main_image_url'])) {
    $carImages = [
        ['image_url' => $car['main_image_url'], 'image_order' => 0]
    ];
}

// Déterminer le statut
$statusText = 'Disponible';
$statusClass = 'available';
if ($car['status'] === 'réservé') {
    $statusText = 'Réservé';
    $statusClass = 'reserved';
} elseif ($car['status'] === 'en maintenance') {
    $statusText = 'Maintenance';
    $statusClass = 'maintenance';
} elseif ($car['status'] === 'indisponible') {
    $statusText = 'Indisponible';
    $statusClass = 'unavailable';
}
?>
<!-- Section Hero -->
<section class="hero-cars-details">
    <div class="image-container-details">
        <?php if (!empty($car['main_image_url'])): ?>
            <img src="./public/<?php echo htmlspecialchars($car['main_image_url']); ?>" alt="<?php echo htmlspecialchars($brand['name'] . ' ' . $car['model']); ?>" id="CarBackground">
        <?php else: ?>
            <img src="./public/images/car-2.jpg" alt="Image par défaut" id="CarBackground">
        <?php endif; ?>
    </div>
    <div class="smoke-effect-realistic"></div>
  
    <div class="hero-cars-details-content">
        <div class="hero-cars-details-text">
            <p><?php echo htmlspecialchars($category['name']); ?></p>
            <h1><?php echo htmlspecialchars($brand['name'] . ' ' . $car['model']); ?></h1>
            <h3>Modèle <?php echo htmlspecialchars($car['year']); ?></h3>
        </div>
    </div>
</section>

<div class="gallery-and-contact-container">
    <section class="gallery-page">
        <div class="gallery-header">
            <h1>Galerie</h1>
        </div>
        
        <div class="gallery-container-main">
            <div class="gallery-content">
                <div class="gallery-main-image">
                    <?php if (!empty($carImages)): ?>
                        <img id="galleryMainImage" class="gallery-image-display" src="././public/<?php echo htmlspecialchars($carImages[0]['image_url']); ?>" alt="<?php echo htmlspecialchars($brand['name'] . ' ' . $car['model']); ?>">
                    <?php else: ?>
                        <img id="galleryMainImage" class="gallery-image-display" src="././public/images/car-1.png" alt="Image par défaut">
                    <?php endif; ?>
                    <div class="gallery-counter">
                        <span id="galleryCurrentImage">1</span> / <span id="galleryTotalImages"><?php echo count($carImages); ?></span>
                    </div>
                    
                    <div class="gallery-navigation">
                        <div id="galleryPrevBtn" class="gallery-nav-btn disabled">
                            <i class="fas fa-chevron-left"></i>
                        </div>
                        <div id="galleryNextBtn" class="gallery-nav-btn">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($carImages)): ?>
                <div class="gallery-thumbnail-container">
                    <div class="swiper gallery-thumbnail-swiper">
                        <div class="swiper-wrapper">
                            <?php foreach ($carImages as $index => $image): ?>
                              <div class="swiper-slide gallery-thumb-slide <?php echo $index === 0 ? 'gallery-thumb-slide-active' : ''; ?>" 
                                  data-image="./public/<?php echo htmlspecialchars($image['image_url']); ?>" 
                                  data-index="<?php echo $index; ?>">
                                  <img src="./public/<?php echo htmlspecialchars($image['image_url']); ?>" alt="Vue <?php echo $index + 1; ?> de <?php echo htmlspecialchars($brand['name'] . ' ' . $car['model']); ?>">
                              </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="swiper-pagination gallery-pagination"></div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="Spécifications">
            <h3>Spécifications Techniques</h3>
            <div class="spécifications-container">
                <div class="moteur">
                    <i class="fa-solid fa-bolt"></i>
                    <h4>Moteur</h4>
                    <p>
                        <?php 
                        $fuelTypes = [
                            'essence' => 'Essence',
                            'diesel' => 'Diesel', 
                            'electrique' => 'Électrique',
                            'hybride' => 'Hybride'
                        ];
                        echo htmlspecialchars($fuelTypes[$car['fuel_type']] ?? $car['fuel_type']);
                        ?>
                    </p>
                </div>
                <div class="Puissance">
                    <i class="fa-solid fa-gears"></i>
                    <h4>Transmission</h4>
                    <p><?php echo htmlspecialchars($car['transmission'] === 'automatic' ? 'Automatique' : 'Manuelle'); ?></p>
                </div>
                <div class="Accélération">
                    <i class="fa-solid fa-calendar"></i>
                    <h4>Année</h4>
                    <p><?php echo htmlspecialchars($car['year']); ?></p>
                </div>
                <div class="Vitesse Max">
                    <i class="fa-solid fa-palette"></i>
                    <h4>Couleur</h4>
                    <p><?php echo htmlspecialchars($car['color'] ?: 'Non spécifiée'); ?></p>
                </div>
                <div class="Catégorie">
                    <i class="fa-solid fa-tag"></i>
                    <h4>Catégorie</h4>
                    <p><?php echo htmlspecialchars($category['name']); ?></p>
                </div>
                <div class="Marque">
                    <i class="fa-solid fa-industry"></i>
                    <h4>Marque</h4>
                    <p><?php echo htmlspecialchars($brand['name']); ?></p>
                </div>
            </div>
        </div>

        <?php if (!empty($car['description'])): ?>
        <div class="description-section">
            <h3>Description</h3>
            <div class="description-content">
                <p><?php echo nl2br(htmlspecialchars($car['description'])); ?></p>
            </div>
        </div>
        <?php endif; ?>
    </section>

    <!-- Section Contactez-nous pour les prix -->
    <aside class="contact-pricing-sidebar">
        <div class="pricing-card">
            <h3>Tarifs de location</h3>
            <div class="contact-info">
                <div class="price-display">
                    <h4><?php echo number_format($car['daily_price'], 0, ',', ' '); ?> €/jour</h4>
                    <p class="car-status-badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></p>
                </div>
                <div class="contact-person">
                    <h4>Votre contact</h4>
                    <p class="role">Sales Manager</p>
                    <div class="contact-details">
                        <p><i class="fas fa-phone"></i> +212 0 60-000-000</p>
                        <p><i class="fas fa-envelope"></i> Bariz@gmail.com</p>
                    </div>
                </div>
                <div class="finance-options">
                    <p>Contactez-nous pour des options de financement personnalisées</p>
                </div>
                <button class="booking-btn <?php echo $car['status'] !== 'disponible' ? 'disabled' : ''; ?>" 
                        <?php echo $car['status'] !== 'disponible' ? 'disabled' : ''; ?>>
                    <?php echo $car['status'] === 'disponible' ? 'Réserver' : 'Indisponible'; ?>
                </button>
            </div>
        </div>
    </aside>
</div>

<!-- Modal de Réservation -->
<div class="booking-modal" id="bookingModal">
    <div class="booking-modal-content">
        <button class="booking-modal-close" id="bookingModalClose">
            <i class="fas fa-times"></i>
        </button>
        <h2 class="booking-modal-title">RÉSERVER CETTE VOITURE</h2>
        <form id="bookingForm">
            <input type="hidden" name="car_id" value="<?php echo $car_id; ?>">
            <input type="hidden" name="car_model" value="<?php echo htmlspecialchars($brand['name'] . ' ' . $car['model']); ?>">
            <input type="hidden" name="daily_price" value="<?php echo $car['daily_price']; ?>">

            <div class="booking-form-row">
                <div class="booking-form-group">
                    <label for="booking-pickup-firstname" class="booking-form-label booking-required">Nom</label>
                    <input type="text" id="booking-pickup-firstname" class="booking-form-input" name="first-name" required>
                </div>
                <div class="booking-form-group">
                    <label for="booking-return-lastname" class="booking-form-label booking-required">Prénom</label>
                    <input type="text" id="booking-return-lastname" class="booking-form-input" name="last-name" required>
                </div>
            </div>
            
            <div class="booking-form-row">
                <div class="booking-form-group">
                    <label for="booking-pickup-date" class="booking-form-label booking-required">Date de prise en charge</label>
                    <div class="date-input-container">
                        <input type="date" id="booking-pickup-date" class="booking-form-input date-time-input" name="pickup-date" required>
                        <i class="fas fa-calendar-alt date-icon"></i>
                    </div>
                </div>
                <div class="booking-form-group">
                    <label for="booking-pickup-time" class="booking-form-label booking-required">Heure de prise en charge</label>
                    <div class="time-input-container">
                        <input type="time" id="booking-pickup-time" class="booking-form-input date-time-input" name="pickup-time" required>
                        <i class="fas fa-clock time-icon"></i>
                    </div>
                </div>
            </div>
            
            <div class="booking-form-row">
                <div class="booking-form-group">
                    <label for="booking-return-date" class="booking-form-label booking-required">Date de restitution</label>
                    <div class="date-input-container">
                        <input type="date" id="booking-return-date" class="booking-form-input date-time-input" name="return-date" required>
                        <i class="fas fa-calendar-alt date-icon"></i>
                    </div>
                </div>
                <div class="booking-form-group">
                    <label for="booking-return-time" class="booking-form-label booking-required">Heure de restitution</label>
                    <div class="time-input-container">
                        <input type="time" id="booking-return-time" class="booking-form-input date-time-input" name="return-time" required>
                        <i class="fas fa-clock time-icon"></i>
                    </div>
                </div>
            </div>
            
            <div class="booking-form-group">
                <label for="booking-driver-Telephone" class="booking-form-label booking-required">Téléphone</label>
                <input type="tel" id="booking-driver-Telephone" class="booking-form-input" name="Telephone" required>
            </div>
            
            <div class="booking-form-group">
                <label for="booking-email" class="booking-form-label booking-required">Email</label>
                <input type="email" id="booking-email" class="booking-form-input" name="email" required>
            </div>
            
            <div class="booking-form-group">
                <label for="booking-special-requests" class="booking-form-label">Demandes spéciales</label>
                <textarea id="booking-special-requests" class="booking-form-textarea" name="special-requests" rows="3" placeholder="Siège bébé, GPS, etc."></textarea>
            </div>
            
            <div class="booking-summary">
                <h4>Résumé de la réservation</h4>
                <div class="summary-details">
                    <p><strong>Véhicule:</strong> <?php echo htmlspecialchars($brand['name'] . ' ' . $car['model']); ?></p>
                    <p><strong>Prix journalier:</strong> <?php echo number_format($car['daily_price'], 0, ',', ' '); ?> €</p>
                    <p><strong>Durée:</strong> <span id="booking-duration">0</span> jour(s)</p>
                    <p><strong>Total estimé:</strong> <span id="booking-total">0</span> €</p>
                </div>
            </div>
            
            <button type="submit" class="booking-form-submit">CONFIRMER LA RÉSERVATION</button>
        </form>
    </div>
</div>
<script>
// Initialiser la galerie quand la page est complètement chargée
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM chargé, vérification de la galerie...');
    
    // Attendre que tous les éléments soient rendus
    setTimeout(function() {
        if (document.querySelector('.gallery-page')) {
            console.log('Page galerie détectée, initialisation...');
            initGallery();
        } else {
            console.log('Page galerie non détectée');
        }
    }, 100);
    
    // Calculer la durée et le total de la réservation
    function calculateBooking() {
        const pickupDateInput = document.getElementById('booking-pickup-date');
        const returnDateInput = document.getElementById('booking-return-date');
        
        if (pickupDateInput && returnDateInput) {
            const pickupDate = new Date(pickupDateInput.value);
            const returnDate = new Date(returnDateInput.value);
            
            if (pickupDate && returnDate && returnDate > pickupDate) {
                const duration = Math.ceil((returnDate - pickupDate) / (1000 * 60 * 60 * 24));
                const dailyPrice = <?php echo $car['daily_price']; ?>;
                const total = duration * dailyPrice;
                
                document.getElementById('booking-duration').textContent = duration;
                document.getElementById('booking-total').textContent = total.toLocaleString('fr-FR');
            } else {
                document.getElementById('booking-duration').textContent = '0';
                document.getElementById('booking-total').textContent = '0';
            }
        }
    }
    
    // Écouter les changements de dates
    const pickupDate = document.getElementById('booking-pickup-date');
    const returnDate = document.getElementById('booking-return-date');
    
    if (pickupDate) pickupDate.addEventListener('change', calculateBooking);
    if (returnDate) returnDate.addEventListener('change', calculateBooking);
});
</script>
<?php elseif ($page == 'A-propos') :?>
<!-- Section Hero -->
<section class="hero-about">
  <div class="hero-about-content">
    <div class="hero-about-text">
      <div class="Section-header-about">
        <h4>about</h4>
        <h2>Nous Joindre</h2>
      </div>
      <div class="container-about">
        <img src="././public/images/BARIZ MOTORSPORT.png" alt="Bariz Motorsport" id="poster-about">
        <p>Avec une passion inébranlable pour la performance et une admiration profonde pour l'art automobile, Bariz Motorsport s'impose comme une référence incontournable dans l'univers des véhicules d'exception. Fondée par des passionnés de vitesse et d'élégance, la marque incarne l'union parfaite entre puissance, design et innovation.</p>
      </div>
    </div>
  </div>
</section>
<?php endif ?>



<?php include('./views/footer.php')?>