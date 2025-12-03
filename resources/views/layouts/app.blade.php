<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Kandidat') - {{ config('app.company_name', 'Portal Rekrutmen') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
    body {
        background-color: #f4f7f9;
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif;
    }

    .card {
        border: 1px solid #e9ecef;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border-radius: 0.75rem;
    }

    /* === KUSTOMISASI TEMA HIJAU UNTUK PORTAL KANDIDAT (LEBIH SPESIFIK) === */
    :root {
        --primary-green: #006400;
        --primary-green-darker: #004d00;
    }

    /* 1. Kustomisasi Tombol Solid (btn-primary) */
    .btn-primary {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
    }

    .bg-primary {
        background-color: var(--primary-green) !important;
        border-color: var(--primary-green);
    }

    .btn-primary:hover,
    .btn-primary:focus,
    .btn-primary:active {
        background-color: var(--primary-green-darker) !important;
        border-color: var(--primary-green-darker) !important;
        box-shadow: 0 0 0 0.25rem rgba(0, 100, 0, 0.5) !important;
    }

    /* 2. Kustomisasi Tombol Outline (btn-outline-primary) */
    .btn-outline-primary {
        color: var(--primary-green);
        border-color: var(--primary-green);
    }

    .btn-outline-primary:hover,
    .btn-outline-primary:focus,
    .btn-outline-primary:active {
        color: #ffffff !important;
        background-color: var(--primary-green) !important;
        border-color: var(--primary-green) !important;
        box-shadow: 0 0 0 0.25rem rgba(0, 100, 0, 0.5) !important;
    }

    /* 3. Kustomisasi Elemen Lainnya */
    .text-primary {
        color: var(--primary-green) !important;
    }

    .badge.bg-primary {
        background-color: var(--primary-green) !important;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #008000;
        box-shadow: 0 0 0 0.25rem rgba(0, 100, 0, 0.25);
    }

    .form-check-input:checked {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
    }

    /* === Akhir dari Kustomisasi === */
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background-color: #006400;">
        {{-- ... (Isi navbar tidak berubah) ... --}}
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Portal Kandidat</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div id="navbarNav" class="navbar-collapse collapse">
                <ul class="navbar-nav ms-auto">
                    @auth
                    @if (Auth::user()->role === 'candidate')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('candidate.profile') ? 'active' : '' }}"
                            href="{{ route('candidate.profile') }}">Profil Saya</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('job_vacancies.index') ? 'active' : '' }}"
                            href="{{ route('job_vacancies.index') }}">Lowongan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                    @endif
                    @endauth
                    @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-5">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>