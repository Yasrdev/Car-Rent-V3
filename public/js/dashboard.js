// Gestion de la navigation
document.addEventListener('DOMContentLoaded', function() {
    const navItems = document.querySelectorAll('.nav-item');
    const contentSections = document.querySelectorAll('.content-section');
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarClose = document.getElementById('sidebarClose');
    const headerTitle = document.querySelector('.header-title h1');

    // Navigation entre les sections
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Retirer la classe active de tous les éléments
            navItems.forEach(nav => nav.classList.remove('active'));
            contentSections.forEach(section => section.classList.remove('active'));
            
            // Ajouter la classe active à l'élément cliqué
            this.classList.add('active');
            
            // Afficher la section correspondante
            const contentId = this.getAttribute('data-content') + '-content';
            const targetSection = document.getElementById(contentId);
            if (targetSection) {
                targetSection.classList.add('active');
                
                // Mettre à jour le titre du header
                const sectionName = this.querySelector('span').textContent;
                headerTitle.textContent = sectionName;
            }
            
            // Fermer le sidebar sur mobile
            if (window.innerWidth <= 1024) {
                sidebar.classList.remove('active');
            }
        });
    });

    // Toggle sidebar sur mobile
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.add('active');
    });

    // Fermer le sidebar
    sidebarClose.addEventListener('click', function() {
        sidebar.classList.remove('active');
    });

    // Fermer le sidebar en cliquant à l'extérieur
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 1024 && 
            !sidebar.contains(e.target) && 
            !sidebarToggle.contains(e.target)) {
            sidebar.classList.remove('active');
        }
    });

    // Gestion du redimensionnement de la fenêtre
    window.addEventListener('resize', function() {
        if (window.innerWidth > 1024) {
            sidebar.classList.remove('active');
        }
    });

    // Animation de chargement
    const activeSection = document.querySelector('.content-section.active');
    if (activeSection) {
        activeSection.style.animation = 'fadeIn 0.3s ease';
    }

    // Initialiser les boutons d'édition de voiture
    initEditCarButtons();
    attachEmployeeEvents();
    attachCarDeleteEvents();
});

// ========== GESTION DU MODAL D'AJOUT D'EMPLOYÉ ==========
document.addEventListener('DOMContentLoaded', function() {
    const employeeModal = document.getElementById('employeeModal');
    const openEmployeeModalBtn = document.getElementById('openEmployeeModal');
    const closeEmployeeModalBtn = document.getElementById('closeEmployeeModal');
    const cancelEmployeeModalBtn = document.getElementById('cancelEmployeeModal');
    const employeeForm = document.getElementById('employeeForm');

    // === OUVRIR LE MODAL ===
    if (openEmployeeModalBtn) {
        openEmployeeModalBtn.addEventListener('click', function() {
            console.log('Bouton cliqué - Ouverture modal');
            employeeModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }

    // === FERMER LE MODAL ===
    function closeModal() {
        employeeModal.classList.remove('active');
        document.body.style.overflow = 'auto';
        employeeForm.reset();
        clearAllErrors();
    }

    if (closeEmployeeModalBtn) {
        closeEmployeeModalBtn.addEventListener('click', closeModal);
    }
    if (cancelEmployeeModalBtn) {
        cancelEmployeeModalBtn.addEventListener('click', closeModal);
    }

    // Fermer en cliquant à l'extérieur
    employeeModal.addEventListener('click', function(e) {
        if (e.target === employeeModal) {
            closeModal();
        }
    });

    // === TOGGLE PASSWORD ===
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const input = this.parentElement.querySelector('.form-control');
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // === EFFACER LES ERREURS ===
    function clearAllErrors() {
        document.querySelectorAll('.error-message').forEach(el => {
            el.style.display = 'none';
            el.textContent = '';
        });
        document.querySelectorAll('.form-control').forEach(el => {
            el.classList.remove('error', 'valid');
        });

        const serverMsg = document.getElementById('employeeFormServerMessage');
        if (serverMsg) {
            serverMsg.style.display = 'none';
            serverMsg.textContent = '';
            serverMsg.classList.remove('error', 'success');
        }
    }

    function setError(inputId, message) {
        const input = document.getElementById(inputId);
        const errorEl = document.getElementById(inputId + 'Error');
        if (input && errorEl) {
            input.classList.add('error');
            input.classList.remove('valid');
            errorEl.textContent = message;
            errorEl.style.display = 'block';
        }
    }

    function setSuccess(inputId) {
        const input = document.getElementById(inputId);
        const errorEl = document.getElementById(inputId + 'Error');
        if (input && errorEl) {
            input.classList.remove('error');
            input.classList.add('valid');
            errorEl.style.display = 'none';
        }
    }

    // === VALIDATION SIMPLE ===
    function validateForm() {
        clearAllErrors();
        let isValid = true;

        const firstName = document.getElementById('firstName').value.trim();
        const lastName = document.getElementById('lastName').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const role = document.getElementById('role').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (!firstName) {
            setError('firstName', 'Le prénom est requis');
            isValid = false;
        } else {
            setSuccess('firstName');
        }

        if (!lastName) {
            setError('lastName', 'Le nom est requis');
            isValid = false;
        } else {
            setSuccess('lastName');
        }

        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            setError('email', 'Email invalide');
            isValid = false;
        } else {
            setSuccess('email');
        }

        if (!phone || !/^[0-9]{9}$/.test(phone.replace(/\s/g, ''))) {
            setError('phone', '9 chiffres requis');
            isValid = false;
        } else {
            setSuccess('phone');
        }

        if (!role) {
            setError('role', 'Sélectionnez un rôle');
            isValid = false;
        } else {
            setSuccess('role');
        }

        if (!password || password.length < 6) {
            setError('password', '6 caractères minimum');
            isValid = false;
        } else {
            setSuccess('password');
        }

        if (password !== confirmPassword) {
            setError('confirmPassword', 'Les mots de passe ne correspondent pas');
            isValid = false;
        } else {
            setSuccess('confirmPassword');
        }

        return isValid;
    }

    // === SOUMISSION FORMULAIRE ===
    if (employeeForm) {
        employeeForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!validateForm()) return;

            // Créer FormData
            const formData = new FormData();
            formData.append('first_name', document.getElementById('firstName').value.trim());
            formData.append('last_name', document.getElementById('lastName').value.trim());
            formData.append('email', document.getElementById('email').value.trim());
            formData.append('phone', document.getElementById('phone').value.trim());
            formData.append('role', document.getElementById('role').value);
            formData.append('password', document.getElementById('password').value);
            formData.append('confirm_password', document.getElementById('confirmPassword').value);
            formData.append('action', 'add_user');
            
            // Désactiver le bouton
            const submitBtn = employeeForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ajout en cours...';

            const serverMsg = document.getElementById('employeeFormServerMessage');
            if (serverMsg) { serverMsg.style.display = 'none'; serverMsg.textContent = ''; serverMsg.className = 'server-message'; }

            // Envoi AJAX
            const url = '../controllers/UserController.php';

            fetch(url, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(async response => {
                // Lire le corps une seule fois puis tenter de le parser en JSON
                const text = await response.text();
                const contentType = response.headers.get('Content-Type') || '';
                let data = null;
                if (text) {
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        data = null;
                    }
                }

                if (!response.ok) {
                    console.error('Server returned non-OK status', response.status, text);
                    if (data) {
                        return data; // renvoyer JSON d'erreur
                    }
                    throw new Error('Erreur serveur: ' + response.status);
                }

                return data !== null ? data : text;
            })
            .then(data => {
                if (typeof data === 'string') {
                    console.error('Réponse serveur (texte):', data);
                    if (serverMsg) {
                        serverMsg.style.display = 'block';
                        serverMsg.textContent = 'Erreur serveur (réponse inattendue)';
                        serverMsg.classList.add('error');
                    } else {
                        alert('❌ Erreur serveur (réponse inattendue)');
                    }
                    return;
                }

                if (data.success) {
                    if (serverMsg) {
                        serverMsg.style.display = 'block';
                        serverMsg.textContent = data.message || 'Employé ajouté avec succès.';
                        serverMsg.classList.add('success');
                        
                        // Masquer automatiquement après 3 secondes
                        setTimeout(() => {
                            serverMsg.style.display = 'none';
                        }, 3000);
                    }
                    
                    // Fermer le modal après un court délai et rafraîchir
                    setTimeout(() => { 
                        closeModal(); 
                        refreshEmployeesList();
                    }, 800);
                    
                } else if (data.errors) {
                    // Afficher les erreurs serveur
                    const nonFieldMessages = [];
                    for (let field in data.errors) {
                        const fieldMap = {
                            'first_name': 'firstName',
                            'last_name': 'lastName',
                            'confirm_password': 'confirmPassword',
                            'password': 'password'
                        };
                        const fieldId = fieldMap[field] || field;
                        if (document.getElementById(fieldId)) {
                            setError(fieldId, data.errors[field]);
                        } else {
                            nonFieldMessages.push(data.errors[field]);
                        }
                    }
                    if (nonFieldMessages.length && serverMsg) {
                        serverMsg.style.display = 'block';
                        serverMsg.innerHTML = nonFieldMessages.join('<br>');
                        serverMsg.classList.add('error');
                    }
                } else {
                    if (serverMsg) {
                        serverMsg.style.display = 'block';
                        serverMsg.textContent = data.message || 'Erreur lors de l\'ajout';
                        serverMsg.classList.add('error');
                    } else {
                        alert('❌ ' + (data.message || 'Erreur lors de l\'ajout'));
                    }
                }
            })
            .catch(error => {
                console.error('Erreur lors de la requête:', error);
                if (serverMsg) {
                    serverMsg.style.display = 'block';
                    serverMsg.textContent = 'Erreur serveur. Vérifiez la console pour plus de détails.';
                    serverMsg.classList.add('error');
                } else {
                    alert('❌ Erreur serveur');
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });

        // Validation en temps réel
        employeeForm.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('blur', function() {
                validateForm();
            });
            input.addEventListener('input', function() {
                const errorEl = document.getElementById(this.id + 'Error');
                if (errorEl && errorEl.style.display === 'block') {
                    validateForm();
                }
            });
        });
    }

});

// ========== GESTION DE LA SUPPRESSION D'EMPLOYÉ ==========
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteEmployeeModal');
    const closeDeleteModalBtn = document.getElementById('closeDeleteModal');
    const cancelDeleteModalBtn = document.getElementById('cancelDeleteModal');
    const confirmDeleteBtn = document.getElementById('confirmDeleteEmployee');
    const deleteEmployeeBtns = document.querySelectorAll('.delete-employee-btn');
    const deleteServerMsg = document.getElementById('deleteEmployeeServerMessage');

    let employeeToDelete = null;

    // Ouvrir la modal de confirmation de suppression
    deleteEmployeeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            employeeToDelete = {
                id: this.getAttribute('data-id'),
                name: this.getAttribute('data-name'),
                email: this.getAttribute('data-email'),
                role: this.getAttribute('data-role')
            };

            // Remplir les informations dans la modal
            document.getElementById('deleteEmployeeName').textContent = employeeToDelete.name;
            document.getElementById('deleteEmployeeEmail').textContent = employeeToDelete.email;
            document.getElementById('deleteEmployeeRole').textContent = employeeToDelete.role;

            // Afficher la modal
            deleteModal.classList.add('active');
            document.body.style.overflow = 'hidden';
            // Clear server message when opening
            if (deleteServerMsg) {
                deleteServerMsg.style.display = 'none';
                deleteServerMsg.textContent = '';
                deleteServerMsg.classList.remove('error', 'success');
            }
        });
    });

    // Fermer la modal de suppression
    function closeDeleteModal() {
        deleteModal.classList.remove('active');
        document.body.style.overflow = 'auto';
        employeeToDelete = null;
    }

    if (closeDeleteModalBtn) {
        closeDeleteModalBtn.addEventListener('click', closeDeleteModal);
    }

    if (cancelDeleteModalBtn) {
        cancelDeleteModalBtn.addEventListener('click', closeDeleteModal);
    }

    // Fermer en cliquant à l'extérieur
    deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
            closeDeleteModal();
        }
    });

    // Confirmer la suppression
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (!employeeToDelete) return;

            // Désactiver le bouton
            const originalText = confirmDeleteBtn.innerHTML;
            confirmDeleteBtn.disabled = true;
            confirmDeleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Suppression...';

            // Envoyer la requête de suppression
            const formData = new FormData();
            formData.append('employee_id', employeeToDelete.id);
            formData.append('action', 'delete_user');

            fetch(window.location.origin + '/Luxury-cars/controllers/UserController.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(async response => {
                const text = await response.text();
                let data = null;
                
                if (text) {
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        data = null;
                    }
                }

                if (!response.ok) {
                    // show error inside modal instead of throwing to be handled uniformly
                    if (deleteServerMsg) {
                        deleteServerMsg.style.display = 'block';
                        deleteServerMsg.textContent = 'Erreur serveur: ' + response.status;
                        deleteServerMsg.classList.add('error');
                    }
                    throw new Error('Erreur serveur: ' + response.status);
                }

                return data !== null ? data : text;
            })
            .then(data => {
                if (typeof data === 'string') {
                    if (deleteServerMsg) {
                        deleteServerMsg.style.display = 'block';
                        deleteServerMsg.textContent = 'Erreur serveur (réponse inattendue)';
                        deleteServerMsg.classList.add('error');
                    }
                    return;
                }

                if (data.success) {
                    if (deleteServerMsg) {
                        deleteServerMsg.style.display = 'block';
                        deleteServerMsg.textContent = data.message || 'Employé supprimé avec succès.';
                        deleteServerMsg.classList.remove('error');
                        deleteServerMsg.classList.add('success');
                        
                        // Masquer automatiquement après 3 secondes
                        setTimeout(() => {
                            deleteServerMsg.style.display = 'none';
                        }, 3000);
                    }
                    
                    // Fermer le modal et rafraîchir la liste après court délai
                    setTimeout(() => { 
                        closeDeleteModal(); 
                        refreshEmployeesList();
                    }, 700);
                    
                } else {
                    if (deleteServerMsg) {
                        deleteServerMsg.style.display = 'block';
                        deleteServerMsg.textContent = data.message || 'Erreur lors de la suppression';
                        deleteServerMsg.classList.add('error');
                    }
                }
            })
            .catch(error => {
                console.error('Erreur lors de la suppression:', error);
                if (deleteServerMsg) {
                    deleteServerMsg.style.display = 'block';
                    deleteServerMsg.textContent = 'Erreur serveur lors de la suppression. Vérifiez la console pour plus de détails.';
                    deleteServerMsg.classList.add('error');
                }
            })
            .finally(() => {
                confirmDeleteBtn.disabled = false;
                confirmDeleteBtn.innerHTML = originalText;
            });
        });
    }
});

