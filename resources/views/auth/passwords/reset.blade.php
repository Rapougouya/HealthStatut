@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Réinitialiser le mot de passe</h2>
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div>
                <label for="email">Adresse e-mail</label>
                <input id="email" type="email" name="email" value="{{ $email }}" required>
            </div>
            <div>
                <label for="password">Nouveau mot de passe</label>
                <input id="password" type="password" name="password" required>
            </div>
            <div>
                <label for="password-confirm">Confirmer le mot de passe</label>
                <input id="password-confirm" type="password" name="password_confirmation" required>
            </div>
            <button type="submit">Réinitialiser le mot de passe</button>
        </form>
    </div>
@endsection