document.addEventListener('DOMContentLoaded', function() {
    // Setup Tab Navigation
    setupTabNavigation();
    
    // Setup User Search
    setupUserSearch();
    
    // Setup Role Selection
    setupRoleSelection();
    
    // Setup Department Management
    setupDepartmentManagement();
    
    // Setup Log Filtering
    setupLogFiltering();
    
    // Setup Modals
    setupModals();
    
    // Setup Pagination
    setupPagination();
    
    // Setup Action Buttons
    setupActionButtons();
});

// Function to setup tab navigation
function setupTabNavigation() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button
            button.classList.add('active');
            
            // Show corresponding tab content
            const tabId = button.getAttribute('data-tab');
            document.getElementById(`${tabId}-tab`).classList.add('active');
        });
    });
}

// Function to setup user search
function setupUserSearch() {
    const searchInput = document.getElementById('user-search');
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function() {
        const searchValue = this.value.toLowerCase();
        const userRows = document.querySelectorAll('.users-table tbody tr');
        
        userRows.forEach(row => {
            const userName = row.querySelector('.user-name').textContent.toLowerCase();
            const userId = row.querySelector('.user-id').textContent.toLowerCase();
            const userEmail = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            
            if (userName.includes(searchValue) || userId.includes(searchValue) || userEmail.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}

// Function to setup role selection
function setupRoleSelection() {
    const roleItems = document.querySelectorAll('.role-item');
    if (roleItems.length === 0) return;
    
    roleItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all role items
            roleItems.forEach(role => role.classList.remove('active'));
            
            // Add active class to clicked role item
            this.classList.add('active');
            
            // Update permissions header
            const roleName = this.querySelector('.role-name span').textContent;
            document.querySelector('.permissions-header h3').textContent = `Permissions pour: ${roleName}`;
            
            // Update permissions display (in a real app, this would fetch permissions data)
            const permissionAccesses = document.querySelectorAll('.permission-access');
            
            if (roleName === 'Administrateur') {
                permissionAccesses.forEach(access => {
                    access.className = 'permission-access full';
                    access.textContent = 'Total';
                });
            } else if (roleName === 'Médecin') {
                // Simulate different permissions for doctors
                permissionAccesses.forEach((access, index) => {
                    if (index < 3) {
                        access.className = 'permission-access none';
                        access.textContent = 'Aucun';
                    } else {
                        access.className = 'permission-access full';
                        access.textContent = 'Total';
                    }
                });
            } else if (roleName === 'Infirmier/Infirmière') {
                // Simulate different permissions for nurses
                permissionAccesses.forEach((access, index) => {
                    if (index < 6) {
                        access.className = 'permission-access none';
                        access.textContent = 'Aucun';
                    } else {
                        access.className = 'permission-access partial';
                        access.textContent = 'Partiel';
                    }
                });
            } else {
                // Default to partial for other roles
                permissionAccesses.forEach(access => {
                    access.className = 'permission-access partial';
                    access.textContent = 'Partiel';
                });
            }
        });
    });
}

// Function to setup department management
function setupDepartmentManagement() {
    const departmentManageButtons = document.querySelectorAll('.department-footer .action-btn');
    if (departmentManageButtons.length === 0) return;
    
    departmentManageButtons.forEach(button => {
        button.addEventListener('click', function() {
            const departmentName = this.closest('.department-card').querySelector('h3').textContent;
            alert(`Gestion du service "${departmentName}" - Fonctionnalité à implémenter`);
        });
    });
    
    const departmentEditButtons = document.querySelectorAll('.department-actions .icon-btn[title="Éditer"]');
    departmentEditButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const departmentName = this.closest('.department-card').querySelector('h3').textContent;
            alert(`Édition du service "${departmentName}" - Fonctionnalité à implémenter`);
        });
    });
    
    const departmentDeleteButtons = document.querySelectorAll('.department-actions .icon-btn.delete');
    departmentDeleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const departmentCard = this.closest('.department-card');
            const departmentName = departmentCard.querySelector('h3').textContent;
            
            if (confirm(`Êtes-vous sûr de vouloir supprimer le service "${departmentName}" ?`)) {
                departmentCard.remove();
                showNotification('Service supprimé', 'info');
            }
        });
    });
    
    const addDepartmentButton = document.getElementById('add-department-btn');
    if (addDepartmentButton) {
        addDepartmentButton.addEventListener('click', function() {
            const departmentName = prompt('Nom du nouveau service:');
            if (!departmentName) return;
            
            const departmentsGrid = document.querySelector('.departments-grid');
            const newDepartmentCard = document.createElement('div');
            newDepartmentCard.className = 'department-card';
            newDepartmentCard.innerHTML = `
                <div class="department-card-header">
                    <h3>${departmentName}</h3>
                    <div class="department-actions">
                        <button class="icon-btn" title="Éditer"><i class="fas fa-edit"></i></button>
                        <button class="icon-btn delete" title="Supprimer"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
                <div class="department-info">
                    <div class="info-item">
                        <i class="fas fa-users"></i>
                        <span>0 utilisateurs</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-user-injured"></i>
                        <span>0 patients</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-heartbeat"></i>
                        <span>0 capteurs</span>
                    </div>
                </div>
                <div class="department-footer">
                    <button class="action-btn full-width">
                        <i class="fas fa-cog"></i> Gérer
                    </button>
                </div>
            `;
            
            departmentsGrid.appendChild(newDepartmentCard);
            setupDepartmentManagement(); // Re-setup to include new department
            showNotification(`Service "${departmentName}" créé`, 'success');
        });
    }
}