// ========== GESTION DE LA MODIFICATION D'EMPLOYÉ ==========
document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('editEmployeeModal');
    const closeEditModalBtn = document.getElementById('closeEditModal');
    const cancelEditModalBtn = document.getElementById('cancelEditModal');
    const editEmployeeForm = document.getElementById('editEmployeeForm');
    const editEmployeeBtns = document.querySelectorAll('.edit-employee-btn');

    let currentEditingEmployee = null;

    // Ouvrir la modal de modification
    editEmployeeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            currentEditingEmployee = {
                id: this.getAttribute('data-id'),
                first_name: this.getAttribute('data-firstname'),
                last_name: this.getAttribute('data-lastname'),
                email: this.getAttribute('data-email'),
                phone: this.getAttribute('data-phone'),
                role: this.getAttribute('data-role')
            };

            // Remplir le formulaire avec les données actuelles
            document.getElementById('editEmployeeId').value = currentEditingEmployee.id;
            document.getElementById('editFirstName').value = currentEditingEmployee.first_name;
            document.getElementById('editLastName').value = currentEditingEmployee.last_name;
            document.getElementById('editEmail').value = currentEditingEmployee.email;
            document.getElementById('editPhone').value = currentEditingEmployee.phone;
            document.getElementById('editRole').value = currentEditingEmployee.role;
            
            // Réinitialiser les champs mot de passe
            document.getElementById('editPassword').value = '';
            document.getElementById('editConfirmPassword').value = '';

            // Effacer les erreurs
            clearEditFormErrors();
            
            // Afficher la modal
            editModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });

    // Fermer la modal de modification
    function closeEditModal() {
        editModal.classList.remove('active');
        document.body.style.overflow = 'auto';
        currentEditingEmployee = null;
        editEmployeeForm.reset();
        clearEditFormErrors();
    }

    if (closeEditModalBtn) {
        closeEditModalBtn.addEventListener('click', closeEditModal);
    }

    if (cancelEditModalBtn) {
        cancelEditModalBtn.addEventListener('click', closeEditModal);
    }

    // Fermer en cliquant à l'extérieur
    editModal.addEventListener('click', function(e) {
        if (e.target === editModal) {
            closeEditModal();
        }
    });

    // Effacer les erreurs du formulaire d'édition
    function clearEditFormErrors() {
        document.querySelectorAll('#editEmployeeForm .error-message').forEach(el => {
            el.style.display = 'none';
            el.textContent = '';
        });
        document.querySelectorAll('#editEmployeeForm .form-control').forEach(el => {
            el.classList.remove('error', 'valid');
        });

        const serverMsg = document.getElementById('editFormServerMessage');
        if (serverMsg) {
            serverMsg.style.display = 'none';
            serverMsg.textContent = '';
            serverMsg.classList.remove('error', 'success');
        }
    }

    function setEditError(inputId, message) {
        const input = document.getElementById(inputId);
        const errorEl = document.getElementById(inputId + 'Error');
        if (input && errorEl) {
            input.classList.add('error');
            input.classList.remove('valid');
            errorEl.textContent = message;
            errorEl.style.display = 'block';
        }
    }

    function setEditSuccess(inputId) {
        const input = document.getElementById(inputId);
        const errorEl = document.getElementById(inputId + 'Error');
        if (input && errorEl) {
            input.classList.remove('error');
            input.classList.add('valid');
            errorEl.style.display = 'none';
        }
    }

    // Validation du formulaire d'édition
    function validateEditForm() {
        clearEditFormErrors();
        let isValid = true;

        const firstName = document.getElementById('editFirstName').value.trim();
        const lastName = document.getElementById('editLastName').value.trim();
        const email = document.getElementById('editEmail').value.trim();
        const phone = document.getElementById('editPhone').value.trim();
        const role = document.getElementById('editRole').value;
        const password = document.getElementById('editPassword').value;
        const confirmPassword = document.getElementById('editConfirmPassword').value;

        // Validation des champs requis
        if (!firstName) {
            setEditError('editFirstName', 'Le prénom est requis');
            isValid = false;
        } else {
            setEditSuccess('editFirstName');
        }

        if (!lastName) {
            setEditError('editLastName', 'Le nom est requis');
            isValid = false;
        } else {
            setEditSuccess('editLastName');
        }

        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            setEditError('editEmail', 'Email invalide');
            isValid = false;
        } else {
            setEditSuccess('editEmail');
        }

        if (!phone || !/^[0-9]{9}$/.test(phone.replace(/\s/g, ''))) {
            setEditError('editPhone', '9 chiffres requis');
            isValid = false;
        } else {
            setEditSuccess('editPhone');
        }

        if (!role) {
            setEditError('editRole', 'Sélectionnez un rôle');
            isValid = false;
        } else {
            setEditSuccess('editRole');
        }

        // Validation conditionnelle des mots de passe
        if (password || confirmPassword) {
            if (password.length < 6) {
                setEditError('editPassword', '6 caractères minimum');
                isValid = false;
            } else {
                setEditSuccess('editPassword');
            }

            if (password !== confirmPassword) {
                setEditError('editConfirmPassword', 'Les mots de passe ne correspondent pas');
                isValid = false;
            } else {
                setEditSuccess('editConfirmPassword');
            }
        }

        return isValid;
    }

    // Soumission du formulaire d'édition
    if (editEmployeeForm) {
        editEmployeeForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!validateEditForm()) return;

            // Créer FormData
            const formData = new FormData();
            formData.append('employee_id', document.getElementById('editEmployeeId').value);
            formData.append('first_name', document.getElementById('editFirstName').value.trim());
            formData.append('last_name', document.getElementById('editLastName').value.trim());
            formData.append('email', document.getElementById('editEmail').value.trim());
            formData.append('phone', document.getElementById('editPhone').value.trim());
            formData.append('role', document.getElementById('editRole').value);
            formData.append('password', document.getElementById('editPassword').value);
            formData.append('confirm_password', document.getElementById('editConfirmPassword').value);
            formData.append('action', 'update_user');

            // Désactiver le bouton
            const submitBtn = editEmployeeForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';

            const serverMsg = document.getElementById('editFormServerMessage');
            if (serverMsg) { 
                serverMsg.style.display = 'none'; 
                serverMsg.textContent = ''; 
                serverMsg.className = 'server-message'; 
            }

            // Envoi AJAX
            const url = window.location.origin + '/Luxury-cars/controllers/UserController.php';

            fetch(url, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(async response => {
                const text = await response.text();
                const contentType = response.headers.get('Content-Type') || '';
                let data = null;
                if (text) {
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        data = null;
                    }
                }

                if (!response.ok) {
                    console.error('Server returned non-OK status', response.status, text);
                    if (data) {
                        return data;
                    }
                    throw new Error('Erreur serveur: ' + response.status);
                }

                return data !== null ? data : text;
            })
            .then(data => {
                if (typeof data === 'string') {
                    console.error('Réponse serveur (texte):', data);
                    if (serverMsg) {
                        serverMsg.style.display = 'block';
                        serverMsg.textContent = 'Erreur serveur (réponse inattendue)';
                        serverMsg.classList.add('error');
                    } else {
                        alert('❌ Erreur serveur (réponse inattendue)');
                    }
                    return;
                }

                if (data.success) {
                    if (serverMsg) {
                        serverMsg.style.display = 'block';
                        serverMsg.textContent = data.message || 'Employé modifié avec succès.';
                        serverMsg.classList.add('success');
                        
                        // Masquer automatiquement après 3 secondes
                        setTimeout(() => {
                            serverMsg.style.display = 'none';
                        }, 3000);
                    }
                    
                    // Fermer le modal après un court délai et rafraîchir
                    setTimeout(() => { 
                        closeEditModal(); 
                        refreshEmployeesList();
                    }, 800);
                    
                } else if (data.errors) {
                    // Afficher les erreurs serveur
                    const nonFieldMessages = [];
                    for (let field in data.errors) {
                        const fieldMap = {
                            'first_name': 'editFirstName',
                            'last_name': 'editLastName',
                            'confirm_password': 'editConfirmPassword',
                            'password': 'editPassword'
                        };
                        const fieldId = fieldMap[field] || ('edit' + field.charAt(0).toUpperCase() + field.slice(1));
                        if (document.getElementById(fieldId)) {
                            setEditError(fieldId, data.errors[field]);
                        } else {
                            nonFieldMessages.push(data.errors[field]);
                        }
                    }
                    if (nonFieldMessages.length && serverMsg) {
                        serverMsg.style.display = 'block';
                        serverMsg.innerHTML = nonFieldMessages.join('<br>');
                        serverMsg.classList.add('error');
                    }
                } else {
                    if (serverMsg) {
                        serverMsg.style.display = 'block';
                        serverMsg.textContent = data.message || 'Erreur lors de la modification';
                        serverMsg.classList.add('error');
                    } else {
                        alert('❌ ' + (data.message || 'Erreur lors de la modification'));
                    }
                }
            })
            .catch(error => {
                console.error('Erreur lors de la modification:', error);
                if (serverMsg) {
                    serverMsg.style.display = 'block';
                    serverMsg.textContent = 'Erreur serveur. Vérifiez la console pour plus de détails.';
                    serverMsg.classList.add('error');
                } else {
                    alert('❌ Erreur serveur');
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });

        // Validation en temps réel pour l'édition
        editEmployeeForm.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('blur', function() {
                validateEditForm();
            });
            input.addEventListener('input', function() {
                const errorEl = document.getElementById(this.id + 'Error');
                if (errorEl && errorEl.style.display === 'block') {
                    validateEditForm();
                }
            });
        });
    }
});

