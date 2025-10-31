<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard HRD') - Portal Rekrutmen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
    /* === PERUBAHAN GAYA VISUAL (TEMA HIJAU) === */
    :root {
        /* [UBAH] Warna utama diubah menjadi hijau */
        --primary-green: #006400;
        --secondary-text: #6c757d;
        --body-bg: #f8f9fa;
        /* [UBAH] Override warna primer Bootstrap agar konsisten di semua komponen */
        --bs-primary: var(--primary-green);
        --bs-primary-rgb: 0, 100, 0;
    }

    body {
        display: flex;
        min-height: 100vh;
        background-color: var(--body-bg);
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
    }

    #sidebar {
        width: 250px;
        min-height: 100vh;
        background-color: #ffffff;
        color: #333;
        position: fixed;
        left: 0;
        top: 0;
        bottom: 0;
        z-index: 100;
        border-right: 1px solid #e9ecef;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
    }

    #sidebar .nav-link {
        color: var(--secondary-text);
        padding: 0.85rem 1.5rem;
        display: flex;
        align-items: center;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
    }

    .btn-primary {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
    }

    .btn-primary:hover {
        background-color: #004d00;
        /* Warna hijau sedikit lebih gelap untuk hover */
        border-color: #004500;
    }

    .btn-primary:active,
    .btn-primary:focus,
    .btn-primary.active {
        background-color: #004d00 !important;
        border-color: #004500 !important;
        box-shadow: 0 0 0 0.25rem rgba(0, 100, 0, 0.5) !important;
        /* Glow hijau saat aktif/fokus */
    }

    /* [UBAH] Warna hover disesuaikan dengan warna utama baru */
    #sidebar .nav-link:hover {
        background-color: rgba(0, 100, 0, 0.08);
        /* Hijau dengan opacity rendah */
        color: var(--primary-green);
    }

    #sidebar .nav-link.active {
        background-color: var(--primary-green);
        color: #fff;
        border-radius: 8px;
        margin: 0.25rem 1rem;
        padding: 0.85rem 1rem;
    }

    #sidebar .nav-link .bi {
        margin-right: 0.85rem;
        font-size: 1.1rem;
    }

    #sidebar .sidebar-header {
        padding: 1.5rem;
    }

    /* [UBAH] Warna teks di header disesuaikan */
    #sidebar .sidebar-header h4 {
        color: var(--primary-green);
    }

    #sidebar .sidebar-footer {
        padding: 1.5rem;
        border-top: 1px solid #e9ecef;
    }

    #sidebar .sidebar-footer a {
        color: var(--secondary-text);
        text-decoration: none;
    }

    #sidebar .sidebar-footer a:hover {
        color: var(--primary-green);
    }

    .main-content {
        margin-left: 250px;
        width: calc(100% - 250px);
        padding: 2.5rem;
    }

    .card {
        border: 1px solid #e9ecef;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border-radius: 0.75rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #008000;
        /* Sedikit lebih terang dari warna utama */
        box-shadow: 0 0 0 0.25rem rgba(0, 100, 0, 0.25);
        /* Glow berwarna hijau */
    }

    .form-check-input:checked {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
    }

    .table-green thead {
        background-color: var(--primary-green);
        color: #ffffff;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }

    .align-middle {
        vertical-align: middle;
    }
    </style>
</head>

<body>

    <nav id="sidebar" class="d-flex flex-column p-0">
        <div class="sidebar-header">
            <h4 class="fw-bold"><i class="bi bi-person-workspace"></i>
                <span class="text-uppercase">{{ Auth::user()->role }}</span> Portal
            </h4>
        </div>
        <ul class="nav flex-column flex-grow-1">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    href="{{ route('dashboard') }}">
                    <i class="bi bi-grid-fill"></i> Dashboard
                </a>
            </li>
            @switch(Auth::user()->role)
            @case('hrd')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('hrd.job_vacancies.*') ? 'active' : '' }}"
                    href="{{ route('hrd.job_vacancies.index') }}">
                    <i class="bi bi-briefcase-fill"></i> Lowongan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('hrd.applications.*') ? 'active' : '' }}"
                    href="{{ route('hrd.applications.index') }}">
                    <i class="bi bi-people-fill"></i> Pelamar
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('hrd.psychotest_questions.*') ? 'active' : '' }}"
                    href="{{ route('hrd.psychotest_questions.index') }}">
                    <i class="bi bi-patch-question-fill"></i> Soal Psikotes
                </a>
            </li>
            @break

            @case('manager')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('manager.applications.*') ? 'active' : '' }}"
                    href="{{ route('manager.applications.index') }}">
                    <i class="bi bi-file-bar-graph-fill"></i>Laporan
                </a>
            </li>
            @break
            @endswitch
        </ul>
        <div class="sidebar-footer">
            <div class="fw-bold">{{ Auth::user()->name }}</div>
            <small class="text-muted">{{ Auth::user()->role }}</small>
            <hr>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Logout <i class="bi bi-box-arrow-right"></i>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </nav>

    <div class="main-content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>