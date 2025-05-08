<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'HealthStatut')</title>
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
  <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
  @yield('styles')
</head>
<body>
  @yield('content')
  
  @yield('scripts')
</body>
</html>