// ========== GESTION DES VOITURES ET CATÉGORIES ==========
document.addEventListener('DOMContentLoaded', function() {
    // === VARIABLES ===
    const addCarModal = document.getElementById('addCarModal');
    const addCategoryModal = document.getElementById('addCategoryModal');
    const viewCategoriesModal = document.getElementById('viewCategoriesModal');
    const addBrandModal = document.getElementById('addBrandModal');
    const viewBrandsModal = document.getElementById('viewBrandsModal');
    const deleteCategoryModal = document.getElementById('deleteCategoryModal');
    const deleteBrandModal = document.getElementById('deleteBrandModal');
    const editCarModal = document.getElementById('editCarModal');
    const deleteCarModal = document.getElementById('deleteCarModal');
    
    // Boutons d'ouverture
    const openCarModalBtn = document.getElementById('openCarModal');
    const openBrandModalBtn = document.getElementById('openBrandModal');
    const openViewBrandsModalBtn = document.getElementById('openViewBrandsModal');
    const openCategoryModalBtn = document.getElementById('openCategoryModal');
    const openViewCategoriesModalBtn = document.getElementById('openViewCategoriesModal');
    
    // Boutons de fermeture
    const closeCarModalBtn = document.getElementById('closeCarModal');
    const closeBrandModalBtn = document.getElementById('closeBrandModal');
    const closeViewBrandsModalBtn = document.getElementById('closeViewBrandsModal');
    const closeCategoryModalBtn = document.getElementById('closeCategoryModal');
    const closeViewCategoriesModalBtn = document.getElementById('closeViewCategoriesModal');
    const closeViewCategoriesBtn = document.getElementById('closeViewCategoriesBtn');
    const closeViewBrandsBtn = document.getElementById('closeViewBrandsBtn');
    
    // Boutons d'annulation
    const cancelCarModalBtn = document.getElementById('cancelCarModal');
    const cancelBrandModalBtn = document.getElementById('cancelBrandModal');
    const cancelCategoryModalBtn = document.getElementById('cancelCategoryModal');
    
    // Formulaires
    const addCarForm = document.getElementById('addCarForm');
    const addBrandForm = document.getElementById('addBrandForm');
    const addCategoryForm = document.getElementById('addCategoryForm');

    const quickAddBrandBtn = document.getElementById('quickAddBrandBtn');

    // Variables pour la suppression des marques
    const deleteBrandNameEl = document.getElementById('deleteBrandName');
    const confirmDeleteBrandBtn = document.getElementById('confirmDeleteBrand');
    const cancelDeleteBrandBtn = document.getElementById('cancelDeleteBrand');
    const closeDeleteBrandModalBtn = document.getElementById('closeDeleteBrandModal');
    const deleteBrandServerMsg = document.getElementById('deleteBrandServerMessage');

    let selectedBrandToDelete = null;

    // === FONCTIONS GÉNÉRIQUES ===
    function closeAllModals() {
        // fermer tous les modals connus (inclure marques et catégories)
        [addCarModal, addCategoryModal, viewCategoriesModal, addBrandModal, viewBrandsModal, deleteCategoryModal, deleteBrandModal, editCarModal, deleteCarModal].forEach(modal => {
            if (modal) modal.classList.remove('active');
        });
        document.body.style.overflow = 'auto';
    }

    function clearFormErrors(formId) {
        const form = document.getElementById(formId);
        if (form) {
            form.querySelectorAll('.error-message').forEach(el => {
                el.style.display = 'none';
                el.textContent = '';
            });
            form.querySelectorAll('.form-control').forEach(el => {
                el.classList.remove('error', 'valid');
            });
            
            const serverMsg = form.parentElement.querySelector('.server-message');
            if (serverMsg) {
                serverMsg.style.display = 'none';
                serverMsg.textContent = '';
                serverMsg.className = 'server-message';
            }
        }
    }

    function setFormError(formId, inputId, message) {
        const input = document.getElementById(inputId);
        const errorEl = document.getElementById(inputId + 'Error');
        if (input && errorEl) {
            input.classList.add('error');
            input.classList.remove('valid');
            errorEl.textContent = message;
            errorEl.style.display = 'block';
        }
    }

    function setFormSuccess(formId, inputId) {
        const input = document.getElementById(inputId);
        const errorEl = document.getElementById(inputId + 'Error');
        if (input && errorEl) {
            input.classList.remove('error');
            input.classList.add('valid');
            errorEl.style.display = 'none';
        }
    }

    function showServerMessage(elementId, message, type) {
        const element = document.getElementById(elementId);
        if (element) {
            element.textContent = message;
            element.style.display = 'block';
            element.className = 'server-message ' + type;
            
            // Masquer automatiquement après 5 secondes pour les succès et 8 secondes pour les erreurs
            const timeout = type === 'success' ? 3000 : 8000;
            setTimeout(() => {
                if (element.textContent === message) { // Vérifier que le message n'a pas changé
                    element.style.display = 'none';
                    element.textContent = '';
                }
            }, timeout);
        }
    }

    // === GESTION DES IMAGES MULTIPLES POUR LES VOITURES ===
    function initImageUpload() {
        const imageUploadArea = document.getElementById('imageUploadArea');
        const carImagesInput = document.getElementById('carImages');
        const selectImagesBtn = document.getElementById('selectImagesBtn');
        const imagePreview = document.getElementById('imagePreview');
        const imageCounter = document.getElementById('imageCounter');
        const currentImageCount = document.getElementById('currentImageCount');

        if (!imageUploadArea || !carImagesInput) return;

        let uploadedFiles = [];

        // Ouvrir le sélecteur de fichiers
        selectImagesBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            carImagesInput.click();
        });

        // Clic sur la zone de drop
        imageUploadArea.addEventListener('click', () => {
            carImagesInput.click();
        });

        // Drag and drop
        imageUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            imageUploadArea.style.borderColor = '#2ecc71';
            imageUploadArea.style.backgroundColor = 'rgba(46, 204, 113, 0.1)';
        });

        imageUploadArea.addEventListener('dragleave', () => {
            imageUploadArea.style.borderColor = 'rgb(139, 137, 137)';
            imageUploadArea.style.backgroundColor = 'transparent';
        });

        imageUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            imageUploadArea.style.borderColor = 'rgb(139, 137, 137)';
            imageUploadArea.style.backgroundColor = 'transparent';
            
            if (e.dataTransfer.files.length > 0) {
                handleImageSelection(e.dataTransfer.files);
            }
        });

        // Sélection via input file
        carImagesInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleImageSelection(e.target.files);
            }
        });

        function handleImageSelection(files) {
            const availableSlots = 5 - uploadedFiles.length;
            
            if (files.length > availableSlots) {
                showServerMessage('carFormServerMessage', `Vous ne pouvez ajouter que ${availableSlots} image(s) supplémentaire(s)`, 'error');
                return;
            }

            for (let i = 0; i < Math.min(files.length, availableSlots); i++) {
                const file = files[i];
                
                // Validation du type de fichier
                if (!file.type.startsWith('image/')) {
                    showServerMessage('carFormServerMessage', `Le fichier "${file.name}" n'est pas une image valide`, 'error');
                    continue;
                }

                // Validation de la taille (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    showServerMessage('carFormServerMessage', `L'image "${file.name}" est trop volumineuse (max 5MB)`, 'error');
                    continue;
                }

                // Vérifier si le fichier n'est pas déjà uploadé
                const isDuplicate = uploadedFiles.some(existingFile => 
                    existingFile.name === file.name && existingFile.size === file.size
                );

                if (!isDuplicate) {
                    uploadedFiles.push(file);
                    createImagePreview(file, uploadedFiles.length - 1);
                }
            }

            updateImageCounter();
            updateFileInput();
        }

        function createImagePreview(file, index) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const previewContainer = document.createElement('div');
                previewContainer.className = 'preview-image';
                previewContainer.style.position = 'relative';
                previewContainer.style.borderRadius = '8px';
                previewContainer.style.overflow = 'hidden';
                previewContainer.style.border = '1px solid rgb(139, 137, 137)';
                previewContainer.style.background = 'rgba(0, 0, 0, 0.6)';

                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100%';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                img.style.display = 'block';

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                removeBtn.style.position = 'absolute';
                removeBtn.style.top = '5px';
                removeBtn.style.right = '5px';
                removeBtn.style.background = 'rgba(231, 76, 60, 0.9)';
                removeBtn.style.border = 'none';
                removeBtn.style.borderRadius = '50%';
                removeBtn.style.width = '24px';
                removeBtn.style.height = '24px';
                removeBtn.style.color = 'white';
                removeBtn.style.cursor = 'pointer';
                removeBtn.style.display = 'flex';
                removeBtn.style.alignItems = 'center';
                removeBtn.style.justifyContent = 'center';
                removeBtn.style.fontSize = '12px';
                removeBtn.style.transition = 'all 0.2s ease';

                removeBtn.addEventListener('mouseenter', () => {
                    removeBtn.style.background = 'rgba(231, 76, 60, 1)';
                    removeBtn.style.transform = 'scale(1.1)';
                });

                removeBtn.addEventListener('mouseleave', () => {
                    removeBtn.style.background = 'rgba(231, 76, 60, 0.9)';
                    removeBtn.style.transform = 'scale(1)';
                });

                removeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    uploadedFiles.splice(index, 1);
                    previewContainer.remove();
                    updateImageCounter();
                    updateFileInput();
                    // Recréer tous les previews pour mettre à jour les index
                    recreatePreviews();
                });

                const imageInfo = document.createElement('div');
                imageInfo.style.position = 'absolute';
                imageInfo.style.bottom = '0';
                imageInfo.style.left = '0';
                imageInfo.style.right = '0';
                imageInfo.style.background = 'rgba(0, 0, 0, 0.7)';
                imageInfo.style.color = 'white';
                imageInfo.style.padding = '2px 5px';
                imageInfo.style.fontSize = '10px';
                imageInfo.style.textOverflow = 'ellipsis';
                imageInfo.style.overflow = 'hidden';
                imageInfo.style.whiteSpace = 'nowrap';
                imageInfo.textContent = file.name;

                previewContainer.appendChild(img);
                previewContainer.appendChild(removeBtn);
                previewContainer.appendChild(imageInfo);
                imagePreview.appendChild(previewContainer);
                
                imagePreview.style.display = 'grid';
            };

            reader.readAsDataURL(file);
        }

        function recreatePreviews() {
            imagePreview.innerHTML = '';
            uploadedFiles.forEach((file, index) => {
                createImagePreview(file, index);
            });
            updateImageCounter();
        }

        function updateImageCounter() {
            const count = uploadedFiles.length;
            currentImageCount.textContent = count;
            
            if (count === 0) {
                imagePreview.style.display = 'none';
                imageCounter.style.color = '#888';
            } else if (count === 5) {
                imageCounter.style.color = '#2ecc71';
            } else {
                imageCounter.style.color = '#f39c12';
            }
        }

        function updateFileInput() {
            // Créer un nouveau DataTransfer pour mettre à jour les fichiers
            const dataTransfer = new DataTransfer();
            uploadedFiles.forEach(file => {
                dataTransfer.items.add(file);
            });
            carImagesInput.files = dataTransfer.files;
        }

        // Réinitialiser les images quand le modal est fermé
        document.getElementById('closeCarModal')?.addEventListener('click', resetImageUpload);
        document.getElementById('cancelCarModal')?.addEventListener('click', resetImageUpload);

        function resetImageUpload() {
            imagePreview.innerHTML = '';
            imagePreview.style.display = 'none';
            carImagesInput.value = '';
            uploadedFiles = [];
            updateImageCounter();
        }
    }

    // === GESTION DES IMAGES MULTIPLES POUR L'ÉDITION DE VOITURE ===
    function initEditImageUpload() {
        const editImageUploadArea = document.getElementById('editImageUploadArea');
        const editCarImagesInput = document.getElementById('editCarImages');
        const editSelectImagesBtn = document.getElementById('editSelectImagesBtn');
        const editImagePreview = document.getElementById('editImagePreview');
        const editImageCounter = document.getElementById('editImageCounter');
        const editCurrentImageCount = document.getElementById('editCurrentImageCount');
        const editCurrentImagesPreview = document.getElementById('editCurrentImagesPreview');

        if (!editImageUploadArea || !editCarImagesInput) return;

        let existingImages = [];
        let newUploadedFiles = [];
        let mainImage = null;

        // Créer un input pour l'image principale si il n'existe pas
        let editMainImageInput = document.getElementById('editMainImage');
        if (!editMainImageInput) {
            const mainImageInput = document.createElement('input');
            mainImageInput.type = 'file';
            mainImageInput.id = 'editMainImage';
            mainImageInput.name = 'main_image';
            mainImageInput.accept = 'image/jpeg,image/png,image/webp';
            mainImageInput.style.display = 'none';
            document.getElementById('editCarForm').appendChild(mainImageInput);
            editMainImageInput = mainImageInput;
        }

        // Fonction pour charger l'image principale et les images existantes
        function loadExistingImages(carId, mainImageUrl) {
            // Stocker l'image principale
            mainImage = {
                id: 'main',
                image_url: mainImageUrl,
                is_main: true
            };

            // Afficher l'image principale
            displayMainImage();

            // Charger les images supplémentaires
            fetch(window.location.origin + '/Luxury-cars/controllers/CarController.php?action=get_car_images&car_id=' + carId)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.images) {
                        existingImages = data.images;
                        displayExistingImages();
                        updateEditImageCounter();
                    }
                })
                .catch(error => console.error('Erreur chargement images:', error));
        }

        function displayMainImage() {
            if (!mainImage) return;

            const previewContainer = document.createElement('div');
            previewContainer.className = 'preview-image main-image';
            previewContainer.style.position = 'relative';
            previewContainer.style.borderRadius = '8px';
            previewContainer.style.overflow = 'hidden';
            previewContainer.style.border = '2px solid #2ecc71'; // Bordure verte pour l'image principale
            previewContainer.style.background = 'rgba(0, 0, 0, 0.6)';

            const img = document.createElement('img');
            img.src = window.location.origin + '/Luxury-cars/public/' + mainImage.image_url;
            img.style.width = '100%';
            img.style.height = '100px';
            img.style.objectFit = 'cover';
            img.style.display = 'block';

            const mainBadge = document.createElement('div');
            mainBadge.innerHTML = '<i class="fas fa-star"></i> Principale';
            mainBadge.style.position = 'absolute';
            mainBadge.style.top = '5px';
            mainBadge.style.left = '5px';
            mainBadge.style.background = 'rgba(46, 204, 113, 0.9)';
            mainBadge.style.color = 'white';
            mainBadge.style.padding = '2px 6px';
            mainBadge.style.borderRadius = '4px';
            mainBadge.style.fontSize = '10px';
            mainBadge.style.fontWeight = 'bold';

            const changeBtn = document.createElement('button');
            changeBtn.type = 'button';
            changeBtn.innerHTML = '<i class="fas fa-sync-alt"></i>';
            changeBtn.title = 'Changer l\'image principale';
            changeBtn.style.position = 'absolute';
            changeBtn.style.top = '5px';
            changeBtn.style.right = '5px';
            changeBtn.style.background = 'rgba(52, 152, 219, 0.9)';
            changeBtn.style.border = 'none';
            changeBtn.style.borderRadius = '50%';
            changeBtn.style.width = '24px';
            changeBtn.style.height = '24px';
            changeBtn.style.color = 'white';
            changeBtn.style.cursor = 'pointer';
            changeBtn.style.display = 'flex';
            changeBtn.style.alignItems = 'center';
            changeBtn.style.justifyContent = 'center';
            changeBtn.style.fontSize = '12px';

            changeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                editMainImageInput.click();
            });

            const imageInfo = document.createElement('div');
            imageInfo.style.position = 'absolute';
            imageInfo.style.bottom = '0';
            imageInfo.style.left = '0';
            imageInfo.style.right = '0';
            imageInfo.style.background = 'rgba(0, 0, 0, 0.7)';
            imageInfo.style.color = 'white';
            imageInfo.style.padding = '2px 5px';
            imageInfo.style.fontSize = '10px';
            imageInfo.style.textOverflow = 'ellipsis';
            imageInfo.style.overflow = 'hidden';
            imageInfo.style.whiteSpace = 'nowrap';
            imageInfo.textContent = 'Image principale';

            previewContainer.appendChild(img);
            previewContainer.appendChild(mainBadge);
            previewContainer.appendChild(changeBtn);
            previewContainer.appendChild(imageInfo);
            
            // Ajouter au début du conteneur
            editCurrentImagesPreview.insertBefore(previewContainer, editCurrentImagesPreview.firstChild);
        }

        function displayExistingImages() {
            existingImages.forEach((image, index) => {
                const previewContainer = document.createElement('div');
                previewContainer.className = 'preview-image additional-image';
                previewContainer.style.position = 'relative';
                previewContainer.style.borderRadius = '8px';
                previewContainer.style.overflow = 'hidden';
                previewContainer.style.border = '1px solid rgb(139, 137, 137)';
                previewContainer.style.background = 'rgba(0, 0, 0, 0.6)';

                const img = document.createElement('img');
                img.src = window.location.origin + '/Luxury-cars/public/' + image.image_url;
                img.style.width = '100%';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                img.style.display = 'block';

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                removeBtn.style.position = 'absolute';
                removeBtn.style.top = '5px';
                removeBtn.style.right = '5px';
                removeBtn.style.background = 'rgba(231, 76, 60, 0.9)';
                removeBtn.style.border = 'none';
                removeBtn.style.borderRadius = '50%';
                removeBtn.style.width = '24px';
                removeBtn.style.height = '24px';
                removeBtn.style.color = 'white';
                removeBtn.style.cursor = 'pointer';
                removeBtn.style.display = 'flex';
                removeBtn.style.alignItems = 'center';
                removeBtn.style.justifyContent = 'center';
                removeBtn.style.fontSize = '12px';

                removeBtn.addEventListener('mouseenter', () => {
                    removeBtn.style.background = 'rgba(231, 76, 60, 1)';
                    removeBtn.style.transform = 'scale(1.1)';
                });

                removeBtn.addEventListener('mouseleave', () => {
                    removeBtn.style.background = 'rgba(231, 76, 60, 0.9)';
                    removeBtn.style.transform = 'scale(1)';
                });

                removeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (confirm('Supprimer cette image ?')) {
                        deleteExistingImage(image.id, previewContainer);
                    }
                });

                const imageInfo = document.createElement('div');
                imageInfo.style.position = 'absolute';
                imageInfo.style.bottom = '0';
                imageInfo.style.left = '0';
                imageInfo.style.right = '0';
                imageInfo.style.background = 'rgba(0, 0, 0, 0.7)';
                imageInfo.style.color = 'white';
                imageInfo.style.padding = '2px 5px';
                imageInfo.style.fontSize = '10px';
                imageInfo.style.textOverflow = 'ellipsis';
                imageInfo.style.overflow = 'hidden';
                imageInfo.style.whiteSpace = 'nowrap';
                imageInfo.textContent = 'Image supplémentaire';

                previewContainer.appendChild(img);
                previewContainer.appendChild(removeBtn);
                previewContainer.appendChild(imageInfo);
                editCurrentImagesPreview.appendChild(previewContainer);
            });
        }

        // Gestion du changement d'image principale
        editMainImageInput.addEventListener('change', function(e) {
            if (this.files.length > 0) {
                const file = this.files[0];
                
                // Validation
                if (!file.type.startsWith('image/')) {
                    showServerMessage('editCarServerMessage', 'Le fichier sélectionné n\'est pas une image valide', 'error');
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    showServerMessage('editCarServerMessage', 'L\'image est trop volumineuse (max 5MB)', 'error');
                    return;
                }

                // Aperçu de la nouvelle image principale
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Mettre à jour l'aperçu de l'image principale
                    const mainImageContainer = document.querySelector('.main-image');
                    if (mainImageContainer) {
                        mainImageContainer.querySelector('img').src = e.target.result;
                    }
                    showServerMessage('editCarServerMessage', 'Nouvelle image principale sélectionnée. Enregistrez pour appliquer les modifications.', 'success');
                };
                reader.readAsDataURL(file);
            }
        });

        // Le reste du code pour les images supplémentaires reste inchangé...
        function handleEditImageSelection(files) {
            const totalImages = (mainImage ? 1 : 0) + existingImages.length + newUploadedFiles.length;
            const availableSlots = 5 - totalImages;
            
            if (files.length > availableSlots) {
                showServerMessage('editCarServerMessage', `Vous ne pouvez ajouter que ${availableSlots} image(s) supplémentaire(s)`, 'error');
                return;
            }

            for (let i = 0; i < Math.min(files.length, availableSlots); i++) {
                const file = files[i];
                
                // Validation du type de fichier
                if (!file.type.startsWith('image/')) {
                    showServerMessage('editCarServerMessage', `Le fichier "${file.name}" n'est pas une image valide`, 'error');
                    continue;
                }

                // Validation de la taille (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    showServerMessage('editCarServerMessage', `L'image "${file.name}" est trop volumineuse (max 5MB)`, 'error');
                    continue;
                }

                // Vérifier si le fichier n'est pas déjà uploadé
                const isDuplicate = newUploadedFiles.some(existingFile => 
                    existingFile.name === file.name && existingFile.size === file.size
                );

                if (!isDuplicate) {
                    newUploadedFiles.push(file);
                    createEditImagePreview(file, newUploadedFiles.length - 1);
                }
            }

            updateEditImageCounter();
            updateEditFileInput();
        }

        function createEditImagePreview(file, index) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const previewContainer = document.createElement('div');
                previewContainer.className = 'preview-image';
                previewContainer.style.position = 'relative';
                previewContainer.style.borderRadius = '8px';
                previewContainer.style.overflow = 'hidden';
                previewContainer.style.border = '1px solid rgb(139, 137, 137)';
                previewContainer.style.background = 'rgba(0, 0, 0, 0.6)';

                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100%';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                img.style.display = 'block';

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                removeBtn.style.position = 'absolute';
                removeBtn.style.top = '5px';
                removeBtn.style.right = '5px';
                removeBtn.style.background = 'rgba(231, 76, 60, 0.9)';
                removeBtn.style.border = 'none';
                removeBtn.style.borderRadius = '50%';
                removeBtn.style.width = '24px';
                removeBtn.style.height = '24px';
                removeBtn.style.color = 'white';
                removeBtn.style.cursor = 'pointer';
                removeBtn.style.display = 'flex';
                removeBtn.style.alignItems = 'center';
                removeBtn.style.justifyContent = 'center';
                removeBtn.style.fontSize = '12px';
                removeBtn.style.transition = 'all 0.2s ease';

                removeBtn.addEventListener('mouseenter', () => {
                    removeBtn.style.background = 'rgba(231, 76, 60, 1)';
                    removeBtn.style.transform = 'scale(1.1)';
                });

                removeBtn.addEventListener('mouseleave', () => {
                    removeBtn.style.background = 'rgba(231, 76, 60, 0.9)';
                    removeBtn.style.transform = 'scale(1)';
                });

                removeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    newUploadedFiles.splice(index, 1);
                    previewContainer.remove();
                    updateEditImageCounter();
                    updateEditFileInput();
                    recreateEditPreviews();
                });

                const imageInfo = document.createElement('div');
                imageInfo.style.position = 'absolute';
                imageInfo.style.bottom = '0';
                imageInfo.style.left = '0';
                imageInfo.style.right = '0';
                imageInfo.style.background = 'rgba(0, 0, 0, 0.7)';
                imageInfo.style.color = 'white';
                imageInfo.style.padding = '2px 5px';
                imageInfo.style.fontSize = '10px';
                imageInfo.style.textOverflow = 'ellipsis';
                imageInfo.style.overflow = 'hidden';
                imageInfo.style.whiteSpace = 'nowrap';
                imageInfo.textContent = file.name;

                previewContainer.appendChild(img);
                previewContainer.appendChild(removeBtn);
                previewContainer.appendChild(imageInfo);
                editImagePreview.appendChild(previewContainer);
                
                editImagePreview.style.display = 'grid';
            };

            reader.readAsDataURL(file);
        }

        function recreateEditPreviews() {
            editImagePreview.innerHTML = '';
            newUploadedFiles.forEach((file, index) => {
                createEditImagePreview(file, index);
            });
            updateEditImageCounter();
        }

        function updateEditImageCounter() {
            const totalCount = (mainImage ? 1 : 0) + existingImages.length + newUploadedFiles.length;
            editCurrentImageCount.textContent = totalCount;
            
            if (totalCount === 0) {
                editImagePreview.style.display = 'none';
                editImageCounter.style.color = '#888';
            } else if (totalCount === 5) {
                editImageCounter.style.color = '#2ecc71';
            } else {
                editImageCounter.style.color = '#f39c12';
            }
        }

        function updateEditFileInput() {
            const dataTransfer = new DataTransfer();
            newUploadedFiles.forEach(file => {
                dataTransfer.items.add(file);
            });
            editCarImagesInput.files = dataTransfer.files;
        }

        function deleteExistingImage(imageId, previewElement) {
            const formData = new FormData();
            formData.append('action', 'delete_car_image');
            formData.append('image_id', imageId);

            fetch(window.location.origin + '/Luxury-cars/controllers/CarController.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    previewElement.remove();
                    existingImages = existingImages.filter(img => img.id !== imageId);
                    updateEditImageCounter();
                    showServerMessage('editCarServerMessage', 'Image supprimée avec succès', 'success');
                } else {
                    showServerMessage('editCarServerMessage', data.message || 'Erreur lors de la suppression', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur suppression image:', error);
                showServerMessage('editCarServerMessage', 'Erreur lors de la suppression', 'error');
            });
        }

        // Réinitialiser quand le modal est fermé
        document.getElementById('closeEditCarModal')?.addEventListener('click', resetEditImageUpload);
        document.getElementById('cancelEditCarModal')?.addEventListener('click', resetEditImageUpload);

        function resetEditImageUpload() {
            editImagePreview.innerHTML = '';
            editImagePreview.style.display = 'none';
            editCurrentImagesPreview.innerHTML = '';
            editCarImagesInput.value = '';
            editMainImageInput.value = '';
            existingImages = [];
            newUploadedFiles = [];
            mainImage = null;
            updateEditImageCounter();
        }

        // Événements pour les images supplémentaires
        editSelectImagesBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            editCarImagesInput.click();
        });

        editImageUploadArea.addEventListener('click', () => {
            editCarImagesInput.click();
        });

        editImageUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            editImageUploadArea.style.borderColor = '#2ecc71';
            editImageUploadArea.style.backgroundColor = 'rgba(46, 204, 113, 0.1)';
        });

        editImageUploadArea.addEventListener('dragleave', () => {
            editImageUploadArea.style.borderColor = 'rgb(139, 137, 137)';
            editImageUploadArea.style.backgroundColor = 'transparent';
        });

        editImageUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            editImageUploadArea.style.borderColor = 'rgb(139, 137, 137)';
            editImageUploadArea.style.backgroundColor = 'transparent';
            
            if (e.dataTransfer.files.length > 0) {
                handleEditImageSelection(e.dataTransfer.files);
            }
        });

        editCarImagesInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleEditImageSelection(e.target.files);
            }
        });

        return {
            loadExistingImages: function(carId, mainImageUrl) {
                loadExistingImages(carId, mainImageUrl);
            },
            resetEditImageUpload
        };
    }

    // Initialiser l'upload d'images multiples
    initImageUpload();

    // Initialiser l'upload d'images multiples pour l'édition
    const editImageManager = initEditImageUpload();

    // === INITIALISATION DES BOUTONS D'ÉDITION DE VOITURE ===
    function initEditCarButtons() {
        console.log('Initialisation des boutons d\'édition de voiture...');
        
        const editCarBtns = document.querySelectorAll('.edit-car-btn');
        console.log('Boutons d\'édition trouvés:', editCarBtns.length);
        
        editCarBtns.forEach((btn, index) => {
            console.log(`Configuration du bouton ${index + 1} avec ID:`, btn.getAttribute('data-id'));
            
            // Supprimer les écouteurs existants pour éviter les doublons
            btn.replaceWith(btn.cloneNode(true));
        });
        
        // Réattacher les écouteurs aux nouveaux éléments
        document.querySelectorAll('.edit-car-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const carId = this.getAttribute('data-id');
                console.log('Bouton édition voiture cliqué - ID:', carId);
                
                if (carId) {
                    openEditCarModal(carId);
                } else {
                    console.error('ID de voiture manquant');
                }
            });
        });
    }

    // === FONCTION OUVERTURE MODAL ÉDITION VOITURE ===
    function openEditCarModal(carId) {
        console.log('Ouverture modal édition voiture ID:', carId);
        
        if (!carId) {
            console.error('ID de voiture invalide');
            return;
        }
        
        // Fermer tous les modals d'abord
        closeAllModals();
        
        // Afficher le modal d'édition
        if (editCarModal) {
            editCarModal.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            // Afficher un message de chargement
            if (editCarServerMsg) {
                editCarServerMsg.style.display = 'block';
                editCarServerMsg.textContent = 'Chargement des données...';
                editCarServerMsg.classList.remove('error', 'success');
            }
            
            // Réinitialiser le formulaire
            if (editCarForm) editCarForm.reset();
            if (editImageManager && editImageManager.resetEditImageUpload) {
                editImageManager.resetEditImageUpload();
            }
        } else {
            console.error('Modal d\'édition non trouvé');
            return;
        }

        // Charger les données de la voiture
        fetch(window.location.origin + '/Luxury-cars/controllers/CarController.php?action=get_car&car_id=' + carId)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Données voiture reçues:', data);
                
                if (data.success && data.car) {
                    const car = data.car;
                    
                    // Remplir le formulaire
                    document.getElementById('editCarId').value = car.id;
                    document.getElementById('editCarBrand').value = car.brand_id || '';
                    document.getElementById('editCarModel').value = car.model || '';
                    document.getElementById('editCarCategory').value = car.category_id || '';
                    document.getElementById('editCarYear').value = car.year || '';
                    document.getElementById('editCarColor').value = car.color || '';
                    document.getElementById('editCarLicensePlate').value = car.license_plate || '';
                    document.getElementById('editCarDailyPrice').value = car.daily_price || '';
                    document.getElementById('editCarStatus').value = car.status || 'disponible';
                    document.getElementById('editCarFuelType').value = car.fuel_type || '';
                    document.getElementById('editCarTransmission').value = car.transmission || '';
                    document.getElementById('editCarDescription').value = car.description || '';

                    // Charger les images (principale + supplémentaires)
                    if (editImageManager && editImageManager.loadExistingImages) {
                        editImageManager.loadExistingImages(carId, car.main_image_url);
                    }

                    // Charger les catégories et marques
                    loadCategoriesForEdit();
                    loadBrands();

                    // Cacher le message de chargement
                    if (editCarServerMsg) {
                        editCarServerMsg.style.display = 'none';
                    }
                    
                    console.log('Formulaire d\'édition rempli avec succès');
                } else {
                    throw new Error(data.message || 'Erreur lors du chargement des données');
                }
            })
            .catch(error => {
                console.error('Erreur chargement voiture:', error);
                if (editCarServerMsg) {
                    editCarServerMsg.style.display = 'block';
                    editCarServerMsg.textContent = 'Erreur: ' + error.message;
                    editCarServerMsg.classList.add('error');
                }
            });
    }

    // === CHARGEMENT DES CATÉGORIES POUR L'ÉDITION ===
    function loadCategoriesForEdit() {
        fetch(window.location.origin + '/Luxury-cars/controllers/CarController.php?action=get_categories')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('editCarCategory');
                if (select && data.success) {
                    const currentValue = select.value;
                    select.innerHTML = '<option value="">-- Sélectionnez une catégorie --</option>';
                    data.categories.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = category.name;
                        select.appendChild(option);
                    });
                    // Maintenir la valeur sélectionnée
                    select.value = currentValue;
                }
            })
            .catch(error => console.error('Erreur chargement catégories:', error));
    }

    // === MODAL AJOUT VOITURE ===
    if (openCarModalBtn) {
        openCarModalBtn.addEventListener('click', function() {
            closeAllModals();
            addCarModal.classList.add('active');
            document.body.style.overflow = 'hidden';
            loadCategories(); // Charger les catégories
            loadBrands(); // Charger les marques
        });
    }

    if (closeCarModalBtn) closeCarModalBtn.addEventListener('click', closeAllModals);
    if (cancelCarModalBtn) cancelCarModalBtn.addEventListener('click', closeAllModals);

    // Validation formulaire voiture
    function validateCarForm() {
        clearFormErrors('addCarForm');
        let isValid = true;

        const fields = [
            { id: 'carBrand', validator: (val) => val.trim().length > 0, message: 'La marque est requise' },
            { id: 'carModel', validator: (val) => val.trim().length > 0, message: 'Le modèle est requis' },
            { id: 'carCategory', validator: (val) => val !== '', message: 'La catégorie est requise' },
            { id: 'carYear', validator: (val) => val >= 2000 && val <= 2030, message: 'L\'année doit être entre 2000 et 2030' },
            { id: 'carColor', validator: (val) => val.trim().length > 0, message: 'La couleur est requise' },
            { id: 'carLicensePlate', validator: (val) => val.trim().length > 0, message: 'La plaque est requise' },
            { id: 'carDailyPrice', validator: (val) => val > 0, message: 'Le prix doit être positif' },
            { id: 'carFuelType', validator: (val) => val !== '', message: 'Le type de carburant est requis' },
            { id: 'carTransmission', validator: (val) => val !== '', message: 'La transmission est requis'},
                        { id: 'carStatus', validator: (val) => val !== '', message: 'Le statut est requis' }
        ];

        fields.forEach(field => {
            const value = document.getElementById(field.id).value;
            if (!field.validator(value)) {
                setFormError('addCarForm', field.id, field.message);
                isValid = false;
            } else {
                setFormSuccess('addCarForm', field.id);
            }
        });

        // Validation des images
        const carImagesInput = document.getElementById('carImages');
        if (!carImagesInput || carImagesInput.files.length === 0) {
            setFormError('addCarForm', 'carImages', 'Au moins une image est requise');
            isValid = false;
        } else if (carImagesInput.files.length > 5) {
            setFormError('addCarForm', 'carImages', 'Maximum 5 images autorisées');
            isValid = false;
        } else {
            setFormSuccess('addCarForm', 'carImages');
        }

        return isValid;
    }

    // Soumission formulaire voiture
    if (addCarForm) {
        addCarForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validateCarForm()) return;

            const formData = new FormData(this);
            formData.append('action', 'add_car');

            const submitBtn = addCarForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ajout en cours...';

            const serverMsg = document.getElementById('carFormServerMessage');
            if (serverMsg) serverMsg.style.display = 'none';

            fetch(window.location.origin + '/Luxury-cars/controllers/CarController.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(async response => {
                const text = await response.text();
                let data = null;
                try { data = JSON.parse(text); } catch (e) { data = null; }
                
                if (!response.ok) throw new Error('Erreur serveur: ' + response.status);
                return data !== null ? data : text;
            })
            .then(data => {
                if (typeof data === 'string') {
                    showServerMessage('carFormServerMessage', 'Erreur serveur (réponse inattendue)', 'error');
                    return;
                }

                if (data.success) {
                    showServerMessage('carFormServerMessage', data.message || 'Voiture ajoutée avec succès', 'success');
                    
                    setTimeout(() => { 
                        closeAllModals(); 
                        refreshCarsList();
                    }, 1500);
                    
                } else if (data.errors) {
                    for (let field in data.errors) {
                        setFormError('addCarForm', 'car' + field.charAt(0).toUpperCase() + field.slice(1), data.errors[field]);
                    }
                } else {
                    showServerMessage('carFormServerMessage', data.message || 'Erreur lors de l\'ajout', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur ajout voiture:', error);
                showServerMessage('carFormServerMessage', 'Erreur serveur', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }

    // === MODAL AJOUT CATÉGORIE ===
    if (openCategoryModalBtn) {
        openCategoryModalBtn.addEventListener('click', function() {
            closeAllModals();
            addCategoryModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }

    if (closeCategoryModalBtn) closeCategoryModalBtn.addEventListener('click', closeAllModals);
    if (cancelCategoryModalBtn) cancelCategoryModalBtn.addEventListener('click', closeAllModals);

    // Brand modal open/close
    if (openBrandModalBtn) {
        openBrandModalBtn.addEventListener('click', function() {
            closeAllModals();
            addBrandModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }
    if (openViewBrandsModalBtn) {
        openViewBrandsModalBtn.addEventListener('click', function() {
            closeAllModals();
            viewBrandsModal.classList.add('active');
            document.body.style.overflow = 'hidden';
            loadBrandsForView();
        });
    }
    if (closeBrandModalBtn) closeBrandModalBtn.addEventListener('click', closeAllModals);
    if (cancelBrandModalBtn) cancelBrandModalBtn.addEventListener('click', closeAllModals);
    if (closeViewBrandsModalBtn) closeViewBrandsModalBtn.addEventListener('click', closeAllModals);
    if (closeViewBrandsBtn) closeViewBrandsBtn.addEventListener('click', closeAllModals);

    if (quickAddBrandBtn) {
        quickAddBrandBtn.addEventListener('click', function() {
            closeAllModals();
            addBrandModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }

    // Soumission formulaire catégorie
    if (addCategoryForm) {
        addCategoryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const categoryName = document.getElementById('categoryName').value.trim();
            if (!categoryName) {
                setFormError('addCategoryForm', 'categoryName', 'Le nom de la catégorie est requis');
                return;
            }

            const formData = new FormData(this);
            formData.append('action', 'add_category');

            const submitBtn = addCategoryForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ajout en cours...';

            const serverMsg = document.getElementById('categoryFormServerMessage');
            if (serverMsg) serverMsg.style.display = 'none';

            fetch(window.location.origin + '/Luxury-cars/controllers/CarController.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(async response => {
                const text = await response.text();
                let data = null;
                try { data = JSON.parse(text); } catch (e) { data = null; }
                
                if (!response.ok) throw new Error('Erreur serveur: ' + response.status);
                return data !== null ? data : text;
            })
            .then(data => {
                if (typeof data === 'string') {
                    showServerMessage('categoryFormServerMessage', 'Erreur serveur (réponse inattendue)', 'error');
                    return;
                }

                if (data.success) {
                    showServerMessage('categoryFormServerMessage', data.message || 'Catégorie ajoutée avec succès', 'success');
                    
                    // Masquer le message après 3 secondes
                    setTimeout(() => {
                        const serverMsg = document.getElementById('categoryFormServerMessage');
                        if (serverMsg) serverMsg.style.display = 'none';
                    }, 3000);
                    
                    setTimeout(() => { 
                        closeAllModals(); 
                        addCategoryForm.reset();
                    }, 1500);
                } else {
                    showServerMessage('categoryFormServerMessage', data.message || 'Erreur lors de l\'ajout', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur ajout catégorie:', error);
                showServerMessage('categoryFormServerMessage', 'Erreur serveur', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }

    // === MODAL AJOUT MARQUE ===
    if (addBrandForm) {
        addBrandForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('brandName').value.trim();
            if (!name) {
                setFormError('addBrandForm', 'brandName', 'Le nom de la marque est requis');
                return;
            }

            const formData = new FormData(this);
            formData.append('action', 'add_brand');

            const submitBtn = addBrandForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ajout en cours...';

            const serverMsg = document.getElementById('brandFormServerMessage');
            if (serverMsg) serverMsg.style.display = 'none';

            fetch(window.location.origin + '/Luxury-cars/controllers/CarController.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(async response => {
                const text = await response.text();
                let data = null;
                try { data = JSON.parse(text); } catch (e) { data = null; }
                if (!response.ok) throw new Error('Erreur serveur: ' + response.status);
                return data !== null ? data : text;
            })
            .then(data => {
                if (typeof data === 'string') {
                    showServerMessage('brandFormServerMessage', 'Erreur serveur (réponse inattendue)', 'error');
                    return;
                }
                if (data.success) {
                    showServerMessage('brandFormServerMessage', data.message || 'Marque ajoutée', 'success');
                    
                    // Masquer le message après 3 secondes
                    setTimeout(() => {
                        const serverMsg = document.getElementById('brandFormServerMessage');
                        if (serverMsg) serverMsg.style.display = 'none';
                    }, 3000);
                    
                    setTimeout(() => { 
                        closeAllModals(); 
                        addBrandForm.reset(); 
                        loadBrands(); 
                    }, 1000);
                } else {
                    showServerMessage('brandFormServerMessage', data.message || 'Erreur lors de l\'ajout', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur ajout marque:', error);
                showServerMessage('brandFormServerMessage', 'Erreur serveur', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }

    // === MODAL AFFICHAGE MARQUES ===
    if (openViewBrandsModalBtn) {
        // already wired above
    }

    // Charger les marques pour le select
    function loadBrands() {
        fetch(window.location.origin + '/Luxury-cars/controllers/CarController.php?action=get_brands')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('carBrand');
                const editSelect = document.getElementById('editCarBrand');
                if (select && data.success) {
                    const currentValue = select.value;
                    select.innerHTML = '<option value="">-- Sélectionnez une marque --</option>';
                    data.brands.forEach(brand => {
                        const option = document.createElement('option');
                        option.value = brand.id;
                        option.textContent = brand.name;
                        select.appendChild(option);
                    });
                    select.value = currentValue;
                }
                if (editSelect && data.success) {
                    const currentValue = editSelect.value;
                    editSelect.innerHTML = '<option value="">-- Sélectionnez une marque --</option>';
                    data.brands.forEach(brand => {
                        const option = document.createElement('option');
                        option.value = brand.id;
                        option.textContent = brand.name;
                        editSelect.appendChild(option);
                    });
                    editSelect.value = currentValue;
                }
            });
    }

    function loadBrandsForView() {
        fetch(window.location.origin + '/Luxury-cars/controllers/CarController.php?action=get_brands')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('brandsTableBody');
                if (tbody && data.success) {
                    tbody.innerHTML = '';
                    data.brands.forEach(brand => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${brand.id}</td>
                            <td>${brand.name}</td>
                            <td>${brand.created_at || ''}</td>
                            <td>
                                <button class="btn-action delete-brand-btn" data-id="${brand.id}" data-name="${brand.name}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                    
                    // Attacher les gestionnaires pour la suppression - ouvrir modal de confirmation
                    document.querySelectorAll('.delete-brand-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const brandId = this.getAttribute('data-id');
                            const brandName = this.getAttribute('data-name');
                            // Ouvrir le modal de suppression
                            openDeleteBrandModal(brandId, brandName);
                        });
                    });
                }
            });
    }

    // === SUPPRESSION DE MARQUE VIA LE MODAL DE CONFIRMATION ===
    function openDeleteBrandModal(brandId, brandName) {
        selectedBrandToDelete = { id: brandId, name: brandName };
        // close view modal and open delete confirmation
        closeAllModals();
        if (deleteBrandNameEl) deleteBrandNameEl.textContent = brandName;
        if (deleteBrandServerMsg) { 
            deleteBrandServerMsg.style.display = 'none'; 
            deleteBrandServerMsg.textContent = ''; 
            deleteBrandServerMsg.className = 'server-message'; 
        }
        if (deleteBrandModal) {
            deleteBrandModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeDeleteBrandModal() {
        if (deleteBrandModal) {
            deleteBrandModal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        selectedBrandToDelete = null;
    }

    // Événements pour le modal de suppression de marque
    if (closeDeleteBrandModalBtn) closeDeleteBrandModalBtn.addEventListener('click', closeDeleteBrandModal);
    if (cancelDeleteBrandBtn) {
        cancelDeleteBrandBtn.addEventListener('click', function() {
            // Annuler la suppression : fermer modal suppression et rouvrir la vue des marques
            closeDeleteBrandModal();
            // réouvrir la modal de visualisation des marques
            if (viewBrandsModal) {
                viewBrandsModal.classList.add('active');
                document.body.style.overflow = 'hidden';
                loadBrandsForView();
            }
        });
    }

    if (deleteBrandModal) {
        deleteBrandModal.addEventListener('click', function(e) {
            if (e.target === deleteBrandModal) {
                closeDeleteBrandModal();
            }
        });
    }

    // Confirmation de suppression de marque
    if (confirmDeleteBrandBtn) {
        confirmDeleteBrandBtn.addEventListener('click', function() {
            if (!selectedBrandToDelete) return;

            const originalText = confirmDeleteBrandBtn.innerHTML;
            confirmDeleteBrandBtn.disabled = true;
            confirmDeleteBrandBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Suppression...';

            const formData = new FormData();
            formData.append('action', 'delete_brand');
            formData.append('brand_id', selectedBrandToDelete.id);

            fetch(window.location.origin + '/Luxury-cars/controllers/CarController.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(async response => {
                const text = await response.text();
                let data = null;
                try { 
                    data = JSON.parse(text); 
                } catch (e) { 
                    data = null; 
                }

                if (!response.ok) {
                    if (deleteBrandServerMsg) {
                        deleteBrandServerMsg.style.display = 'block';
                        deleteBrandServerMsg.textContent = 'Erreur serveur: ' + response.status;
                        deleteBrandServerMsg.classList.add('error');
                    }
                    throw new Error('Erreur serveur: ' + response.status);
                }

                return data !== null ? data : text;
            })
            .then(data => {
                if (typeof data === 'string') {
                    if (deleteBrandServerMsg) {
                        deleteBrandServerMsg.style.display = 'block';
                        deleteBrandServerMsg.textContent = 'Erreur serveur (réponse inattendue)';
                        deleteBrandServerMsg.classList.add('error');
                    }
                    return;
                }

                if (data.success) {
                    if (deleteBrandServerMsg) {
                        deleteBrandServerMsg.style.display = 'block';
                        deleteBrandServerMsg.textContent = data.message || 'Marque supprimée avec succès.';
                        deleteBrandServerMsg.classList.remove('error');
                        deleteBrandServerMsg.classList.add('success');
                        
                        // Masquer automatiquement après 3 secondes
                        setTimeout(() => {
                            deleteBrandServerMsg.style.display = 'none';
                        }, 3000);
                    }
                    
                    // Recharger les listes et revenir à la vue marques
                    setTimeout(() => {
                        closeDeleteBrandModal();
                        if (viewBrandsModal) {
                            viewBrandsModal.classList.add('active');
                            document.body.style.overflow = 'hidden';
                        }
                        loadBrandsForView();
                        loadBrands();
                    }, 800);
                } else {
                    if (deleteBrandServerMsg) {
                        deleteBrandServerMsg.style.display = 'block';
                        deleteBrandServerMsg.textContent = data.message || 'Erreur lors de la suppression';
                        deleteBrandServerMsg.classList.add('error');
                    }
                }
            })
            .catch(error => {
                console.error('Erreur suppression marque:', error);
                if (deleteBrandServerMsg) {
                    deleteBrandServerMsg.style.display = 'block';
                    deleteBrandServerMsg.textContent = 'Erreur serveur lors de la suppression. Vérifiez la console pour plus de détails.';
                    deleteBrandServerMsg.classList.add('error');
                }
            })
            .finally(() => {
                confirmDeleteBrandBtn.disabled = false;
                confirmDeleteBrandBtn.innerHTML = originalText;
            });
        });
    }

    // === MODAL AFFICHAGE CATÉGORIES ===
    if (openViewCategoriesModalBtn) {
        openViewCategoriesModalBtn.addEventListener('click', function() {
            closeAllModals();
            viewCategoriesModal.classList.add('active');
            document.body.style.overflow = 'hidden';
            loadCategoriesForView();
        });
    }

    if (closeViewCategoriesModalBtn) closeViewCategoriesModalBtn.addEventListener('click', closeAllModals);
    if (closeViewCategoriesBtn) closeViewCategoriesBtn.addEventListener('click', closeAllModals);

    function loadCategories() {
        // Charger les catégories pour le select
        fetch(window.location.origin + '/Luxury-cars/controllers/CarController.php?action=get_categories')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('carCategory');
                if (select && data.success) {
                    select.innerHTML = '<option value="">-- Sélectionnez une catégorie --</option>';
                    data.categories.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = category.name;
                        select.appendChild(option);
                    });
                }
            })
            .catch(error => console.error('Erreur chargement catégories:', error));
    }

    function loadCategoriesForView() {
        // Charger les catégories pour l'affichage
        fetch(window.location.origin + '/Luxury-cars/controllers/CarController.php?action=get_categories')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('categoriesTableBody');
                if (tbody && data.success) {
                    tbody.innerHTML = '';
                    data.categories.forEach(category => {
                        const row = document.createElement('tr');
                        const date = new Date(category.created_at);
                        row.innerHTML = `
                            <td>${category.id}</td>
                            <td>${category.name}</td>
                            <td>${date.toLocaleDateString('fr-FR')}</td>
                            <td>
                                <button class="btn-action delete-category-btn" data-id="${category.id}" data-name="${category.name}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });

                    // Ajouter les écouteurs pour la suppression -> ouvrir modal de confirmation
                    document.querySelectorAll('.delete-category-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const categoryId = this.getAttribute('data-id');
                            const categoryName = this.getAttribute('data-name');
                            // Ouvrir le modal de suppression
                            openDeleteCategoryModal(categoryId, categoryName);
                        });
                    });
                }
            })
            .catch(error => console.error('Erreur chargement catégories:', error));
    }

    // === SUPPRESSION DE CATÉGORIE VIA LE MODAL DE CONFIRMATION ===
    const deleteCategoryNameEl = document.getElementById('deleteCategoryName');
    const confirmDeleteCategoryBtn = document.getElementById('confirmDeleteCategory');
    const cancelDeleteCategoryBtn = document.getElementById('cancelDeleteCategory');
    const closeDeleteCategoryModalBtn = document.getElementById('closeDeleteCategoryModal');
    const deleteCategoryServerMsg = document.getElementById('deleteCategoryServerMessage');

    let selectedCategoryToDelete = null;

    function openDeleteCategoryModal(categoryId, categoryName) {
        selectedCategoryToDelete = { id: categoryId, name: categoryName };
        // close view modal and open delete confirmation
        closeAllModals();
        if (deleteCategoryNameEl) deleteCategoryNameEl.textContent = categoryName;
        if (deleteCategoryServerMsg) { deleteCategoryServerMsg.style.display = 'none'; deleteCategoryServerMsg.textContent = ''; deleteCategoryServerMsg.className = 'server-message'; }
        if (deleteCategoryModal) {
            deleteCategoryModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeDeleteCategoryModal() {
        if (deleteCategoryModal) {
            deleteCategoryModal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        selectedCategoryToDelete = null;
    }

    if (closeDeleteCategoryModalBtn) closeDeleteCategoryModalBtn.addEventListener('click', closeDeleteCategoryModal);
    if (cancelDeleteCategoryBtn) {
        cancelDeleteCategoryBtn.addEventListener('click', function() {
            // Annuler la suppression : fermer modal suppression et rouvrir la vue des catégories
            closeDeleteCategoryModal();
            // réouvrir la modal de visualisation des catégories
            if (viewCategoriesModal) {
                viewCategoriesModal.classList.add('active');
                document.body.style.overflow = 'hidden';
                loadCategoriesForView();
            }
        });
    }

    if (deleteCategoryModal) {
        deleteCategoryModal.addEventListener('click', function(e) {
            if (e.target === deleteCategoryModal) {
                closeDeleteCategoryModal();
            }
        });
    }

    if (confirmDeleteCategoryBtn) {
        confirmDeleteCategoryBtn.addEventListener('click', function() {
            if (!selectedCategoryToDelete) return;

            const originalText = confirmDeleteCategoryBtn.innerHTML;
            confirmDeleteCategoryBtn.disabled = true;
            confirmDeleteCategoryBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Suppression...';

            const formData = new FormData();
            formData.append('action', 'delete_category');
            formData.append('category_id', selectedCategoryToDelete.id);

            fetch(window.location.origin + '/Luxury-cars/controllers/CarController.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(async response => {
                const text = await response.text();
                let data = null;
                try { data = JSON.parse(text); } catch (e) { data = null; }

                if (!response.ok) {
                    if (deleteCategoryServerMsg) {
                        deleteCategoryServerMsg.style.display = 'block';
                        deleteCategoryServerMsg.textContent = 'Erreur serveur: ' + response.status;
                        deleteCategoryServerMsg.classList.add('error');
                    }
                    throw new Error('Erreur serveur: ' + response.status);
                }

                return data !== null ? data : text;
            })
            .then(data => {
                if (typeof data === 'string') {
                    if (deleteCategoryServerMsg) {
                        deleteCategoryServerMsg.style.display = 'block';
                        deleteCategoryServerMsg.textContent = 'Erreur serveur (réponse inattendue)';
                        deleteCategoryServerMsg.classList.add('error');
                    }
                    return;
                }

                if (data.success) {
                    if (deleteCategoryServerMsg) {
                        deleteCategoryServerMsg.style.display = 'block';
                        deleteCategoryServerMsg.textContent = data.message || 'Catégorie supprimée avec succès.';
                        deleteCategoryServerMsg.classList.remove('error');
                        deleteCategoryServerMsg.classList.add('success');
                        
                        // Masquer automatiquement après 3 secondes
                        setTimeout(() => {
                            deleteCategoryServerMsg.style.display = 'none';
                        }, 3000);
                    }
                    
                    // Recharger les listes et revenir à la vue catégories
                    setTimeout(() => {
                        closeDeleteCategoryModal();
                        if (viewCategoriesModal) {
                            viewCategoriesModal.classList.add('active');
                            document.body.style.overflow = 'hidden';
                        }
                        loadCategoriesForView();
                        loadCategories();
                    }, 800);
                } else {
                    if (deleteCategoryServerMsg) {
                        deleteCategoryServerMsg.style.display = 'block';
                        deleteCategoryServerMsg.textContent = data.message || 'Erreur lors de la suppression';
                        deleteCategoryServerMsg.classList.add('error');
                    }
                }
            })
            .catch(error => {
                console.error('Erreur suppression catégorie:', error);
                if (deleteCategoryServerMsg) {
                    deleteCategoryServerMsg.style.display = 'block';
                    deleteCategoryServerMsg.textContent = 'Erreur serveur lors de la suppression. Vérifiez la console pour plus de détails.';
                    deleteCategoryServerMsg.classList.add('error');
                }
            })
            .finally(() => {
                confirmDeleteCategoryBtn.disabled = false;
                confirmDeleteCategoryBtn.innerHTML = originalText;
            });
        });
    }

    // Fermer les modals en cliquant à l'extérieur (ajout des modals de marques)
    [addCarModal, addCategoryModal, viewCategoriesModal, addBrandModal, viewBrandsModal, deleteCategoryModal, deleteBrandModal].forEach(modal => {
        if (!modal) return;
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeAllModals();
            }
        });
    });

    // Initialiser les boutons immédiatement
    initEditCarButtons();

    // === ÉDITION ET SUPPRESSION DE VOITURE ===
    const closeEditCarModalBtn = document.getElementById('closeEditCarModal');
    const closeDeleteCarModalBtn = document.getElementById('closeDeleteCarModal');
    const cancelEditCarModalBtn = document.getElementById('cancelEditCarModal');
    const cancelDeleteCarBtn = document.getElementById('cancelDeleteCar');
    const confirmDeleteCarBtn = document.getElementById('confirmDeleteCar');
    const editCarForm = document.getElementById('editCarForm');
    const editCarServerMsg = document.getElementById('editCarServerMessage');
    const deleteCarServerMsg = document.getElementById('deleteCarServerMessage');

    let selectedCarToEdit = null;
    let selectedCarToDelete = null;

    // Fermer la modal d'édition de voiture
    function closeEditCarModal() {
        if (editCarModal) {
            editCarModal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        selectedCarToEdit = null;
        if (editCarForm) editCarForm.reset();
        if (editImageManager && editImageManager.resetEditImageUpload) {
            editImageManager.resetEditImageUpload();
        }
    }

    if (closeEditCarModalBtn) closeEditCarModalBtn.addEventListener('click', closeEditCarModal);
    if (cancelEditCarModalBtn) cancelEditCarModalBtn.addEventListener('click', closeEditCarModal);

    // Validation du formulaire d'édition de voiture
    function validateEditCarForm() {
        clearFormErrors('editCarForm');
        let isValid = true;

        // Validation seulement si le champ est rempli
        const year = document.getElementById('editCarYear').value;
        if (year && (year < 2000 || year > 2030)) {
            setFormError('editCarForm', 'editCarYear', 'L\'année doit être entre 2000 et 2030');
            isValid = false;
        } else {
            setFormSuccess('editCarForm', 'editCarYear');
        }

        const dailyPrice = document.getElementById('editCarDailyPrice').value;
        if (dailyPrice && dailyPrice <= 0) {
            setFormError('editCarForm', 'editCarDailyPrice', 'Le prix doit être positif');
            isValid = false;
        } else {
            setFormSuccess('editCarForm', 'editCarDailyPrice');
        }

        // Les autres champs ne sont pas obligatoires pour l'édition
        return isValid;
    }

    // Soumission du formulaire d'édition
    if (editCarForm) {
        editCarForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!validateEditCarForm()) return;

            const formData = new FormData(this);
            formData.append('action', 'update_car');

            const submitBtn = editCarForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';

            if (editCarServerMsg) editCarServerMsg.style.display = 'none';

            fetch(window.location.origin + '/Luxury-cars/controllers/CarController.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(async response => {
                const text = await response.text();
                let data = null;
                try { data = JSON.parse(text); } catch (e) { data = null; }

                if (!response.ok) {
                    if (editCarServerMsg) {
                        editCarServerMsg.style.display = 'block';
                        editCarServerMsg.textContent = 'Erreur serveur: ' + response.status;
                        editCarServerMsg.classList.add('error');
                    }
                    throw new Error('Erreur serveur: ' + response.status);
                }

                return data !== null ? data : text;
            })
            .then(data => {
                if (typeof data === 'string') {
                    if (editCarServerMsg) {
                        editCarServerMsg.style.display = 'block';
                        editCarServerMsg.textContent = 'Erreur serveur (réponse inattendue)';
                        editCarServerMsg.classList.add('error');
                    }
                    return;
                }

                if (data.success) {
                    if (editCarServerMsg) {
                        editCarServerMsg.style.display = 'block';
                        editCarServerMsg.textContent = data.message || 'Voiture modifiée avec succès.';
                        editCarServerMsg.classList.remove('error');
                        editCarServerMsg.classList.add('success');
                        
                        // Masquer automatiquement après 3 secondes
                        setTimeout(() => {
                            editCarServerMsg.style.display = 'none';
                        }, 3000);
                    }
                    
                    setTimeout(() => {
                        closeEditCarModal();
                        refreshCarsList();
                    }, 1000);
                    
                } else if (data.errors) {
                    for (let field in data.errors) {
                        const errorEl = document.getElementById('editCar' + field.charAt(0).toUpperCase() + field.slice(1) + 'Error');
                        if (errorEl) {
                            errorEl.textContent = data.errors[field];
                            errorEl.style.display = 'block';
                        }
                    }
                } else {
                    if (editCarServerMsg) {
                        editCarServerMsg.style.display = 'block';
                        editCarServerMsg.textContent = data.message || 'Erreur lors de la modification';
                        editCarServerMsg.classList.add('error');
                    }
                }
            })
            .catch(error => {
                console.error('Erreur modification voiture:', error);
                if (editCarServerMsg) {
                    editCarServerMsg.style.display = 'block';
                    editCarServerMsg.textContent = 'Erreur serveur. Vérifiez la console pour plus de détails.';
                    editCarServerMsg.classList.add('error');
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }

    // Gestionnaire de suppression de voiture
    document.querySelectorAll('.delete-car-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const carId = this.getAttribute('data-id');
            openDeleteCarModal(carId);
        });
    });

    function openDeleteCarModal(carId) {
        // Chercher la voiture pour afficher son nom
        fetch(window.location.origin + '/Luxury-cars/controllers/CarController.php?action=get_car&car_id=' + carId)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.car) {
                    const car = data.car;
                    selectedCarToDelete = { id: car.id, name: (car.brand_name + ' ' + car.model) };
                    document.getElementById('deleteCarName').textContent = selectedCarToDelete.name;
                    
                    closeAllModals();
                    if (deleteCarModal) {
                        deleteCarModal.classList.add('active');
                        document.body.style.overflow = 'hidden';
                    }
                    if (deleteCarServerMsg) { deleteCarServerMsg.style.display = 'none'; deleteCarServerMsg.textContent = ''; deleteCarServerMsg.className = 'server-message'; }
                }
            })
            .catch(error => console.error('Erreur chargement voiture:', error));
    }

    function closeDeleteCarModal() {
        if (deleteCarModal) {
            deleteCarModal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        selectedCarToDelete = null;
    }

    if (closeDeleteCarModalBtn) closeDeleteCarModalBtn.addEventListener('click', closeDeleteCarModal);
    if (cancelDeleteCarBtn) cancelDeleteCarBtn.addEventListener('click', closeDeleteCarModal);

    if (confirmDeleteCarBtn) {
        confirmDeleteCarBtn.addEventListener('click', function() {
            if (!selectedCarToDelete) return;

            const originalText = confirmDeleteCarBtn.innerHTML;
            confirmDeleteCarBtn.disabled = true;
            confirmDeleteCarBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Suppression...';

            const formData = new FormData();
            formData.append('action', 'delete_car');
            formData.append('car_id', selectedCarToDelete.id);

            fetch(window.location.origin + '/Luxury-cars/controllers/CarController.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(async response => {
                const text = await response.text();
                let data = null;
                try { data = JSON.parse(text); } catch (e) { data = null; }

                if (!response.ok) {
                    if (deleteCarServerMsg) {
                        deleteCarServerMsg.style.display = 'block';
                        deleteCarServerMsg.textContent = 'Erreur serveur: ' + response.status;
                        deleteCarServerMsg.classList.add('error');
                    }
                    throw new Error('Erreur serveur: ' + response.status);
                }

                return data !== null ? data : text;
            })
            .then(data => {
                if (typeof data === 'string') {
                    if (deleteCarServerMsg) {
                        deleteCarServerMsg.style.display = 'block';
                        deleteCarServerMsg.textContent = 'Erreur serveur (réponse inattendue)';
                        deleteCarServerMsg.classList.add('error');
                    }
                    return;
                }

                if (data.success) {
                    if (deleteCarServerMsg) {
                        deleteCarServerMsg.style.display = 'block';
                        deleteCarServerMsg.textContent = data.message || 'Voiture supprimée avec succès.';
                        deleteCarServerMsg.classList.remove('error');
                        deleteCarServerMsg.classList.add('success');
                        
                        // Masquer automatiquement après 3 secondes
                        setTimeout(() => {
                            deleteCarServerMsg.style.display = 'none';
                        }, 3000);
                    }
                    
                    setTimeout(() => {
                        closeDeleteCarModal();
                        refreshCarsList();
                    }, 1000);
                    
                } else {
                    if (deleteCarServerMsg) {
                        deleteCarServerMsg.style.display = 'block';
                        deleteCarServerMsg.textContent = data.message || 'Erreur lors de la suppression';
                        deleteCarServerMsg.classList.add('error');
                    }
                }
            })
            .catch(error => {
                console.error('Erreur suppression voiture:', error);
                if (deleteCarServerMsg) {
                    deleteCarServerMsg.style.display = 'block';
                    deleteCarServerMsg.textContent = 'Erreur serveur lors de la suppression. Vérifiez la console pour plus de détails.';
                    deleteCarServerMsg.classList.add('error');
                }
            })
            .finally(() => {
                confirmDeleteCarBtn.disabled = false;
                confirmDeleteCarBtn.innerHTML = originalText;
            });
        });
    }

    // Fermer modals de voiture en cliquant à l'extérieur
    if (editCarModal) {
        editCarModal.addEventListener('click', function(e) {
            if (e.target === editCarModal) {
                closeEditCarModal();
            }
        });
    }
    if (deleteCarModal) {
        deleteCarModal.addEventListener('click', function(e) {
            if (e.target === deleteCarModal) {
                closeDeleteCarModal();
            }
        });
    }

    // Ajouter le bouton "Ajouter marque" dans l'édition
    document.getElementById('quickAddBrandBtnEdit')?.addEventListener('click', function() {
        closeAllModals();
        addBrandModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    });

    // Réinitialiser les boutons d'édition après les actions
    function refreshEditCarButtons() {
        console.log('Rafraîchissement des boutons d\'édition...');
        setTimeout(() => {
            initEditCarButtons();
        }, 500);
    }

    // Initialisation finale
    initEditCarButtons();
});

// ========== FONCTIONS DE RAFRAÎCHISSEMENT ==========

// Rafraîchir la liste des employés
function refreshEmployeesList() {
    console.log('Rafraîchissement de la liste des employés...');
    
    fetch(window.location.origin + '/Luxury-cars/controllers/UserController.php?action=get_users')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.users) {
                updateEmployeesTable(data.users);
                updateEmployeesStats(data.users.length);
            }
        })
        .catch(error => console.error('Erreur rafraîchissement employés:', error));
}

// Mettre à jour le tableau des employés
function updateEmployeesTable(users) {
    const tbody = document.querySelector('#employees-content table tbody');
    if (!tbody) return;

    if (users.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="no-data">
                    <i class="fas fa-users"></i>
                    <p>Aucun employé trouvé</p>
                </td>
            </tr>
        `;
        return;
    }

    let html = '';
    users.forEach(user => {
        const roleClass = user.role === 'manager' ? 'manager' : 'admin';
        const date = new Date(user.created_at);
        const formattedDate = date.toLocaleDateString('fr-FR');
        
        html += `
            <tr>
                <td>${user.id}</td>
                <td>${user.first_name.toLowerCase()} ${user.last_name.toLowerCase()}</td>
                <td>${user.email}</td>
                <td>+212 ${user.phone}</td>
                <td><span class="role-badge ${roleClass}">${user.role}</span></td>
                <td>${formattedDate}</td>
                <td>
                    <button class="btn-action edit-employee-btn" 
                            data-id="${user.id}"
                            data-firstname="${user.first_name}"
                            data-lastname="${user.last_name}"
                            data-email="${user.email}"
                            data-phone="${user.phone}"
                            data-role="${user.role}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-action delete-employee-btn" 
                            data-id="${user.id}" 
                            data-name="${user.first_name.toLowerCase()} ${user.last_name.toLowerCase()}"
                            data-email="${user.email}"
                            data-role="${user.role}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
    
    // Réattacher les événements aux nouveaux boutons
    attachEmployeeEvents();
}

// Mettre à jour les statistiques des employés
function updateEmployeesStats(count) {
    const statElement = document.querySelector('.stat-card:nth-child(2) .stat-info h3');
    if (statElement) {
        statElement.textContent = count;
    }
}

// Rafraîchir la liste des voitures
function refreshCarsList() {
    console.log('Rafraîchissement de la liste des voitures...');
    
    fetch(window.location.origin + '/Luxury-cars/controllers/CarController.php?action=get_cars')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.cars) {
                updateCarsGrid(data.cars);
                updateCarsStats(data.cars.length);
            }
        })
        .catch(error => console.error('Erreur rafraîchissement voitures:', error));
}

// Mettre à jour la grille des voitures
function updateCarsGrid(cars) {
    const carsGrid = document.querySelector('.cars-grid');
    if (!carsGrid) return;

    if (cars.length === 0) {
        carsGrid.innerHTML = `
            <div class="no-data" style="grid-column: 1 / -1; text-align:center; padding:20px;">
                <i class="fas fa-car"></i>
                <p>Aucune voiture trouvée</p>
            </div>
        `;
        return;
    }

    let html = '';
    cars.forEach(car => {
        const imagePath = car.main_image_url ? '../public/' + car.main_image_url : '../public/images/car-placeholder.png';
        const brand = car.brand_name || car.brand_id || '';
        const model = car.model || '';
        const title = `${brand} ${model}`.trim();
        const year = car.year || '';
        const category = car.category_name || '';
        const price = car.daily_price ? Number(car.daily_price).toFixed(2) : '';
        const status = car.status || 'Disponible';
        
        // Déterminer la classe CSS pour le statut
        let statusClass = 'available';
        if (status === 'réservé') statusClass = 'reserved';
        else if (status === 'en maintenance') statusClass = 'maintenance';
        else if (status === 'indisponible') statusClass = 'indisponible';

        html += `
            <div class="car-card">
                <div class="car-image">
                    <img src="${imagePath}" alt="${title}">
                    <span class="car-status ${statusClass}">${status}</span>
                </div>
                <div class="car-info">
                    <h3>${title || 'Voiture'}</h3>
                    <p>${year} • ${category}</p>
                    <div class="car-price">€${price}/jour</div>
                </div>
                <div class="car-actions">
                    <button class="btn-action edit-car-btn" data-id="${car.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-action delete-car-btn" data-id="${car.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });

    carsGrid.innerHTML = html;
    
    // Réattacher les événements aux nouveaux boutons
    initEditCarButtons();
    attachCarDeleteEvents();
}

// Mettre à jour les statistiques des voitures
function updateCarsStats(count) {
    const statElement = document.querySelector('.stat-card:first-child .stat-info h3');
    if (statElement) {
        statElement.textContent = count;
    }
}

// Réattacher les événements aux boutons d'employés
function attachEmployeeEvents() {
    // Réattacher les événements de modification
    document.querySelectorAll('.edit-employee-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const employeeData = {
                id: this.getAttribute('data-id'),
                first_name: this.getAttribute('data-firstname'),
                last_name: this.getAttribute('data-lastname'),
                email: this.getAttribute('data-email'),
                phone: this.getAttribute('data-phone'),
                role: this.getAttribute('data-role')
            };
            openEditEmployeeModal(employeeData);
        });
    });

    // Réattacher les événements de suppression
    document.querySelectorAll('.delete-employee-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const employeeData = {
                id: this.getAttribute('data-id'),
                name: this.getAttribute('data-name'),
                email: this.getAttribute('data-email'),
                role: this.getAttribute('data-role')
            };
            openDeleteEmployeeModal(employeeData);
        });
    });
}

// Réattacher les événements de suppression des voitures
function attachCarDeleteEvents() {
    document.querySelectorAll('.delete-car-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const carId = this.getAttribute('data-id');
            openDeleteCarModal(carId);
        });
    });
}

// Fonction pour ouvrir la modal d'édition d'employé
function openEditEmployeeModal(employeeData) {
    const editModal = document.getElementById('editEmployeeModal');
    if (!editModal) return;

    // Remplir le formulaire avec les données actuelles
    document.getElementById('editEmployeeId').value = employeeData.id;
    document.getElementById('editFirstName').value = employeeData.first_name;
    document.getElementById('editLastName').value = employeeData.last_name;
    document.getElementById('editEmail').value = employeeData.email;
    document.getElementById('editPhone').value = employeeData.phone;
    document.getElementById('editRole').value = employeeData.role;
    
    // Réinitialiser les champs mot de passe
    document.getElementById('editPassword').value = '';
    document.getElementById('editConfirmPassword').value = '';

    // Effacer les erreurs
    const serverMsg = document.getElementById('editFormServerMessage');
    if (serverMsg) {
        serverMsg.style.display = 'none';
        serverMsg.textContent = '';
        serverMsg.className = 'server-message';
    }

    // Afficher la modal
    editModal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Fonction pour ouvrir la modal de suppression d'employé
function openDeleteEmployeeModal(employeeData) {
    const deleteModal = document.getElementById('deleteEmployeeModal');
    if (!deleteModal) return;

    // Remplir les informations dans la modal
    document.getElementById('deleteEmployeeName').textContent = employeeData.name;
    document.getElementById('deleteEmployeeEmail').textContent = employeeData.email;
    document.getElementById('deleteEmployeeRole').textContent = employeeData.role;

    // Stocker les données de l'employé à supprimer
    window.currentEmployeeToDelete = employeeData;

    // Afficher la modal
    deleteModal.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    // Clear server message when opening
    const deleteServerMsg = document.getElementById('deleteEmployeeServerMessage');
    if (deleteServerMsg) {
        deleteServerMsg.style.display = 'none';
        deleteServerMsg.textContent = '';
        deleteServerMsg.classList.remove('error', 'success');
    }
}



// ========== GESTION DES RÉSERVATIONS ==========
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les statistiques des réservations
    updateReservationStats();
    
    // Attacher les événements aux boutons
    attachReservationEvents();
});

// Fonctions pour les réservations
function initReservationManagement() {
    const reservationModal = document.getElementById('reservationModal');
    const viewReservationModal = document.getElementById('viewReservationModal');
    const deleteReservationModal = document.getElementById('deleteReservationModal');
    
    const openReservationModalBtn = document.getElementById('openReservationModal');
    const closeReservationModalBtn = document.getElementById('closeReservationModal');
    const cancelReservationModalBtn = document.getElementById('cancelReservationModal');
    
    const closeViewReservationModalBtn = document.getElementById('closeViewReservationModal');
    const closeViewReservationBtn = document.getElementById('closeViewReservationBtn');
    
    const closeDeleteReservationModalBtn = document.getElementById('closeDeleteReservationModal');
    const cancelDeleteReservationBtn = document.getElementById('cancelDeleteReservation');
    const confirmDeleteReservationBtn = document.getElementById('confirmDeleteReservation');
    
    const reservationForm = document.getElementById('reservationForm');
    
    let currentReservationId = null;

    // Ouvrir le modal d'ajout
    if (openReservationModalBtn) {
        openReservationModalBtn.addEventListener('click', function() {
            openReservationModal();
        });
    }

    // Fermer le modal d'ajout/modification
    function closeReservationModal() {
        if (reservationModal) {
            reservationModal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        currentReservationId = null;
        if (reservationForm) reservationForm.reset();
        clearReservationFormErrors();
    }

    if (closeReservationModalBtn) closeReservationModalBtn.addEventListener('click', closeReservationModal);
    if (cancelReservationModalBtn) cancelReservationModalBtn.addEventListener('click', closeReservationModal);

    // Fermer le modal de visualisation
    function closeViewReservationModal() {
        if (viewReservationModal) {
            viewReservationModal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    }

    if (closeViewReservationModalBtn) closeViewReservationModalBtn.addEventListener('click', closeViewReservationModal);
    if (closeViewReservationBtn) closeViewReservationBtn.addEventListener('click', closeViewReservationModal);

    // Fermer le modal de suppression
    function closeDeleteReservationModal() {
        if (deleteReservationModal) {
            deleteReservationModal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    }

    if (closeDeleteReservationModalBtn) closeDeleteReservationModalBtn.addEventListener('click', closeDeleteReservationModal);
    if (cancelDeleteReservationBtn) cancelDeleteReservationBtn.addEventListener('click', closeDeleteReservationModal);

    // Fermer les modals en cliquant à l'extérieur
    [reservationModal, viewReservationModal, deleteReservationModal].forEach(modal => {
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    if (modal === reservationModal) closeReservationModal();
                    if (modal === viewReservationModal) closeViewReservationModal();
                    if (modal === deleteReservationModal) closeDeleteReservationModal();
                }
            });
        }
    });

    // Calculer le prix lors du changement de dates
    document.getElementById('startDate')?.addEventListener('change', calculateReservationPrice);
    document.getElementById('endDate')?.addEventListener('change', calculateReservationPrice);
    document.getElementById('carSelect')?.addEventListener('change', calculateReservationPrice);

    // Soumission du formulaire
    if (reservationForm) {
        reservationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (currentReservationId) {
                updateReservation();
            } else {
                createReservation();
            }
        });
    }

    // Confirmation de suppression
    if (confirmDeleteReservationBtn) {
        confirmDeleteReservationBtn.addEventListener('click', function() {
            if (currentReservationId) {
                deleteReservation(currentReservationId);
            }
        });
    }
}

