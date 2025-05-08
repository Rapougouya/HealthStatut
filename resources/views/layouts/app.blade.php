<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HealthStatut')</title>
    
    {{-- Google Fonts --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    
    {{-- Remix Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    
    {{-- Base Styles --}}
    
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">

    @yield('styles')
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        @include('patials.sidebar')
        
        <!-- Main Content -->
        <main class="main-content">
            <header class="main-header">
                <div class="app-controls">
                    <button class="menu-button"><i class="ri-menu-line"></i></button>
                </div>
                <div class="user-controls">
                    <button class="notification-btn" id="notificationBell">
                      <i class="ri-notification-line notification-icon"></i>
                      <span class="notification-counter">{{ $notificationsCount ?? 3 }}</span>
                    </button>
                    <div class="notification-dropdown" id="notificationDropdown">
                        @forelse($notifications ?? [] as $notification)
                            <div class="notification-item">
                                <div class="notification-header">
                                    <div class="notification-title {{ $notification->type === 'alert' ? 'alert' : '' }}">
                                        <i class="icon-{{ $notification->icon }}"></i> {{ $notification->title }}
                                    </div>
                                    <div class="notification-time">{{ $notification->time }}</div>
                                </div>
                                <div class="notification-content">
                                    {{ $notification->content }}
                                </div>
                            </div>
                        @empty
                            <div class="notification-item">
                                <div class="notification-header">
                                    <div class="notification-title alert"><i class="icon-alert"></i> Alerte patient</div>
                                    <div class="notification-time">Il y a 5 min</div>
                                </div>
                                <div class="notification-content">
                                    Émilie Carter présente une fréquence cardiaque élevée (120 bpm)
                                </div>
                            </div>
                            <div class="notification-item">
                                <div class="notification-header">
                                    <div class="notification-title alert"><i class="icon-temp"></i> Température élevée</div>
                                    <div class="notification-time">Il y a 20 min</div>
                                </div>
                                <div class="notification-content">
                                    Thomas Dubois présente une température de 38.7°C
                                </div>
                            </div>
                            <div class="notification-item">
                                <div class="notification-header">
                                    <div class="notification-title"><i class="icon-calendar"></i> Rendez-vous</div>
                                    <div class="notification-time">Il y a 1h</div>
                                </div>
                                <div class="notification-content">
                                    Rappel: Consultation avec Daniel Moreau dans 15 minutes
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <button class="settings-btn"><i class="ri-settings-line"></i></i></button>
                    <div class="user-profile">
                        <div class="user-info">
                            <h3 class="user-name">{{ auth()->user()->name ?? 'Dr. Sophie Martin' }}</h3>
                            <p class="user-id">{{ auth()->user()->role->name ?? 'Médecin spécialiste' }}</p>
                        </div>
                        <div class="user-avatar">
                            <img src="{{ auth()->user()->avatar ?? 'https://randomuser.me/api/portraits/women/65.jpg' }}" alt="User">
                        </div>
                    </div>
                </div>
            </header>

            @yield('content')
        </main>
    </div>
    
    <!-- Toast Container for Notifications -->
    <div class="toast-container" id="toastContainer"></div>
    
    {{-- Scripts --}}
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="{{ asset('js/dashb.js') }}"></script>
    @yield('scripts')
</body>
</html>