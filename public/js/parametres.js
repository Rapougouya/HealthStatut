/**
 * Gestionnaire de paramètres
 * Version optimisée et modularisée
 */

// Initialisation au chargement du document
document.addEventListener('DOMContentLoaded', function() {
  // Initialiser tous les modules
  TabManager.init();
  ProfileManager.init();
  ThemeManager.init();
  FormManager.init();
  ModalManager.init();
  SettingsStorage.loadAll();
});

/**
 * Gestion des onglets
 */
const TabManager = {
  init() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
      button.addEventListener('click', () => this.switchTab(button, tabButtons, tabContents));
    });
    
    // Activer l'onglet sauvegardé ou le premier par défaut
    const savedTab = localStorage.getItem('activeSettingsTab');
    const tabToActivate = savedTab 
      ? document.querySelector(`.tab-btn[data-tab="${savedTab}"]`) 
      : tabButtons[0];
      
    if (tabToActivate) {
      this.switchTab(tabToActivate, tabButtons, tabContents);
    }
  },
  
  switchTab(selectedTab, allTabs, allContents) {
    // Désactiver tous les onglets
    allTabs.forEach(tab => tab.classList.remove('active'));
    allContents.forEach(content => content.classList.remove('active'));
    
    // Activer l'onglet sélectionné
    selectedTab.classList.add('active');
    const tabId = selectedTab.getAttribute('data-tab');
    document.getElementById(`${tabId}-tab`).classList.add('active');
    
    // Sauvegarder la préférence
    localStorage.setItem('activeSettingsTab', tabId);
  }
};

/**
 * Gestion du profil et de l'image
 */
const ProfileManager = {
  init() {
    this.setupProfileImageUpload();
  },
  
  setupProfileImageUpload() {
    const profileUpload = document.getElementById('profile-upload');
    const profilePreview = document.getElementById('profile-preview');
    const removeProfileBtn = document.getElementById('remove-profile-image');
    
    if (!profileUpload || !profilePreview || !removeProfileBtn) return;
    
    // Gérer l'upload d'image
    profileUpload.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          profilePreview.src = e.target.result;
          localStorage.setItem('profileImage', e.target.result);
          NotificationManager.show('Photo de profil mise à jour', 'success');
        };
        reader.readAsDataURL(file);
      }
    });
    
    // Gérer la suppression d'image
    removeProfileBtn.addEventListener('click', () => {
      profilePreview.src = 'lovable-uploads/3bec0184-d9fb-47a0-b398-880496412872.png';
      localStorage.removeItem('profileImage');
      profileUpload.value = '';
      NotificationManager.show('Photo de profil supprimée', 'info');
    });
    
    // Charger l'image sauvegardée
    const savedImage = localStorage.getItem('profileImage');
    if (savedImage) {
      profilePreview.src = savedImage;
    }
  }
};

/**
 * Gestion des thèmes
 */
const ThemeManager = {
  init() {
    this.setupThemeSwitching();
  },
  
  setupThemeSwitching() {
    const themeOptions = document.querySelectorAll('.theme-option');
    if (!themeOptions.length) return;
    
    // Charger le thème sauvegardé
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    const themeRadio = document.getElementById(`${savedTheme}-theme`);
    if (themeRadio) {
      themeRadio.checked = true;
    }
    
    // Configurer la sélection de thème
    themeOptions.forEach(option => {
      option.addEventListener('click', () => {
        const themeValue = option.getAttribute('data-theme');
        const radioInput = option.querySelector('input[type="radio"]');
        
        // Mettre à jour les boutons radio
        document.querySelectorAll('input[name="theme"]').forEach(input => {
          input.checked = false;
        });
        radioInput.checked = true;
        
        // Appliquer le thème
        document.documentElement.setAttribute('data-theme', themeValue);
        localStorage.setItem('theme', themeValue);
        
        NotificationManager.show(
          `Thème ${radioInput.nextElementSibling.textContent.trim()} appliqué`, 
          'success'
        );
      });
    });
  }
};

/**
 * Gestion des formulaires
 */