// Ouvrir le modal d'ajout/modification
function openReservationModal(reservationId = null) {
    const modal = document.getElementById('reservationModal');
    const title = document.getElementById('reservationModalTitle');
    const submitText = document.getElementById('reservationSubmitText');
    
    if (!modal) return;

    // Fermer tous les modals d'abord
    closeAllModals();
    
    currentReservationId = reservationId;
    
    if (reservationId) {
        title.textContent = 'Modifier la réservation';
        submitText.textContent = 'Mettre à jour la réservation';
        loadReservationData(reservationId);
    } else {
        title.textContent = 'Ajouter une réservation';
        submitText.textContent = 'Enregistrer la réservation';
        clearReservationForm();
    }
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    // Charger les voitures disponibles
    loadAvailableCars();
}

// Charger les données d'une réservation
function loadReservationData(reservationId) {
    fetch(`../controllers/ReservationController.php?action=get_reservation&reservation_id=${reservationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.reservation) {
                const r = data.reservation;
                
                document.getElementById('clientFirstName').value = r.client_first_name || '';
                document.getElementById('clientLastName').value = r.client_last_name || '';
                document.getElementById('clientPhone').value = r.client_phone || '';
                document.getElementById('clientEmail').value = r.client_email || '';
                document.getElementById('startDate').value = r.start_date || '';
                document.getElementById('startTime').value = r.start_time || '';
                document.getElementById('endDate').value = r.end_date || '';
                document.getElementById('endTime').value = r.end_time || '';
                document.getElementById('reservationStatus').value = r.status || 'confirmed';
                document.getElementById('specialRequests').value = r.special_requests || '';
                
                // Recalculer le prix
                setTimeout(calculateReservationPrice, 100);
            }
        })
        .catch(error => {
            console.error('Erreur chargement réservation:', error);
            showServerMessage('reservationFormServerMessage', 'Erreur lors du chargement des données', 'error');
        });
}

// Charger les voitures disponibles
function loadAvailableCars() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    let url = '../controllers/ReservationController.php?action=get_available_cars';
    if (startDate && endDate) {
        url += `&start_date=${startDate}&end_date=${endDate}`;
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('carSelect');
            if (select && data.success) {
                const currentValue = select.value;
                select.innerHTML = '<option value="">-- Sélectionnez une voiture --</option>';
                
                data.cars.forEach(car => {
                    const option = document.createElement('option');
                    option.value = car.id;
                    option.textContent = `${car.brand_name} ${car.model} (${car.year}) - €${car.daily_price}/jour`;
                    option.setAttribute('data-price', car.daily_price);
                    select.appendChild(option);
                });
                
                // Maintenir la valeur si modification
                if (currentValue) {
                    select.value = currentValue;
                }
                
                // Recalculer le prix
                calculateReservationPrice();
            }
        })
        .catch(error => console.error('Erreur chargement voitures:', error));
}

// Calculer le prix de la réservation
function calculateReservationPrice() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const carSelect = document.getElementById('carSelect');
    const summary = document.getElementById('reservationSummary');
    const summaryDays = document.getElementById('summaryDays');
    const summaryTotal = document.getElementById('summaryTotal');
    
    if (!startDate || !endDate || !carSelect || carSelect.value === '') {
        summary.style.display = 'none';
        return;
    }
    
    const start = new Date(startDate);
    const end = new Date(endDate);
    const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
    const dailyPrice = parseFloat(carSelect.selectedOptions[0]?.getAttribute('data-price') || 0);
    const total = days * dailyPrice;
    
    if (days > 0 && total > 0) {
        summaryDays.textContent = days;
        summaryTotal.textContent = total.toFixed(2);
        summary.style.display = 'block';
    } else {
        summary.style.display = 'none';
    }
}

// Créer une réservation
function createReservation() {
    const formData = new FormData(document.getElementById('reservationForm'));
    formData.append('action', 'create_reservation_dashboard');
    
    const submitBtn = document.querySelector('#reservationForm button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création...';
    
    const serverMsg = document.getElementById('reservationFormServerMessage');
    if (serverMsg) serverMsg.style.display = 'none';
    
    fetch('../controllers/ReservationController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showServerMessage('reservationFormServerMessage', data.message || 'Réservation créée avec succès', 'success');
            setTimeout(() => {
                closeReservationModal();
                refreshReservationsList();
            }, 1500);
        } else {
            showServerMessage('reservationFormServerMessage', data.message || 'Erreur lors de la création', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur création réservation:', error);
        showServerMessage('reservationFormServerMessage', 'Erreur serveur', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Mettre à jour une réservation
function updateReservation() {
    const formData = new FormData(document.getElementById('reservationForm'));
    formData.append('action', 'update_reservation');
    formData.append('reservation_id', currentReservationId);
    
    const submitBtn = document.querySelector('#reservationForm button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mise à jour...';
    
    const serverMsg = document.getElementById('reservationFormServerMessage');
    if (serverMsg) serverMsg.style.display = 'none';
    
    fetch('../controllers/ReservationController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showServerMessage('reservationFormServerMessage', data.message || 'Réservation mise à jour avec succès', 'success');
            setTimeout(() => {
                closeReservationModal();
                refreshReservationsList();
            }, 1500);
        } else {
            showServerMessage('reservationFormServerMessage', data.message || 'Erreur lors de la mise à jour', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur mise à jour réservation:', error);
        showServerMessage('reservationFormServerMessage', 'Erreur serveur', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Voir les détails d'une réservation
function viewReservation(reservationId) {
    fetch(`../controllers/ReservationController.php?action=get_reservation&reservation_id=${reservationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.reservation) {
                const r = data.reservation;
                const content = document.getElementById('reservationDetailsContent');
                
                content.innerHTML = `
                    <div class="reservation-details">
                        <div class="detail-section">
                            <h3>Informations client</h3>
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <label>Nom complet:</label>
                                    <span>${r.client_name}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Téléphone:</label>
                                    <span>${r.client_phone}</span>
                                </div>
                                ${r.client_email ? `<div class="detail-item">
                                    <label>Email:</label>
                                    <span>${r.client_email}</span>
                                </div>` : ''}
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <h3>Informations véhicule</h3>
                            <div class="car-info-display">
                                ${r.main_image_url ? `<img src="../public/${r.main_image_url}" alt="${r.car_brand} ${r.car_model}" style="max-width: 200px; border-radius: 8px; margin-bottom: 15px;">` : ''}
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <label>Véhicule:</label>
                                        <span>${r.car_brand} ${r.car_model}</span>
                                    </div>
                                    <div class="detail-item">
                                        <label>Année:</label>
                                        <span>${r.car_year}</span>
                                    </div>
                                    <div class="detail-item">
                                        <label>Prix journalier:</label>
                                        <span>€${parseFloat(r.daily_price).toFixed(2)}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <h3>Détails de la réservation</h3>
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <label>Date de début:</label>
                                    <span>${new Date(r.start_date + ' ' + r.start_time).toLocaleString('fr-FR')}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Date de fin:</label>
                                    <span>${new Date(r.end_date + ' ' + r.end_time).toLocaleString('fr-FR')}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Durée:</label>
                                    <span>${r.total_days} jour(s)</span>
                                </div>
                                <div class="detail-item">
                                    <label>Prix total:</label>
                                    <span>€${parseFloat(r.total_amount).toFixed(2)}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Statut:</label>
                                    <span class="status-badge ${r.status}">${r.status}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Créée par:</label>
                                    <span>${r.fait_par}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Date de création:</label>
                                    <span>${new Date(r.created_at).toLocaleString('fr-FR')}</span>
                                </div>
                            </div>
                        </div>
                        
                        ${r.special_requests ? `<div class="detail-section">
                            <h3>Demandes spéciales</h3>
                            <p>${r.special_requests}</p>
                        </div>` : ''}
                    </div>
                `;
                
                document.getElementById('viewReservationModal').classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        })
        .catch(error => {
            console.error('Erreur chargement détails:', error);
            alert('Erreur lors du chargement des détails');
        });
}