// Function to setup log filtering
function setupLogFiltering() {
    const filterButton = document.getElementById('filter-logs-btn');
    if (!filterButton) return;
    
    filterButton.addEventListener('click', function() {
        const typeFilter = document.getElementById('log-type-filter').value;
        const userFilter = document.getElementById('log-user-filter').value;
        
        // Simulate filtering logs
        const logRows = document.querySelectorAll('.logs-table tbody tr');
        
        logRows.forEach(row => {
            const logType = row.querySelector('td:nth-child(3) span').classList[1].replace('log-badge-', '');
            const userName = row.querySelector('td:nth-child(2)').textContent;
            
            let typeMatch = typeFilter === 'all' || logType.includes(typeFilter);
            let userMatch = userFilter === 'all' || 
                          (userFilter === '1' && userName.includes('Ahmed')) ||
                          (userFilter === '2' && userName.includes('Sophie')) ||
                          (userFilter === '3' && userName.includes('Isabelle'));
            
            if (typeMatch && userMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        
        showNotification('Logs filtrés', 'info');
    });
    
    const exportButton = document.getElementById('export-logs-btn');
    if (exportButton) {
        exportButton.addEventListener('click', function() {
            // Simulate log export
            showNotification('Logs exportés en CSV', 'success');
        });
    }
}

// Function to setup modals
function setupModals() {
    // Add User modal
    const addUserBtn = document.getElementById('add-user-btn');
    const userModal = document.getElementById('userModal');
    
    if (!addUserBtn || !userModal) return;
    
    addUserBtn.addEventListener('click', function() {
        userModal.style.display = 'flex';
    });
    
    // Close modal when X button is clicked
    const closeModalButtons = document.querySelectorAll('.close-modal');
    closeModalButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.modal').style.display = 'none';
        });
    });
    
    // Close modal when Cancel button is clicked
    const cancelButtons = document.querySelectorAll('.modal .cancel-btn');
    cancelButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.modal').style.display = 'none';
        });
    });
    
    // Close modal when clicking outside the modal content
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    });
    
    // Add user submission
    const addUserSubmitBtn = userModal.querySelector('.modal-footer .action-btn.primary');
    if (addUserSubmitBtn) {
        addUserSubmitBtn.addEventListener('click', function() {
            const firstName = document.getElementById('user-first-name').value;
            const lastName = document.getElementById('user-last-name').value;
            const email = document.getElementById('user-email').value;
            const role = document.getElementById('user-role').value;
            const department = document.getElementById('user-department').value;
            const status = document.getElementById('user-status').value;
            
            if (!firstName || !lastName || !email || !role || !department) {
                alert('Veuillez remplir tous les champs obligatoires.');
                return;
            }
            
            // Simulating adding a user to the table
            const usersTable = document.querySelector('.users-table tbody');
            const newRow = document.createElement('tr');
            
            const roleLabel = document.getElementById('user-role').options[document.getElementById('user-role').selectedIndex].text;
            const departmentLabel = document.getElementById('user-department').options[document.getElementById('user-department').selectedIndex].text;
            
            newRow.innerHTML = `
                <td>
                    <div class="user-info-cell">
                        <div class="user-avatar">
                            <img src="lovable-uploads/3bec0184-d9fb-47a0-b398-880496412872.png" alt="User">
                        </div>
                        <div class="user-details">
                            <div class="user-name">${firstName} ${lastName}</div>
                            <div class="user-id">ID: ${role.toUpperCase()}-${Math.floor(1000 + Math.random() * 9000)}</div>
                        </div>
                    </div>
                </td>
                <td>${email}</td>
                <td><span class="badge role-badge ${role}">${roleLabel}</span></td>
                <td>${departmentLabel}</td>
                <td><span class="badge status-badge ${status}">${status === 'active' ? 'Actif' : 'Inactif'}</span></td>
                <td>Jamais connecté</td>
                <td>
                    <div class="action-buttons">
                        <button class="icon-btn" title="Éditer"><i class="fas fa-edit"></i></button>
                        <button class="icon-btn" title="Permissions"><i class="fas fa-key"></i></button>
                        <button class="icon-btn" title="Réinitialiser mot de passe"><i class="fas fa-lock"></i></button>
                        <button class="icon-btn delete" title="Désactiver"><i class="fas fa-user-slash"></i></button>
                    </div>
                </td>
            `;
            
            usersTable.prepend(newRow);
            setupActionButtons(); // Re-setup to include the new row
            
            // Close modal and reset form
            userModal.style.display = 'none';
            document.getElementById('user-first-name').value = '';
            document.getElementById('user-last-name').value = '';
            document.getElementById('user-email').value = '';
            document.getElementById('user-role').value = '';
            document.getElementById('user-department').value = '';
            document.getElementById('user-status').value = 'active';
            
            showNotification(`Utilisateur ${firstName} ${lastName} ajouté avec succès`, 'success');
        });
    }
    
    // Add Role modal functionality
    const addRoleBtn = document.getElementById('add-role-btn');
    if (addRoleBtn) {
        addRoleBtn.addEventListener('click', function() {
            const roleName = prompt('Nom du nouveau rôle:');
            if (!roleName) return;
            
            const rolesList = document.querySelector('.roles-list');
            const newRoleItem = document.createElement('div');
            newRoleItem.className = 'role-item';
            newRoleItem.innerHTML = `
                <div class="role-name">
                    <i class="fas fa-user"></i>
                    <span>${roleName}</span>
                </div>
                <div class="role-user-count">
                    <span>0 utilisateurs</span>
                </div>
            `;
            
            rolesList.appendChild(newRoleItem);
            setupRoleSelection(); // Re-setup to include new role
            showNotification(`Rôle "${roleName}" créé`, 'success');
        });
    }
    
    // Edit Permissions functionality
    const editPermissionsBtn = document.getElementById('edit-permissions-btn');
    if (editPermissionsBtn) {
        editPermissionsBtn.addEventListener('click', function() {
            const roleName = document.querySelector('.permissions-header h3').textContent.replace('Permissions pour: ', '');
            alert(`Édition des permissions pour "${roleName}" - Fonctionnalité à implémenter`);
        });
    }
}

