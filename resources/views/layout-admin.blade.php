<!DOCTYPE html>
<html
  lang="id"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('/assets/') }}"
  data-template="vertical-menu-template-free"
>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $title ?? 'Dashboard' }} | E-Sinau</title>

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="{{ asset('/assets/img/favicon/favicon.ico') }}" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
    rel="stylesheet"
  />

  <!-- Icons -->
  <link rel="stylesheet" href="{{ asset('/assets/vendor/fonts/boxicons.css') }}" />

  <!-- Core CSS -->
  <link rel="stylesheet" href="{{ asset('/assets/vendor/css/core.css') }}" />
  <link rel="stylesheet" href="{{ asset('/assets/vendor/css/theme-default.css') }}" />
  <link rel="stylesheet" href="{{ asset('/assets/css/demo.css') }}" />

  <!-- Vendor -->
  <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

  <!-- Datatables CSS -->
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

  <!-- Helpers -->
  <script src="{{ asset('/assets/vendor/js/helpers.js') }}"></script>
  <script src="{{ asset('/assets/js/config.js') }}"></script>

  <style>
    /* CSS Cover Course */
    .mapel-cover {
        width: 130px;
        height: 130px;
        border-radius: 0.5rem;
        overflow: hidden;
        background: #f1f3f5;
    }

    .mapel-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
  </style>

</head>

<body>
<div class="layout-wrapper layout-content-navbar">
<div class="layout-container">

<!-- SIDEBAR -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

  <div class="app-brand demo">
    <a href="javascript:void(0);" class="app-brand-link">
      <span class="app-brand-text demo menu-text fw-bolder ms-2">
        E-Sinau
      </span>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">

    <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <a href="{{ route('admin.dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div>Dashboard</div>
      </a>
    </li>

    <li class="menu-item {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
      <a href="{{ route('admin.users.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-user"></i>
        <div>Manajemen User</div>
      </a>
    </li>

    <li class="menu-item {{ request()->routeIs('admin.classes.index', 'admin.classes.courses.index', 'admin.classes.plot', 'admin.courses.tasks.index', 'admin.courses.materials.index', 'admin.courses.materials.create', 'admin.courses.materials.edit', 'admin.courses.materials.preview', 'admin.assignment.show', 'admin.assignment.progress', 'admin.assignment.review', 'admin.assignment.grade') ? 'active' : '' }}">
      <a href="{{ route('admin.classes.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-chalkboard"></i>
        <div>Kelas</div>
      </a>
    </li>
  </ul>
</aside>
<!-- /SIDEBAR -->

<!-- PAGE -->
<div class="layout-page">

<!-- NAVBAR -->
<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached bg-navbar-theme">
  <div class="navbar-nav-right d-flex align-items-center w-100">

    <ul class="navbar-nav flex-row align-items-center ms-auto">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            <img src="{{ asset('/assets/img/avatars/default-avatar.png') }}" class="rounded-circle">
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li class="px-3 py-2">
            <strong>{{ Auth::user()->name ?? 'Administrator' }}</strong><br>
            <small class="text-muted">{{ Auth::user()->role ?? 'Admin' }}</small>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <a class="dropdown-item" href="{{ route('logout') }}">
              <i class="bx bx-log-out me-2"></i> Logout
            </a>
          </li>
        </ul>
      </li>
    </ul>

  </div>
</nav>
<!-- /NAVBAR -->

<!-- CONTENT -->
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    @yield('content')
  </div>
</div>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- JS -->
<script src="{{ asset('/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('/assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('/assets/vendor/js/menu.js') }}"></script>
<script src="{{ asset('/assets/js/main.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
@if(session('success'))
    <script>
        Swal.fire({
            title: "Berhasil",
            text: "{{ session('success') }}",
            icon: "success"
        });
    </script>
@elseif(session('error'))
    <script>
        Swal.fire({
            title: "Gagal!",
            text: "{{ session('error') }}",
            icon: "error"
        });
    </script>
@endif
@stack('scripts')
</body>
</html>