// Supprimer une réservation
function deleteReservation(reservationId) {
    const formData = new FormData();
    formData.append('action', 'delete_reservation');
    formData.append('reservation_id', reservationId);
    
    const submitBtn = document.getElementById('confirmDeleteReservation');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Suppression...';
    
    const serverMsg = document.getElementById('deleteReservationServerMessage');
    if (serverMsg) serverMsg.style.display = 'none';
    
    fetch('../controllers/ReservationController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showServerMessage('deleteReservationServerMessage', data.message || 'Réservation supprimée avec succès', 'success');
            setTimeout(() => {
                closeDeleteReservationModal();
                refreshReservationsList();
            }, 1500);
        } else {
            showServerMessage('deleteReservationServerMessage', data.message || 'Erreur lors de la suppression', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur suppression réservation:', error);
        showServerMessage('deleteReservationServerMessage', 'Erreur serveur', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Ouvrir le modal de suppression
function openDeleteReservationModal(reservationId, clientName, carName, dates) {
    currentReservationId = reservationId;
    
    document.getElementById('deleteReservationClient').textContent = clientName;
    document.getElementById('deleteReservationCar').textContent = carName;
    document.getElementById('deleteReservationDate').textContent = dates;
    
    const serverMsg = document.getElementById('deleteReservationServerMessage');
    if (serverMsg) serverMsg.style.display = 'none';
    
    document.getElementById('deleteReservationModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Effacer le formulaire
function clearReservationForm() {
    document.getElementById('reservationForm').reset();
    document.getElementById('reservationSummary').style.display = 'none';
    clearReservationFormErrors();
}

// Effacer les erreurs du formulaire
function clearReservationFormErrors() {
    document.querySelectorAll('#reservationForm .error-message').forEach(el => {
        el.style.display = 'none';
        el.textContent = '';
    });
}

// Fermer le modal de réservation
function closeReservationModal() {
    const modal = document.getElementById('reservationModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
    currentReservationId = null;
}

// Initialiser la gestion des réservations
initReservationManagement();

// ========== INITIALISATION DES RÉSERVATIONS ==========
function initReservations() {
    console.log('Initialisation des réservations...');
    
    // Mettre à jour les statistiques
    updateReservationStats();
    
    // Attacher les événements
    attachReservationEvents();
    
    // Charger les voitures disponibles
    loadAvailableCarsForReservation();
}

// Mettre à jour les statistiques des réservations
function updateReservationStats() {
    fetch('../controllers/ReservationController.php?action=get_stats')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.stats) {
                const stats = data.stats;
                document.getElementById('totalReservations').textContent = stats.total || 0;
                document.getElementById('pendingReservations').textContent = stats.pending || 0;
                document.getElementById('confirmedReservations').textContent = stats.confirmed || 0;
                document.getElementById('activeReservations').textContent = stats.active || 0;
            }
        })
        .catch(error => console.error('Erreur chargement stats réservations:', error));
}

// Attacher les événements aux boutons de réservation
function attachReservationEvents() {
    // Bouton d'ouverture du modal
    const openBtn = document.getElementById('openReservationModal');
    if (openBtn) {
        openBtn.addEventListener('click', function() {
            openReservationModal();
        });
    }
    
    // Boutons d'édition
    document.querySelectorAll('.edit-reservation-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const reservationId = this.getAttribute('data-id');
            openReservationModal(reservationId);
        });
    });
    
    // Boutons de visualisation
    document.querySelectorAll('.view-reservation-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const reservationId = this.getAttribute('data-id');
            viewReservation(reservationId);
        });
    });
    
    // Boutons de suppression
    document.querySelectorAll('.delete-reservation-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const reservationId = this.getAttribute('data-id');
            const clientName = this.getAttribute('data-client');
            const carName = this.getAttribute('data-car');
            openDeleteReservationModal(reservationId, clientName, carName);
        });
    });
}

// Charger les voitures disponibles pour le formulaire de réservation
function loadAvailableCarsForReservation() {
    fetch('../controllers/ReservationController.php?action=get_available_cars')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('carSelect');
            if (select && data.success) {
                select.innerHTML = '<option value="">-- Sélectionnez une voiture --</option>';
                data.cars.forEach(car => {
                    const option = document.createElement('option');
                    option.value = car.id;
                    option.textContent = `${car.brand_name} ${car.model} (${car.year}) - €${car.daily_price}/jour`;
                    option.setAttribute('data-price', car.daily_price);
                    select.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Erreur chargement voitures:', error));
}

// Initialiser au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    initReservations();
});