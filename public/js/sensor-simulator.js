/**
 * Simulateur de capteur pour tester la réception de données en temps réel
 */
class SensorSimulator {
    constructor(sensorId, type, apiEndpoint) {
        this.sensorId = sensorId;
        this.type = type;
        this.apiEndpoint = apiEndpoint || '/api/sensors/data';
        this.interval = 5000; // 5 secondes par défaut
        this.active = false;
        this.batteryLevel = 100; // Commence avec batterie pleine
        this.signalStrength = -50; // Bon signal par défaut (dBm)
        this.timer = null;
        
        this.valueRanges = {
            'temperature': { min: 36.0, max: 38.0, precision: 1 },
            'heartrate': { min: 60, max: 100, precision: 0 },
            'blood_pressure': { min: 110, max: 140, precision: 0 },
            'oxygen': { min: 94, max: 100, precision: 0 },
            'default': { min: 0, max: 100, precision: 1 }
        };
    }
    
    start() {
        if (this.active) return;
        
        this.active = true;
        this.sendReading();
        this.timer = setInterval(() => this.sendReading(), this.interval);
        console.log(`Capteur ${this.sensorId} démarré avec intervalle de ${this.interval}ms`);
    }
    
    stop() {
        if (!this.active) return;
        
        clearInterval(this.timer);
        this.active = false;
        console.log(`Capteur ${this.sensorId} arrêté`);
    }
    
    setInterval(ms) {
        this.interval = ms;
        if (this.active) {
            this.stop();
            this.start();
        }
    }
    
    generateValue() {
        const range = this.valueRanges[this.type] || this.valueRanges.default;
        const value = Math.random() * (range.max - range.min) + range.min;
        return parseFloat(value.toFixed(range.precision));
    }
    
    updateBatteryLevel() {
        // Simuler une décharge progressive de la batterie
        if (Math.random() < 0.1) {
            this.batteryLevel = Math.max(0, this.batteryLevel - 1);
        }
    }
    
    updateSignalStrength() {
        // Simuler des fluctuations de signal WiFi
        const fluctuation = Math.floor(Math.random() * 10) - 5; // -5 à +5
        this.signalStrength = Math.min(-30, Math.max(-90, this.signalStrength + fluctuation));
    }
    
    sendReading() {
        this.updateBatteryLevel();
        this.updateSignalStrength();
        
        const value = this.generateValue();
        const data = {
            sensor_id: this.sensorId,
            value: value,
            timestamp: new Date().toISOString(),
            raw_data: {
                value: value,
                unit: this.getUnit(),
                type: this.type
            },
            signal_strength: this.signalStrength,
            battery_level: this.batteryLevel,
            status_code: 200
        };
        
        fetch(this.apiEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            console.log(`Lecture envoyée pour ${this.sensorId}:`, result);
        })
        .catch(error => {
            console.error('Erreur lors de l\'envoi des données:', error);
        });
    }
    
    getUnit() {
        switch(this.type) {
            case 'temperature': return '°C';
            case 'heartrate': return 'bpm';
            case 'blood_pressure': return 'mmHg';
            case 'oxygen': return '%';
            default: return 'unit';
        }
    }
}

// Usage:
// const simulator = new SensorSimulator('TEMP001', 'temperature');
// simulator.start();