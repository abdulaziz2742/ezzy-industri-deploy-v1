<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EzzyIndustri Documentation</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #f8f9fa;
            border-right: 1px solid #dee2e6;
            padding: 20px;
            overflow-y: auto;
        }

        .main-content {
            margin-left: 280px;
            padding: 40px;
            max-width: 1200px;
        }

        .nav-link {
            color: #495057;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
        }

        .nav-link:hover {
            background: #e9ecef;
            color: #1e88e5;
        }

        #featuresDropdown .nav-link {
            font-size: 0.95em;
            padding: 6px 16px;
        }

        #featuresDropdown .nav-link:hover {
            background: #e9ecef;
        }

        .collapse {
            transition: all 0.3s ease;
        }

        .feature-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .benefit-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .hero-section {
            background: linear-gradient(135deg, #0a2463 0%, #1e88e5 100%);
            color: white;
            padding: 60px 0;
            border-radius: 15px;
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 30px;
            color: #0a2463;
        }

        .feature-section {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .feature-header {
            padding: 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            transition: all 0.3s ease;
        }

        .feature-header:hover {
            background: #f8f9fa;
        }

        .feature-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease;
            background: #fff;
        }

        .feature-content.active {
            max-height: 2000px;
            padding: 20px;
            border-top: 1px solid #dee2e6;
        }

        .bi-chevron-down {
            transition: transform 0.3s ease;
        }

        .feature-content.active + .feature-header .bi-chevron-down {
            transform: rotate(180deg);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4 class="mb-4">EzzyIndustri Docs</h4>
        <nav class="nav flex-column">
            <a class="nav-link" href="#home">
                <i class="bi bi-house-door"></i> Landing Page
            </a>
            
            <!-- Features Dropdown -->
            <div class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#featuresDropdown">
                    <span><i class="bi bi-stars"></i> Fitur Utama</span>
                    <i class="bi bi-chevron-down"></i>
                </a>
                <div class="collapse" id="featuresDropdown">
                    <nav class="nav flex-column ms-3">
                        <a class="nav-link py-2" href="#production">
                            <i class="bi bi-gear"></i> Manajemen Produksi
                        </a>
                        <a class="nav-link py-2" href="#quality">
                            <i class="bi bi-shield-check"></i> Quality Control
                        </a>
                        <a class="nav-link py-2" href="#maintenance">
                            <i class="bi bi-tools"></i> Sistem Maintenance
                        </a>
                        <a class="nav-link py-2" href="#sop">
                            <i class="bi bi-journal-text"></i> Manajemen SOP
                        </a>
                        <a class="nav-link py-2" href="#oee">
                            <i class="bi bi-graph-up"></i> OEE Monitoring
                        </a>
                        <a class="nav-link py-2" href="#downtime">
                            <i class="bi bi-clock-history"></i> Downtime Management
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Other nav links -->
            <a class="nav-link" href="#benefits">
                <i class="bi bi-lightbulb"></i> Keuntungan
            </a>
            <a class="nav-link" href="#simulation">
                <i class="bi bi-calculator"></i> Simulasi Efisiensi
            </a>
            <a class="nav-link" href="#implementation">
                <i class="bi bi-rocket-takeoff"></i> Cara Implementasi
            </a>
            <a class="nav-link" href="#tech-stack">
                <i class="bi bi-code-square"></i> Tech Stack
            </a>
            <a class="nav-link" href="#faq">
                <i class="bi bi-question-circle"></i> FAQ
            </a>
            <a class="nav-link" href="#contact">
                <i class="bi bi-envelope"></i> Kontak
            </a>
            <!-- Add new nav link -->
            <a class="nav-link" href="#about-dev">
                <i class="bi bi-person"></i> About Developer
            </a>
        </nav>
    </div>

    <div class="main-content">
        @include('documentation.sections.hero')
        @include('documentation.sections.about-dev')
        @include('documentation.sections.features')
        @include('documentation.sections.benefits')
        @include('documentation.sections.simulation')
        @include('documentation.sections.implementation')
        @include('documentation.sections.tech-stack')
        @include('documentation.sections.faq')
        @include('documentation.sections.contact')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function toggleFeature(id) {
        const content = document.getElementById(id);
        content.classList.toggle('active');
        
        // Update chevron icon
        const header = content.previousElementSibling;
        const chevron = header.querySelector('.bi-chevron-down');
        chevron.style.transform = content.classList.contains('active') ? 'rotate(180deg)' : 'rotate(0)';
    }

    // Handle navigation from sidebar
    document.querySelectorAll('#featuresDropdown .nav-link').forEach(link => {
        link.addEventListener('click', (e) => {
            const targetId = e.currentTarget.getAttribute('href').substring(1);
            const contentId = targetId + '-content';
            setTimeout(() => {
                toggleFeature(contentId);
                document.getElementById(targetId).scrollIntoView({ behavior: 'smooth' });
            }, 100);
        });
    });
    </script>
</body>
</html>