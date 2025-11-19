// == SWIPER EVENT == //
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Swiper
    const swiper = new Swiper('.swiper', {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            640: {
                slidesPerView: 1,
            },
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            },
        },
    });
});

//== Effet du bordure pour le menu ==//
const navbar = document.querySelector('.navbar');
const logo = document.getElementById('logo')
window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        navbar.classList.add('active');
        logo.classList.add('active');
    } else {
        navbar.classList.remove('active');
        logo.classList.remove('active');
    }
});

//== afficher le menu en Mobile ==//
const burger = document.getElementById('burger');
const fullscreenMenu = document.getElementById('fullscreenMenu');

burger.addEventListener('click', () => {
    fullscreenMenu.classList.toggle('active');
    if (fullscreenMenu.classList.contains('active')) {
        burger.innerHTML = '<i class="fas fa-times"></i>';
    } else {
        burger.innerHTML = '<i class="fas fa-bars"></i>';
    }
});

//== Loader au démarrage de la page ==//
const loader = document.getElementById("loader");
const content = document.getElementById("content");

// Masquer le loader après quelques secondes
setTimeout(() => {
    loader.style.opacity = "0";
    loader.style.transition = "opacity 1s ease";
    setTimeout(() => {
        loader.style.display = "none";
        content.style.display = "block";
        document.body.style.overflow = "auto";
    }, 1000);
}, 3500);

// Gestion de l'affichage de la card
document.addEventListener('DOMContentLoaded', function() {
    const mobileDropdown = document.querySelector('.custom-dropdown-mobile');
    const dropdownCard = document.querySelector('.custom-dropdown-card');

    mobileDropdown.addEventListener('click', function() {
        dropdownCard.classList.toggle('show-card');
        if (dropdownCard.classList.contains('show-card')) {
            setTimeout(() => {
                forceIconsDisplay();
            }, 100);
        }
    });

    // Fermer la card en cliquant à l'extérieur
    document.addEventListener('click', function(event) {
        if (!mobileDropdown.contains(event.target) && !dropdownCard.contains(event.target)) {
            dropdownCard.classList.remove('show-card');
        }
    });
});

// Fonction pour forcer l'affichage de toutes les icônes
function forceIconsDisplay() {
    const dropdowns = document.querySelectorAll('.custom-dropdown-card .custom-dropdown');
    dropdowns.forEach(dropdown => {
        const options = dropdown.querySelectorAll('ul li');
        options.forEach(option => {
            const icon = option.querySelector('i');
            if (icon) {
                icon.style.display = 'inline-block';
                icon.style.opacity = '0.3';
            }
        });

        // Première option en pleine opacité
        const firstOption = options[0];
        const firstIcon = firstOption.querySelector('i');
        if (firstIcon) {
            firstIcon.style.opacity = '1';
        }
    });
}

// Filter Custom Drop down
function initDropdown(dropdown) {
    const label = dropdown.querySelector('label');
    const options = dropdown.querySelectorAll('ul li');
    const checkbox = dropdown.querySelector('input');

    if (options.length > 0 && label) {
        options.forEach(option => {
            const icon = option.querySelector('i');
            if (icon) {
                icon.style.display = 'inline-block';
                icon.style.opacity = '0.3';
            }
        });

        // Sélection par défaut : première option
        const firstOption = options[0];
        const firstIcon = firstOption.querySelector('i');
        if (firstIcon) {
            firstIcon.style.opacity = '1';
        }
        label.textContent = firstOption.childNodes[0].textContent.trim();

        options.forEach(option => {
            option.addEventListener('click', () => {
                label.textContent = option.childNodes[0].textContent.trim();
                checkbox.checked = false;

                options.forEach(opt => {
                    const optIcon = opt.querySelector('i');
                    if (optIcon) {
                        optIcon.style.opacity = '0.3';
                    }
                });

                const selectedIcon = option.querySelector('i');
                if (selectedIcon) {
                    selectedIcon.style.opacity = '1';
                }
            });
        });
    }
}

