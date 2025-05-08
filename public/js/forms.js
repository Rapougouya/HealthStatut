document.addEventListener('DOMContentLoaded', function () {
    // Fonction pour afficher un toast
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <div class="toast-icon">
                    <i class="icon-${type === 'success' ? 'check' : 'alert'}"></i>
                </div>
                <div class="toast-message">${message}</div>
            </div>
        `;

        const container = document.getElementById('toastContainer');
        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('show');
        }, 100);

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Gestion du formulaire d'ajout de patient avec validation capteurs
    const addPatientForm = document.getElementById('addPatientForm');
    if (addPatientForm) {
        addPatientForm.addEventListener('submit', function (e) {
            const sensorSelect = document.getElementById('sensors');
            if (sensorSelect && sensorSelect.selectedOptions.length < 1) {
                e.preventDefault();
                sensorSelect.focus();
                sensorSelect.classList.add('shake');
                setTimeout(() => sensorSelect.classList.remove('shake'), 520);
                alert('Veuillez sélectionner au moins un capteur pour le patient.');
                return;
            }

            // Ici pas besoin de preventDefault s'il s'agit d'un formulaire classique
            // Sinon, vous pouvez faire une requête AJAX si besoin
        });
    }

    // Gestion du formulaire de configuration de capteur
    const configureSensorForm = document.getElementById('configureSensorForm');
    if (configureSensorForm) {
        configureSensorForm.addEventListener('submit', function (e) {
            e.preventDefault();
            showToast('Capteur configuré avec succès');
            // Ajoutez ici une logique AJAX si souhaité
        });

        // Mise à jour dynamique des seuils en fonction du type de capteur
        const typeSelect = document.getElementById('type');
        if (typeSelect) {
            typeSelect.addEventListener('change', function () {
                updateThresholds(this.value);
            });
        }
    }

    // Gestion du formulaire de génération de rapport
    const generateReportForm = document.getElementById('generateReportForm');
    if (generateReportForm) {
        generateReportForm.addEventListener('submit', function (e) {
            e.preventDefault();
            showToast('Rapport généré avec succès');
            // Ajouter logique si asynchrone
        });

        // Validation des dates
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        if (startDate && endDate) {
            endDate.addEventListener('change', function () {
                if (startDate.value && this.value < startDate.value) {
                    showToast('La date de fin doit être postérieure à la date de début', 'error');
                    this.value = startDate.value;
                }
            });
        }
    }

    // Gestion du formulaire des paramètres d'alerte
    const alertsSettingsForm = document.getElementById('alertsSettingsForm');
    if (alertsSettingsForm) {
        alertsSettingsForm.addEventListener('submit', function (e) {
            e.preventDefault();
            showToast('Paramètres d\'alerte enregistrés');
        });
    }

    // Fonction pour mettre à jour les seuils en fonction du type de capteur
    function updateThresholds(sensorType) {
        const defaultThresholds = {
            'heartrate': { min: 60, max: 100 },
            'temperature': { min: 36.0, max: 37.8 },
            'blood_pressure': { min: 90, max: 140 },
            'oxygen': { min: 95, max: 100 }
        };

        const minInput = document.getElementById('min_value');
        const maxInput = document.getElementById('max_value');

        if (minInput && maxInput && defaultThresholds[sensorType]) {
            minInput.value = defaultThresholds[sensorType].min;
            maxInput.value = defaultThresholds[sensorType].max;
        }
    }

    // Animation des champs de formulaire (focus / blur)
    const groups = document.querySelectorAll('.form-group-modern');
    groups.forEach(group => {
        const inputs = group.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                group.classList.add('focused');
            });
            input.addEventListener('blur', () => {
                group.classList.remove('focused');
            });
        });
    });

    // Affichage du nombre d'éléments sélectionnés pour les multi-selects
    const multiSelects = document.querySelectorAll('.select-multi');
    multiSelects.forEach(select => {
        select.addEventListener('change', function () {
            const selectedCount = Array.from(this.selectedOptions).length;
            const label = this.parentElement.querySelector('label');

            if (selectedCount > 0) {
                const badge = document.createElement('span');
                badge.className = 'selection-badge';
                badge.textContent = selectedCount;

                const oldBadge = label.querySelector('.selection-badge');
                if (oldBadge) oldBadge.remove();

                label.appendChild(badge);
            } else {
                const badge = label.querySelector('.selection-badge');
                if (badge) badge.remove();
            }
        });
    });
});
