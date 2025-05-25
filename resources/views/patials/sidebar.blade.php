<aside class="sidebar">
    <div class="sidebar-header">
        <h2>HealthStatut</h2>
    </div>
    <div class="sidebar-nav">
        <ul>
            {{-- Accueil - Accessible à tous --}}
            <li class="{{ Route::is('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> <span>Accueil</span></a>
            </li>

            {{-- Patients - Accessible aux admins et médecins --}}
            @if(auth()->user()->role->nom === 'admin' || auth()->user()->role->nom === 'medecin')
            <li class="{{ Route::is('patients.*') ? 'active' : '' }}">
                <a href="{{ route('patients.index') }}"><i class="fas fa-user"></i> <span>Patients</span></a>
            </li>
            @endif

            {{-- Alertes - Accessible aux admins et médecins --}}
            @if(auth()->user()->role->nom === 'admin' || auth()->user()->role->nom === 'medecin')
            <li class="{{ Route::is('alertes.*') ? 'active' : '' }}">
                <a href="{{ route('alertes.index') }}"><i class="fas fa-bell"></i> <span>Alertes</span></a>
            </li>
            @endif

            {{-- Capteurs - Accessible aux admins et techniciens --}}
            @if(auth()->user()->role->nom === 'admin' || auth()->user()->role->nom === 'technicien')
            <li class="{{ Route::is('sensors.*') || Route::is('capteurs.*') ? 'active' : '' }}">
                <a href="{{ route('sensors.index') }}"><i class="fas fa-microchip"></i> <span>Capteurs</span></a>
            </li>
            @endif

            {{-- Rapports - Accessible aux admins et médecins --}}
            @if(auth()->user()->role->nom === 'admin' || auth()->user()->role->nom === 'medecin')
            <li class="{{ Route::is('rapports.*') || Route::is('reports.*') ? 'active' : '' }}">
                <a href="{{ route('reports.generate') }}"><i class="fas fa-file-alt"></i> <span>Rapports</span></a>
            </li>
            @endif

            {{-- Historique - Accessible aux admins et médecins --}}
            @if(auth()->user()->role->nom === 'admin' || auth()->user()->role->nom === 'medecin')
            <li class="{{ Route::is('history.*') ? 'active' : '' }}">
                <a href="{{ route('history.index') }}"><i class="fas fa-chart-line"></i> <span>Historique</span></a>
            </li>
            @endif

            {{-- Administration - Accessible uniquement aux admins --}}
            @if(auth()->user()->role->nom === 'admin')
            <li class="{{ Route::is('admin.*') || Route::is('utilisateurs.*') || Route::is('services.*') ? 'active' : '' }}">
                <a href="{{ route('admin.parametres') }}"><i class="fas fa-users-cog"></i> <span>Administration</span></a>
            </li>
            @endif

            {{-- Section spéciale pour les patients --}}
            @if(auth()->user()->role->nom === 'patient')
            <li class="{{ Route::is('prescriptions.mes') ? 'active' : '' }}">
                <a href="{{ route('prescriptions.mes') }}"><i class="fas fa-prescription"></i> <span>Mes Prescriptions</span></a>
            </li>
            @endif

            {{-- Paramètres - Accessible à tous sauf patients --}}
            @if(auth()->user()->role->nom !== 'patient')
            <li class="{{ Route::is('admin.*') || Route::is('utilisateurs.*') || Route::is('services.*') ? 'active' : '' }}">
                <a href="{{ route('utilisateurs.index') }}"><i class="fas fa-cog"></i> <span>Paramètres</span></a>
            </li>
            @endif
        </ul>
    </div>
    
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}" id="logout-form">
            @csrf
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> <span>Déconnexion</span>
            </a>
        </form>
    </div>
</aside>