// Initialiser tous les dropdowns
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.custom-dropdown').forEach(initDropdown);
    setTimeout(forceIconsDisplay, 500);
});

// Fermer les dropdowns si clic à l'extérieur
document.addEventListener('click', (event) => {
    document.querySelectorAll('.custom-dropdown input').forEach(input => {
        const dropdown = input.closest('.custom-dropdown');
        if (!dropdown.contains(event.target)) {
            input.checked = false;
        }
    });
});

// car details gallery 
function initGallery() {
    const thumbSlides = document.querySelectorAll('.gallery-thumb-slide');
    const galleryImages = [];

    if (thumbSlides.length === 0) {
        return;
    }

    thumbSlides.forEach((slide, index) => {
        const imageUrl = slide.getAttribute('data-image');
        galleryImages.push({
            main: imageUrl,
            thumb: imageUrl
        });
    });

    let galleryCurrentIndex = 0;
    const galleryTotalImages = galleryImages.length;

    const galleryMainImage = document.getElementById('galleryMainImage');
    const galleryCurrentImageSpan = document.getElementById('galleryCurrentImage');
    const galleryTotalImagesSpan = document.getElementById('galleryTotalImages');
    const galleryPrevBtn = document.getElementById('galleryPrevBtn');
    const galleryNextBtn = document.getElementById('galleryNextBtn');

    if (!galleryMainImage) {
        return;
    }

    galleryTotalImagesSpan.textContent = galleryTotalImages;

    function updateGalleryMainImage(index) {
        if (index < 0 || index >= galleryTotalImages) {
            return;
        }

        galleryCurrentIndex = index;
        const newImageSrc = galleryImages[galleryCurrentIndex].main;

        galleryMainImage.style.opacity = '0';

        setTimeout(() => {
            galleryMainImage.src = newImageSrc;
            galleryMainImage.alt = 'Vue ' + (galleryCurrentIndex + 1) + ' de la voiture';
            galleryMainImage.style.opacity = '1';
            updateGalleryCounter();
            updateGalleryButtons();
            updateActiveThumb();

            if (window.galleryThumbnailSwiper) {
                window.galleryThumbnailSwiper.slideTo(galleryCurrentIndex);
            }
        }, 200);
    }

    function updateGalleryCounter() {
        galleryCurrentImageSpan.textContent = galleryCurrentIndex + 1;
    }

    function updateGalleryButtons() {
        if (galleryPrevBtn) {
            galleryPrevBtn.classList.toggle('disabled', galleryCurrentIndex === 0);
        }
        if (galleryNextBtn) {
            galleryNextBtn.classList.toggle('disabled', galleryCurrentIndex === galleryTotalImages - 1);
        }
    }

    function updateActiveThumb() {
        thumbSlides.forEach((slide, index) => {
            if (index === galleryCurrentIndex) {
                slide.classList.add('gallery-thumb-slide-active');
            } else {
                slide.classList.remove('gallery-thumb-slide-active');
            }
        });
    }

    function initSwiper() {
        try {
            window.galleryThumbnailSwiper = new Swiper('.gallery-thumbnail-swiper', {
                slidesPerView: 'auto',
                spaceBetween: 15,
                centeredSlides: false,
                loop: false,
                pagination: {
                    el: '.gallery-pagination',
                    clickable: true,
                },
                breakpoints: {
                    640: {
                        slidesPerView: 3,
                        spaceBetween: 10,
                    },
                    768: {
                        slidesPerView: 4,
                        spaceBetween: 15,
                    },
                    1024: {
                        slidesPerView: 5,
                        spaceBetween: 20,
                    },
                },
                on: {
                    slideChange: function() {
                        const activeIndex = this.activeIndex;
                        if (activeIndex !== galleryCurrentIndex) {
                            updateGalleryMainImage(activeIndex);
                        }
                    }
                }
            });
        } catch (error) {
            // Erreur silencieuse pour Swiper
        }
    }

    if (galleryPrevBtn) {
        galleryPrevBtn.addEventListener('click', function() {
            if (galleryCurrentIndex > 0) {
                updateGalleryMainImage(galleryCurrentIndex - 1);
            }
        });
    }

    if (galleryNextBtn) {
        galleryNextBtn.addEventListener('click', function() {
            if (galleryCurrentIndex < galleryTotalImages - 1) {
                updateGalleryMainImage(galleryCurrentIndex + 1);
            }
        });
    }

    thumbSlides.forEach((slide, index) => {
        slide.addEventListener('click', function() {
            updateGalleryMainImage(index);
        });
    });

    updateGalleryCounter();
    updateGalleryButtons();
    updateActiveThumb();
    initSwiper();
}

