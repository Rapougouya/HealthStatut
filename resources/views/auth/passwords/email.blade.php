@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Mot de passe oublié</h2>
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div>
                <label for="email">Adresse e-mail</label>
                <input id="email" type="email" name="email" required autofocus>
            </div>
            <button type="submit">Envoyer le lien de réinitialisation</button>
        </form>
    </div>
@endsection