const FormManager = {
  init() {
    this.setupPasswordStrengthMeter();
    this.setupFormSaving();
  },
  
  setupPasswordStrengthMeter() {
    const newPasswordInput = document.getElementById('new-password');
    const strengthProgress = document.querySelector('.strength-progress');
    const strengthText = document.querySelector('.strength-text');
    
    if (!newPasswordInput || !strengthProgress || !strengthText) return;
    
    newPasswordInput.addEventListener('input', function() {
      const password = this.value;
      const strength = PasswordValidator.calculateStrength(password);
      
      // Mettre à jour la barre de progression
      strengthProgress.style.width = strength.score + '%';
      strengthProgress.style.backgroundColor = strength.color;
      strengthText.textContent = `Force du mot de passe: ${strength.status}`;
    });
  },
  
  setupFormSaving() {
    const saveBtn = document.getElementById('save-settings-btn');
    
    if (!saveBtn) return;
    
    saveBtn.addEventListener('click', () => {
      // Désactiver le bouton et afficher l'état de chargement
      saveBtn.disabled = true;
      const originalText = saveBtn.innerHTML;
      saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
      
      // Collecter les données du formulaire
      const settingsData = this.collectFormData();
      
      // Enregistrer dans le localStorage
      SettingsStorage.saveSettings(settingsData);
      
      // Simuler un délai réseau
      setTimeout(() => {
        // Réactiver le bouton
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
        
        // Afficher la notification de succès
        NotificationManager.show('Paramètres enregistrés avec succès', 'success');
      }, 1000);
    });
  },
  
  collectFormData() {
    return {
      profile: {
        firstName: this.getFieldValue('first-name'),
        lastName: this.getFieldValue('last-name'),
        specialty: this.getFieldValue('specialty'),
        department: this.getFieldValue('department'),
        phone: this.getFieldValue('phone'),
        office: this.getFieldValue('office')
      },
      account: {
        email: this.getFieldValue('email'),
        twoFactorAuth: this.getCheckboxValue('two-factor-auth')
      },
      appearance: {
        theme: document.querySelector('input[name="theme"]:checked')?.id.replace('-theme', ''),
        language: this.getFieldValue('language'),
        dateFormat: this.getFieldValue('date-format'),
        timeFormat: this.getFieldValue('time-format')
      },
      notifications: {
        patientAlerts: this.getChannelValues('patient-alert'),
        newPatients: this.getChannelValues('new-patient'),
        system: this.getChannelValues('system')
      },
      medical: {
        unitsSystem: this.getFieldValue('units-system'),
        thresholds: {
          heartRate: this.getThresholdValues('heart-rate'),
          bpSystolic: this.getThresholdValues('bp-systolic'),
          bpDiastolic: this.getThresholdValues('bp-diastolic'),
          temperature: this.getThresholdValues('temp'),
          spo2: {
            low: this.getFieldValue('spo2-low'),
            critical: this.getFieldValue('spo2-critical')
          },
          glucose: {
            low: this.getFieldValue('glucose-low'),
            high: this.getFieldValue('glucose-high'),
            criticalLow: this.getFieldValue('glucose-critical-low'),
            criticalHigh: this.getFieldValue('glucose-critical-high')
          }
        }
      }
    };
  },
  
  getFieldValue(id) {
    return document.getElementById(id)?.value || '';
  },
  
  getCheckboxValue(id) {
    return document.getElementById(id)?.checked || false;
  },
  
  getChannelValues(prefix) {
    return {
      app: this.getCheckboxValue(`${prefix}-app`),
      email: this.getCheckboxValue(`${prefix}-email`),
      sms: this.getCheckboxValue(`${prefix}-sms`)
    };
  },
  
  getThresholdValues(prefix) {
    return {
      low: this.getFieldValue(`${prefix}-low`),
      high: this.getFieldValue(`${prefix}-high`),
      critical: this.getFieldValue(`${prefix}-critical`)
    };
  }
};

/**
 * Validation de mot de passe
 */
const PasswordValidator = {
  calculateStrength(password) {
    let score = 0;
    let status = '';
    let color = '';
    
    // Calculer la force du mot de passe
    if (password.length >= 8) score += 25;
    if (password.match(/[A-Z]/)) score += 25;
    if (password.match(/[0-9]/)) score += 25;
    if (password.match(/[^a-zA-Z0-9]/)) score += 25;
    
    // Définir le statut et la couleur
    if (score <= 25) {
      color = '#dc3545'; // rouge
      status = 'Très faible';
    } else if (score <= 50) {
      color = '#ffc107'; // jaune
      status = 'Faible';
    } else if (score <= 75) {
      color = '#fd7e14'; // orange
      status = 'Moyen';
    } else {
      color = '#28a745'; // vert
      status = 'Fort';
    }
    
    return { score, status, color };
  }
};

/**
 * Gestion des modales
 */