// Fonction pour afficher les messages dans le formulaire
function showBookingMessage(message, type = 'info') {
    const messageDiv = document.getElementById('booking-messages');
    const messageContent = document.getElementById('booking-message-content');
    
    messageDiv.className = 'booking-messages';
    switch(type) {
        case 'success':
            messageDiv.classList.add('success');
            break;
        case 'error':
            messageDiv.classList.add('error');
            break;
        case 'info':
            messageDiv.classList.add('info');
            break;
    }
    
    messageContent.innerHTML = message;
    messageDiv.style.display = 'block';
    
    messageDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

 // Gestion du modal de réservation
    document.addEventListener('DOMContentLoaded', function() {
        const bookingModal = document.getElementById('bookingModal');
        const bookingModalClose = document.getElementById('bookingModalClose');
        const bookingForm = document.getElementById('bookingForm');
        const bookingBtn = document.querySelector('.booking-btn');
        
        // Ouvrir le modal
        if (bookingBtn) {
            bookingBtn.addEventListener('click', function() {
                bookingModal.classList.add('active');
                document.body.style.overflow = 'hidden'; // Empêcher le défilement
            });
        }
        
        // Fermer le modal
        bookingModalClose.addEventListener('click', function() {
            bookingModal.classList.remove('active');
            document.body.style.overflow = 'auto'; // Rétablir le défilement
        });
        
        // Fermer le modal en cliquant à l'extérieur
        bookingModal.addEventListener('click', function(e) {
            if (e.target === bookingModal) {
                bookingModal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });
        
        const basePathMeta = document.querySelector('meta[name="app-base-path"]');
        const metaBasePath = basePathMeta ? basePathMeta.getAttribute('content') : '';
        const normalizedBasePath = metaBasePath ? (metaBasePath.startsWith('/') ? metaBasePath : `/${metaBasePath}`) : '';
        const reservationEndpoint = `${window.location.origin}${normalizedBasePath}/controllers/ReservationController.php`.replace(/([^:]\/)\/+/g, '$1');

        // Remplacer cette partie dans main.js
bookingForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Afficher le message de chargement
    showBookingMessage('Traitement de votre réservation en cours...', 'info');
    
    // Validation simple
    const pickupDate = new Date(document.getElementById('booking-pickup-date').value);
    const returnDate = new Date(document.getElementById('booking-return-date').value);
    
    if (returnDate <= pickupDate) {
        showBookingMessage('La date de restitution doit être postérieure à la date de prise en charge.', 'error');
        return;
    }
    
    // Préparer les données du formulaire
    const formData = new FormData(bookingForm);
    formData.append('action', 'create_reservation');
    
    console.log('Envoi des données:', Object.fromEntries(formData));
    
    // Envoyer la requête AJAX
    fetch(reservationEndpoint, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Statut de la réponse:', response.status);
        if (!response.ok) {
            throw new Error('Erreur HTTP: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Réponse reçue:', data);
        if (data.success) {
            showBookingMessage(data.message, 'success');
            
            // Réinitialiser le formulaire après 3 secondes
            setTimeout(() => {
                bookingForm.reset();
                document.getElementById('booking-duration').textContent = '0';
                document.getElementById('booking-total').textContent = '0';
                
                // Fermer le modal après succès
                setTimeout(() => {
                    bookingModal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }, 2000);
            }, 3000);
        } else {
            showBookingMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Erreur complète:', error);
        showBookingMessage('Une erreur est survenue lors de la réservation. Détails: ' + error.message, 'error');
    });
});
        
        // Définir la date minimale comme aujourd'hui
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('booking-pickup-date').min = today;
        document.getElementById('booking-return-date').min = today;
    });

// SYSTÈME DE FILTRAGE COMPLET
document.addEventListener('DOMContentLoaded', function() {
    const carsContainer = document.getElementById('cars-container');
    const categoryButtons = document.querySelectorAll('#category-buttons .filter-btn');
    const statusButtons = document.querySelectorAll('#status-buttons .status-btn');
    const brandFilterItems = document.querySelectorAll('#brand-filter li');
    const categoryFilterItems = document.querySelectorAll('#category-filter li');
    const resultCount = document.getElementById('result-count');
    const mobileResultCount = document.getElementById('mobile-result-count');
    const loadingModal = document.getElementById('loading-modal');

    let currentFilters = {
        category: 'all',
        brand: 'all',
        status: 'all'
    };

    function filterCars() {
        const carCards = document.querySelectorAll('.car-card');
        let visibleCount = 0;

        carCards.forEach(card => {
            const cardCategory = card.getAttribute('data-category');
            const cardBrand = card.getAttribute('data-brand');
            const cardStatus = card.getAttribute('data-status');

            const categoryMatch = currentFilters.category === 'all' || cardCategory === currentFilters.category;
            const brandMatch = currentFilters.brand === 'all' || cardBrand === currentFilters.brand;
            const statusMatch = currentFilters.status === 'all' || cardStatus === currentFilters.status;

            if (categoryMatch && brandMatch && statusMatch) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        resultCount.textContent = visibleCount;
        mobileResultCount.textContent = visibleCount + ' Résultats';

        showNoResultsMessage(visibleCount);
    }

    function showNoResultsMessage(visibleCount) {
        let noResultsMessage = document.getElementById('no-results-message');

        if (visibleCount === 0) {
            if (!noResultsMessage) {
                noResultsMessage = document.createElement('div');
                noResultsMessage.id = 'no-results-message';
                noResultsMessage.className = 'no-results-message';
                noResultsMessage.innerHTML = `
                    <i class="fas fa-search"></i>
                    <h3>Aucune voiture trouvée</h3>
                    <p>Aucun véhicule ne correspond à vos critères de recherche.</p>
                    <button id="reset-filters-btn" class="reset-filters-btn">Réinitialiser les filtres</button>
                `;
                carsContainer.appendChild(noResultsMessage);

                document.getElementById('reset-filters-btn').addEventListener('click', resetAllFilters);
            }
            noResultsMessage.style.display = 'block';
        } else if (noResultsMessage) {
            noResultsMessage.style.display = 'none';
        }
    }

    function resetAllFilters() {
        currentFilters = {
            category: 'all',
            brand: 'all',
            status: 'all'
        };

        categoryButtons.forEach(btn => {
            btn.classList.remove('active');
            if (btn.getAttribute('data-category') === 'all') {
                btn.classList.add('active');
            }
        });

        statusButtons.forEach(btn => {
            btn.classList.remove('active');
            if (btn.getAttribute('data-status') === 'all') {
                btn.classList.add('active');
            }
        });

        resetDropdowns();
        filterCars();
    }

    function resetDropdowns() {
        const brandFirstOption = document.querySelector('#brand-filter li[data-brand="all"]');
        if (brandFirstOption) {
            brandFilterItems.forEach(li => li.classList.remove('selected'));
            brandFirstOption.classList.add('selected');
            const brandLabel = document.querySelector('.custom-dropdown label[for="dropdown-toggle-brand"]');
            if (brandLabel) {
                brandLabel.textContent = brandFirstOption.childNodes[0].textContent.trim();
            }
        }

        const categoryFirstOption = document.querySelector('#category-filter li[data-category="all"]');
        if (categoryFirstOption) {
            categoryFilterItems.forEach(li => li.classList.remove('selected'));
            categoryFirstOption.classList.add('selected');
            const categoryLabel = document.querySelector('.custom-dropdown-2 label[for="dropdown-toggle-category"]');
            if (categoryLabel) {
                categoryLabel.textContent = categoryFirstOption.childNodes[0].textContent.trim();
            }
        }
    }

    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            currentFilters.category = this.getAttribute('data-category');
            filterCars();
        });
    });

    statusButtons.forEach(button => {
        button.addEventListener('click', function() {
            statusButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            currentFilters.status = this.getAttribute('data-status');
            filterCars();
        });
    });

    brandFilterItems.forEach(item => {
        item.addEventListener('click', function() {
            const brand = this.getAttribute('data-brand');

            brandFilterItems.forEach(li => li.classList.remove('selected'));
            this.classList.add('selected');

            currentFilters.brand = brand;
            filterCars();

            document.getElementById('dropdown-toggle-brand').checked = false;
        });
    });

    categoryFilterItems.forEach(item => {
        item.addEventListener('click', function() {
            const category = this.getAttribute('data-category');

            categoryFilterItems.forEach(li => li.classList.remove('selected'));
            this.classList.add('selected');

            currentFilters.category = category;
            filterCars();

            document.getElementById('dropdown-toggle-category').checked = false;
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.view-details-btn') && !e.target.closest('.view-details-btn').classList.contains('disabled')) {
            const carId = e.target.closest('.view-details-btn').getAttribute('data-car-id');
            window.location.href = 'index.php?page=Voitures-Detailles&car_id=' + carId;
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.view-details-btn-home') && !e.target.closest('.view-details-btn-home').classList.contains('disabled')) {
            const carId = e.target.closest('.view-details-btn-home').getAttribute('data-car-id');
            window.location.href = 'index.php?page=Voitures-Detailles&car_id=' + carId;
        }
    });

    function loadCarsAjax(filters = {}) {
        loadingModal.style.display = 'flex';

        const formData = new FormData();
        formData.append('action', 'get_filtered_cars');
        if (filters.category && filters.category !== 'all') {
            formData.append('category_id', filters.category);
        }
        if (filters.brand && filters.brand !== 'all') {
            formData.append('brand_id', filters.brand);
        }
        if (filters.status && filters.status !== 'all') {
            formData.append('status', filters.status);
        }

        fetch('../controllers/CarController.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCarsDisplay(data.cars);
            }
        })
        .catch(error => {
            // Erreur silencieuse
        })
        .finally(() => {
            loadingModal.style.display = 'none';
        });
    }

    function updateCarsDisplay(cars) {
        // Fonction pour mettre à jour l'affichage des voitures
    }

    filterCars();
});