// Function to setup pagination
function setupPagination() {
    const paginationButtons = document.querySelectorAll('.pagination-btn');
    
    paginationButtons.forEach(button => {
        button.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            const paginationContainer = this.closest('.pagination');
            const pageInfo = paginationContainer.querySelector('.page-info');
            const [_, currentPage, totalPages] = pageInfo.textContent.match(/Page (\d+) sur (\d+)/);
            
            let newPage = parseInt(currentPage);
            if (action === 'prev' && newPage > 1) {
                newPage--;
            } else if (action === 'next' && newPage < parseInt(totalPages)) {
                newPage++;
            }
            
            pageInfo.textContent = `Page ${newPage} sur ${totalPages}`;
            
            // Disable/enable buttons based on current page
            paginationContainer.querySelector('[data-action="prev"]').disabled = newPage === 1;
            paginationContainer.querySelector('[data-action="next"]').disabled = newPage === parseInt(totalPages);
            
            // Simulate loading different data for the new page
            if (paginationContainer.closest('#users-tab')) {
                console.log(`Loading users page ${newPage}`);
            } else if (paginationContainer.closest('#logs-tab')) {
                console.log(`Loading logs page ${newPage}`);
            }
        });
    });
}

// Function to setup action buttons
function setupActionButtons() {
    // User table action buttons
    const userActionButtons = document.querySelectorAll('.users-table .action-buttons .icon-btn');
    
    userActionButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userName = this.closest('tr').querySelector('.user-name').textContent;
            const action = this.getAttribute('title');
            
            if (this.classList.contains('delete')) {
                if (confirm(`Êtes-vous sûr de vouloir désactiver l'utilisateur "${userName}" ?`)) {
                    const statusCell = this.closest('tr').querySelector('td:nth-child(5) span');
                    statusCell.className = 'badge status-badge inactive';
                    statusCell.textContent = 'Inactif';
                    
                    // Change action button to activate
                    const actionCell = this.closest('td');
                    actionCell.innerHTML = `
                        <div class="action-buttons">
                            <button class="icon-btn" title="Éditer"><i class="fas fa-edit"></i></button>
                            <button class="icon-btn" title="Permissions"><i class="fas fa-key"></i></button>
                            <button class="icon-btn" title="Réinitialiser mot de passe"><i class="fas fa-lock"></i></button>
                            <button class="icon-btn activate" title="Activer"><i class="fas fa-user-check"></i></button>
                        </div>
                    `;
                    
                    setupActionButtons(); // Re-setup to include the new button
                    showNotification(`Utilisateur "${userName}" désactivé`, 'info');
                }
            } else if (this.classList.contains('activate')) {
                if (confirm(`Êtes-vous sûr de vouloir activer l'utilisateur "${userName}" ?`)) {
                    const statusCell = this.closest('tr').querySelector('td:nth-child(5) span');
                    statusCell.className = 'badge status-badge active';
                    statusCell.textContent = 'Actif';
                    
                    // Change action button to deactivate
                    const actionCell = this.closest('td');
                    actionCell.innerHTML = `
                        <div class="action-buttons">
                            <button class="icon-btn" title="Éditer"><i class="fas fa-edit"></i></button>
                            <button class="icon-btn" title="Permissions"><i class="fas fa-key"></i></button>
                            <button class="icon-btn" title="Réinitialiser mot de passe"><i class="fas fa-lock"></i></button>
                            <button class="icon-btn delete" title="Désactiver"><i class="fas fa-user-slash"></i></button>
                        </div>
                    `;
                    
                    setupActionButtons(); // Re-setup to include the new button
                    showNotification(`Utilisateur "${userName}" activé`, 'success');
                }
            } else if (action === 'Éditer') {
                alert(`Édition de l'utilisateur "${userName}" - Fonctionnalité à implémenter`);
            } else if (action === 'Permissions') {
                alert(`Gestion des permissions pour "${userName}" - Fonctionnalité à implémenter`);
            } else if (action === 'Réinitialiser mot de passe') {
                if (confirm(`Envoyer un email de réinitialisation de mot de passe à "${userName}" ?`)) {
                    showNotification(`Email de réinitialisation envoyé à ${userName}`, 'success');
                }
            }
        });
    });
}

// Utility function to show notifications
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 
                         (type === 'error' ? 'fa-exclamation-circle' : 
                         'fa-info-circle')}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close"><i class="fas fa-times"></i></button>
    `;
    
    // Add notification to body
    document.body.appendChild(notification);
    
    // Show notification with animation
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    // Set up close button
    notification.querySelector('.notification-close').addEventListener('click', function() {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    });
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        if (document.body.contains(notification)) {
            notification.classList.remove('show');
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    notification.remove();
                }
            }, 300);
        }
    }, 5000);
}

// Add notification styles if not already in CSS
if (!document.getElementById('notification-styles')) {
    const styleElement = document.createElement('style');
    styleElement.id = 'notification-styles';
    styleElement.textContent = `
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 400px;
            transform: translateX(120%);
            transition: transform 0.3s ease;
            z-index: 9999;
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .notification-content i {
            font-size: 18px;
        }
        
        .notification.success .notification-content i {
            color: #28a745;
        }
        
        .notification.error .notification-content i {
            color: #dc3545;
        }
        
        .notification.info .notification-content i {
            color: #0078D7;
        }
        
        .notification-close {
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            font-size: 14px;
            padding: 0;
            margin-left: 16px;
        }
    `;
    document.head.appendChild(styleElement);
}
