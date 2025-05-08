<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Prescriptions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
   
</head>
<body>

<header>
    <h2><i class="fas fa-file-medical-alt"></i> Mes Prescriptions</h2>
</header>

<div class="container">
    @if($prescriptions->isEmpty())
        <p>Aucune prescription enregistrée pour vous pour le moment.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th><i class="fas fa-capsules"></i> Médicament</th>
                    <th><i class="fas fa-notes-medical"></i> Posologie</th>
                    <th><i class="fas fa-calendar-day"></i> Début</th>
                    <th><i class="fas fa-calendar-check"></i> Fin</th>
                    <th><i class="fas fa-user-md"></i> Médecin</th>
                    <th><i class="fas fa-clipboard-check"></i> Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prescriptions as $prescription)
                    <tr>
                        <td><span class="icon-pill">{{ $prescription->medicament }}</span></td>
                        <td>{{ $prescription->posologie }}</td>
                        <td class="date">{{ \Carbon\Carbon::parse($prescription->date_debut)->format('d/m/Y') }}</td>
                        <td class="date">{{ \Carbon\Carbon::parse($prescription->date_fin)->format('d/m/Y') }}</td>
                        <td>{{ $prescription->medecin }}</td>
                        <td>
                            @if($prescription->statut === 'valide')
                                <span class="badge valide">Valide</span>
                            @elseif($prescription->statut === 'expirée')
                                <span class="badge expiree">Expirée</span>
                            @else
                                <span class="badge attente">En attente</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

</body>
</html>
