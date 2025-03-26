<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>EzzyIndustri - TPM System</title>

    <!-- Vendor CSS Files -->
    <link href="/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="/assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="/assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


       <!-- Template Main CSS File -->
       <link href="/assets/css/style.css" rel="stylesheet">
    <!-- Custom Theme CSS -->
    <link href="/assets/css/custom/theme.css" rel="stylesheet">
    <!-- Custom Pages CSS -->
    <link href="{{ asset('assets/css/custom/pages/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom/pages/problem-approval.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom/pages/start-production.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom/pages/machine-sop-viewer.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom/pages/checksheet-table.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom/pages/quality-check.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom/pages/oee-detail.css') }}" rel="stylesheet">

</head>

<body>
    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-between">
            <a href="/" class="logo d-flex align-items-center">
                <i class="bi bi-building-gear fs-4 me-2"></i>
                <span class="d-none d-lg-block">EzzyIndustri</span>
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div>

        <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">
        <li class="nav-item user-nav">
            <div class="user-info">
                <span class="user-name">{{ auth()->user()->name }}</span>
                <small class="user-role">{{ ucfirst(auth()->user()->role) }}</small>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="logout-button">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </button>
            </form>
        </li>
    </ul>
</nav>
    </header>

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">
        <ul class="sidebar-nav" id="sidebar-nav">
            @if(auth()->user()->role === 'manajerial')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('manajerial.dashboard') ? '' : 'collapsed' }}" 
                       href="{{ route('manajerial.dashboard') }}" wire:navigate>
                        <i class="bi bi-grid"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('manajerial.production.problems') ? '' : 'collapsed' }}" 
                       href="{{ route('manajerial.production.problems') }}" wire:navigate>
                        <i class="bi bi-exclamation-triangle"></i>
                        <span>Problem Approval</span>
                        <livewire:components.problem-count />
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('manajerial.sop') ? '' : 'collapsed' }}" 
                       href="{{ route('manajerial.sop') }}" wire:navigate>
                        <i class="bi bi-journal-text"></i>
                        <span>Master SOP</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('manajerial.karyawan-report') ? '' : 'collapsed' }}" 
                       href="{{ route('manajerial.karyawan-report') }}" wire:navigate>
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Laporan Karyawan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <!-- If there's a sidebar link to OEE Dashboard, update it -->
                    <a class="nav-link {{ request()->routeIs('manajerial.oee.dashboard') ? '' : 'collapsed' }}" 
                       href="{{ route('manajerial.oee.dashboard') }}" wire:navigate>
                        <i class="bi bi-speedometer2"></i>
                        <span>OEE Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('manajerial.sop.approval') ? '' : 'collapsed' }}" 
                    href="{{ route('manajerial.sop.approval') }}" wire:navigate>
                        <i class="bi bi-clipboard-check"></i>
                        <span>SOP Approval</span>
                        @if($pendingCount ?? 0 > 0)
                            <span class="badge bg-danger rounded-pill ms-2">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('manajerial.machines.*', 'manajerial.shifts.*', 'manajerial.tasks.*') ? '' : 'collapsed' }}" 
                       data-bs-target="#manajemenSubmenu" data-bs-toggle="collapse" href="#">
                        <i class="bi bi-gear"></i>
                        <span>Manajemen</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <ul id="manajemenSubmenu" class="nav-content collapse {{ request()->routeIs('manajerial.machines.*', 'manajerial.shifts.*', 'manajerial.tasks.*', 'manajerial.maintenance-tasks') ? 'show' : '' }}">
                        <li>
                            <a href="{{ route('manajerial.machines') }}" wire:navigate 
                               class="{{ request()->routeIs('manajerial.machines.*') ? 'active' : '' }}">
                                <i class="bi bi-tools"></i><span>Mesin</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('manajerial.products') }}" wire:navigate
                               class="{{ request()->routeIs('manajerial.products') ? 'active' : '' }}">
                                <i class="bi bi-box"></i><span>Produk</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('manajerial.shifts') }}" wire:navigate
                               class="{{ request()->routeIs('manajerial.shifts.*') ? 'active' : '' }}">
                                <i class="bi bi-clock"></i><span>Shift</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('manajerial.maintenance-tasks') }}" wire:navigate
                               class="{{ request()->routeIs('manajerial.maintenance-tasks') ? 'active' : '' }}">
                                <i class="bi bi-list-task"></i><span>Maintenance Tasks</span>
                            </a>
                        </li>
                        </li>
                        <li>
                            <a href="{{ route('manajerial.users') }}" wire:navigate
                               class="{{ request()->routeIs('manajerial.users') ? 'active' : '' }}">
                                <i class="bi bi-people"></i><span>Users</span>
                            </a>
                        </li>
                    </ul>
                </li>
                
            @endif

            @if(auth()->user()->role === 'karyawan')
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('karyawan.dashboard') ? '' : 'collapsed' }}" 
           href="{{ route('karyawan.dashboard') }}" wire:navigate>
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('production.*') ? '' : 'collapsed' }}" 
           href="{{ route('production.start') }}" wire:navigate>
            <i class="bi bi-clipboard-check"></i>
            <span>Produksi</span>
        </a>
    </li>
    @endif

    </ul>
    </aside>

    <main id="main" class="main">
        {{ $slot }}
    </main>

    <footer id="footer" class="footer">
        <div class="copyright">
            &copy; {{ date('Y') }} <strong><span>EzzyIndustri</span></strong>. All Rights Reserved
        </div>
    </footer>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });
    </script>

 

@livewireScripts
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Livewire.on('swal-confirm', (data) => {
            Swal.fire({
                title: data.title || 'Konfirmasi',
                text: data.text || 'Apakah Anda yakin?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: data.confirmText || 'Ya, lanjutkan',
                cancelButtonText: data.cancelText || 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Ganti emit dengan dispatch untuk Livewire v3
                    Livewire.dispatch('showNGForm');
                }
            });
        });
    });
</script>


    

    <script>
        window.addEventListener('show-problem-modal', event => {
            $('#reportProblemModal').modal('show');
        });

        window.addEventListener('hide-problem-modal', event => {
            $('#reportProblemModal').modal('hide');
        });
    </script>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('show-problem-modal', () => {
                const modal = document.getElementById('reportProblemModal');
                const bootstrapModal = new bootstrap.Modal(modal);
                bootstrapModal.show();
            });

            Livewire.on('hide-problem-modal', () => {
                const modal = document.getElementById('reportProblemModal');
                const bootstrapModal = bootstrap.Modal.getInstance(modal);
                if (bootstrapModal) {
                    bootstrapModal.hide();
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- sweet JS Files -->
    <script src="/assets/js/sweet-alert-handlers.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Vendor JS Files -->
    <script src="/assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/vendor/chart.js/chart.umd.js"></script>
    <script src="/assets/vendor/echarts/echarts.min.js"></script>
    <script src="/assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="/assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Template Main JS File -->
    <script src="/assets/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <livewire:karyawan.production.report-problem />
    <script src="/assets/vendor/apexcharts/apexcharts.min.js"></script>
    
    <!-- Di bagian head atau sebelum closing body -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/assets/vendor/chart.js/chart.umd.js"></script>
    <script src="/assets/vendor/echarts/echarts.min.js"></script>
    <!-- Di bagian scripts -->
<script src="{{ asset('assets/js/sweet-alert-handlers.js') }}"></script>

    @stack('scripts')
</body>
</html>