const ModalManager = {
  init() {
    this.setupPasswordModal();
    this.setupSessionsModal();
    this.setupCloseModalButtons();
    this.setupOutsideClickClose();
  },
  
  setupPasswordModal() {
    const changePasswordBtn = document.getElementById('change-password-btn');
    const passwordModal = document.getElementById('passwordModal');
    const savePasswordBtn = document.getElementById('save-password-btn');
    
    if (changePasswordBtn && passwordModal) {
      changePasswordBtn.addEventListener('click', () => {
        passwordModal.style.display = 'flex';
      });
    }
    
    if (savePasswordBtn) {
      savePasswordBtn.addEventListener('click', () => {
        const currentPassword = document.getElementById('current-password').value;
        const newPassword = document.getElementById('new-password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        
        if (!currentPassword || !newPassword || !confirmPassword) {
          NotificationManager.show('Veuillez remplir tous les champs', 'error');
          return;
        }
        
        if (newPassword !== confirmPassword) {
          NotificationManager.show('Les mots de passe ne correspondent pas', 'error');
          return;
        }
        
        // Simuler le changement de mot de passe
        passwordModal.style.display = 'none';
        document.getElementById('current-password').value = '';
        document.getElementById('new-password').value = '';
        document.getElementById('confirm-password').value = '';
        
        NotificationManager.show('Mot de passe modifié avec succès', 'success');
      });
    }
  },
  
  setupSessionsModal() {
    const viewSessionsBtn = document.getElementById('view-sessions-btn');
    const sessionsModal = document.getElementById('sessionsModal');
    const logoutAllBtn = document.getElementById('logout-all-btn');
    const sessionDisconnectBtns = document.querySelectorAll('.session-item .icon-btn.delete');
    
    if (viewSessionsBtn && sessionsModal) {
      viewSessionsBtn.addEventListener('click', () => {
        sessionsModal.style.display = 'flex';
      });
    }
    
    if (logoutAllBtn) {
      logoutAllBtn.addEventListener('click', () => {
        if (confirm('Êtes-vous sûr de vouloir déconnecter toutes les autres sessions ?')) {
          const sessionItems = document.querySelectorAll('.session-item:not(.current)');
          sessionItems.forEach(item => item.remove());
          
          NotificationManager.show('Toutes les autres sessions ont été déconnectées', 'success');
        }
      });
    }
    
    if (sessionDisconnectBtns.length > 0) {
      sessionDisconnectBtns.forEach(btn => {
        btn.addEventListener('click', function() {
          const sessionItem = this.closest('.session-item');
          const deviceName = sessionItem.querySelector('h4').textContent;
          
          if (confirm(`Êtes-vous sûr de vouloir déconnecter "${deviceName}" ?`)) {
            sessionItem.remove();
            NotificationManager.show(`Session "${deviceName}" déconnectée`, 'success');
          }
        });
      });
    }
  },
  
  setupCloseModalButtons() {
    const closeModalBtns = document.querySelectorAll('.close-modal, .modal .cancel-btn');
    closeModalBtns.forEach(btn => {
      btn.addEventListener('click', function() {
        this.closest('.modal').style.display = 'none';
      });
    });
  },
  
  setupOutsideClickClose() {
    window.addEventListener('click', (event) => {
      if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
      }
    });
  }
};

/**
 * Gestionnaire de stockage des paramètres
 */