// Initialiser la galerie quand la page est complètement chargée
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        if (document.querySelector('.gallery-page')) {
            initGallery();
        }
    }, 100);

    function calculateBooking() {
        const pickupDateInput = document.getElementById('booking-pickup-date');
        const returnDateInput = document.getElementById('booking-return-date');

        if (pickupDateInput && returnDateInput) {
            const pickupDate = new Date(pickupDateInput.value);
            const returnDate = new Date(returnDateInput.value);

            if (pickupDate && returnDate && returnDate > pickupDate) {
                const duration = Math.ceil((returnDate - pickupDate) / (1000 * 60 * 60 * 24));
                const dailyPrice = document.querySelector('input[name="daily_price"]') ? 
                    parseFloat(document.querySelector('input[name="daily_price"]').value) : 0;
                const total = duration * dailyPrice;

                document.getElementById('booking-duration').textContent = duration;
                document.getElementById('booking-total').textContent = total.toLocaleString('fr-FR');
            } else {
                document.getElementById('booking-duration').textContent = '0';
                document.getElementById('booking-total').textContent = '0';
            }
        }
    }

    const pickupDate = document.getElementById('booking-pickup-date');
    const returnDate = document.getElementById('booking-return-date');

    if (pickupDate) pickupDate.addEventListener('change', calculateBooking);
    if (returnDate) returnDate.addEventListener('change', calculateBooking);
});