const SettingsStorage = {
  STORAGE_KEY: 'userSettings',
  
  saveSettings(settings) {
    localStorage.setItem(this.STORAGE_KEY, JSON.stringify(settings));
  },
  
  loadAll() {
    const savedSettings = localStorage.getItem(this.STORAGE_KEY);
    if (!savedSettings) return;
    
    try {
      const settings = JSON.parse(savedSettings);
      this.applySettings(settings);
    } catch (error) {
      console.error('Erreur lors du chargement des paramètres sauvegardés:', error);
    }
  },
  
  applySettings(settings) {
    // Paramètres du profil
    if (settings.profile) {
      FormUtils.setFormValue('first-name', settings.profile.firstName);
      FormUtils.setFormValue('last-name', settings.profile.lastName);
      FormUtils.setFormValue('specialty', settings.profile.specialty);
      FormUtils.setFormValue('department', settings.profile.department);
      FormUtils.setFormValue('phone', settings.profile.phone);
      FormUtils.setFormValue('office', settings.profile.office);
    }
    
    // Paramètres du compte
    if (settings.account) {
      FormUtils.setFormValue('email', settings.account.email);
      FormUtils.setCheckboxValue('two-factor-auth', settings.account.twoFactorAuth);
    }
    
    // Paramètres d'apparence
    if (settings.appearance) {
      if (settings.appearance.theme) {
        FormUtils.setRadioValue(`${settings.appearance.theme}-theme`);
      }
      FormUtils.setFormValue('language', settings.appearance.language);
      FormUtils.setFormValue('date-format', settings.appearance.dateFormat);
      FormUtils.setFormValue('time-format', settings.appearance.timeFormat);
    }
    
    // Paramètres de notification
    if (settings.notifications) {
      this.applyNotificationSettings(settings.notifications);
    }
    
    // Paramètres médicaux
    if (settings.medical) {
      this.applyMedicalSettings(settings.medical);
    }
  },
  
  applyNotificationSettings(notifications) {
    if (notifications.patientAlerts) {
      FormUtils.setCheckboxValue('patient-alert-app', notifications.patientAlerts.app);
      FormUtils.setCheckboxValue('patient-alert-email', notifications.patientAlerts.email);
      FormUtils.setCheckboxValue('patient-alert-sms', notifications.patientAlerts.sms);
    }
    
    if (notifications.newPatients) {
      FormUtils.setCheckboxValue('new-patient-app', notifications.newPatients.app);
      FormUtils.setCheckboxValue('new-patient-email', notifications.newPatients.email);
      FormUtils.setCheckboxValue('new-patient-sms', notifications.newPatients.sms);
    }
    
    if (notifications.system) {
      FormUtils.setCheckboxValue('system-app', notifications.system.app);
      FormUtils.setCheckboxValue('system-email', notifications.system.email);
      FormUtils.setCheckboxValue('system-sms', notifications.system.sms);
    }
  },
  
  applyMedicalSettings(medical) {
    FormUtils.setFormValue('units-system', medical.unitsSystem);
    
    if (medical.thresholds) {
      const { thresholds } = medical;
      
      if (thresholds.heartRate) {
        this.applyThresholdValues('heart-rate', thresholds.heartRate);
      }
      
      if (thresholds.bpSystolic) {
        this.applyThresholdValues('bp-systolic', thresholds.bpSystolic);
      }
      
      if (thresholds.bpDiastolic) {
        this.applyThresholdValues('bp-diastolic', thresholds.bpDiastolic);
      }
      
      if (thresholds.temperature) {
        this.applyThresholdValues('temp', thresholds.temperature);
      }
      
      if (thresholds.spo2) {
        FormUtils.setFormValue('spo2-low', thresholds.spo2.low);
        FormUtils.setFormValue('spo2-critical', thresholds.spo2.critical);
      }
      
      if (thresholds.glucose) {
        FormUtils.setFormValue('glucose-low', thresholds.glucose.low);
        FormUtils.setFormValue('glucose-high', thresholds.glucose.high);
        FormUtils.setFormValue('glucose-critical-low', thresholds.glucose.criticalLow);
        FormUtils.setFormValue('glucose-critical-high', thresholds.glucose.criticalHigh);
      }
    }
  },
  
  applyThresholdValues(prefix, values) {
    FormUtils.setFormValue(`${prefix}-low`, values.low);
    FormUtils.setFormValue(`${prefix}-high`, values.high);
    FormUtils.setFormValue(`${prefix}-critical`, values.critical);
  }
};

/**
 * Utilitaires pour les formulaires
 */
const FormUtils = {
  setFormValue(id, value) {
    const element = document.getElementById(id);
    if (element && value !== undefined && value !== null) {
      element.value = value;
    }
  },
  
  setCheckboxValue(id, checked) {
    const element = document.getElementById(id);
    if (element && checked !== undefined) {
      element.checked = checked;
    }
  },
  
  setRadioValue(id) {
    const element = document.getElementById(id);
    if (element) {
      element.checked = true;
    }
  }
};

/**
 * Gestionnaire de notifications
 */
const NotificationManager = {
  show(message, type = 'info') {
    // Créer l'élément de notification
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
      <div class="notification-content">
        <i class="fas ${this.getIconClass(type)}"></i>
        <span>${message}</span>
      </div>
      <button class="notification-close"><i class="fas fa-times"></i></button>
    `;
    
    // Ajouter la notification au corps du document
    document.body.appendChild(notification);
    
    // Afficher la notification avec animation
    setTimeout(() => {
      notification.classList.add('show');
    }, 10);
    
    // Configurer le bouton de fermeture
    notification.querySelector('.notification-close').addEventListener('click', () => {
      this.hide(notification);
    });
    
    // Auto-masquer après 5 secondes
    setTimeout(() => {
      if (document.body.contains(notification)) {
        this.hide(notification);
      }
    }, 5000);
  },
  
  hide(notification) {
    notification.classList.remove('show');
    setTimeout(() => {
      if (document.body.contains(notification)) {
        notification.remove();
      }
    }, 300);
  },
  
  getIconClass(type) {
    switch (type) {
      case 'success':
        return 'fa-check-circle';
      case 'error':
        return 'fa-exclamation-circle';
      default:
        return 'fa-info-circle';
    }
  }
};
