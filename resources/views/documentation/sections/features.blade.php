<section id="features" class="mb-5">
    <h2 class="section-title">Fitur Utama</h2>
    
    <!-- Production Management -->
    <div class="feature-section" id="production">
        <div class="feature-header" onclick="toggleFeature('production-content')">
            <div class="d-flex align-items-center gap-3">
                <div class="feature-icon">
                    <i class="bi bi-gear-fill"></i>
                </div>
                <div>
                    <h4 class="mb-1">Manajemen Produksi</h4>
                    <p class="text-muted mb-0">Monitor dan kelola produksi secara real-time</p>
                </div>
            </div>
            <i class="bi bi-chevron-down"></i>
        </div>
        <div id="production-content" class="feature-content">
            @include('documentation.sections.features.production-management')
        </div>
    </div>

    <!-- Quality Control -->
    <div class="feature-section" id="quality">
        <div class="feature-header" onclick="toggleFeature('quality-content')">
            <div class="d-flex align-items-center gap-3">
                <div class="feature-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h4 class="mb-1">Quality Control</h4>
                    <p class="text-muted mb-0">Sistem pemeriksaan kualitas yang terstandar dan terukur</p>
                </div>
            </div>
            <i class="bi bi-chevron-down"></i>
        </div>
        <div id="quality-content" class="feature-content">
            @include('documentation.sections.features.quality-control')
        </div>
    </div>

    <!-- Maintenance System -->
    <div class="feature-section" id="maintenance">
        <div class="feature-header" onclick="toggleFeature('maintenance-content')">
            <div class="d-flex align-items-center gap-3">
                <div class="feature-icon">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <h4 class="mb-1">Sistem Maintenance</h4>
                    <p class="text-muted mb-0">Manajemen perawatan mesin yang terstruktur dan terukur</p>
                </div>
            </div>
            <i class="bi bi-chevron-down"></i>
        </div>
        <div id="maintenance-content" class="feature-content">
            @include('documentation.sections.features.maintenance')
        </div>
    </div>

    <!-- SOP Management -->
    <div class="feature-section" id="sop">
        <div class="feature-header" onclick="toggleFeature('sop-content')">
            <div class="d-flex align-items-center gap-3">
                <div class="feature-icon">
                    <i class="bi bi-journal-text"></i>
                </div>
                <div>
                    <h4 class="mb-1">Manajemen SOP</h4>
                    <p class="text-muted mb-0">Sistem manajemen SOP yang terstruktur dan terintegrasi</p>
                </div>
            </div>
            <i class="bi bi-chevron-down"></i>
        </div>
        <div id="sop-content" class="feature-content">
            @include('documentation.sections.features.sop-management')
        </div>
    </div>

    <!-- OEE Monitoring -->
    <div class="feature-section" id="oee">
        <div class="feature-header" onclick="toggleFeature('oee-content')">
            <div class="d-flex align-items-center gap-3">
                <div class="feature-icon">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div>
                    <h4 class="mb-1">OEE Monitoring</h4>
                    <p class="text-muted mb-0">Pemantauan efektivitas peralatan secara real-time</p>
                </div>
            </div>
            <i class="bi bi-chevron-down"></i>
        </div>
        <div id="oee-content" class="feature-content">
            @include('documentation.sections.features.oee-monitoring')
        </div>
    </div>

    <!-- Downtime Management -->
    <div class="feature-section" id="downtime">
        <div class="feature-header" onclick="toggleFeature('downtime-content')">
            <div class="d-flex align-items-center gap-3">
                <div class="feature-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <h4 class="mb-1">Manajemen Downtime</h4>
                    <p class="text-muted mb-0">Pelacakan dan analisis komprehensif untuk gangguan produksi</p>
                </div>
            </div>
            <i class="bi bi-chevron-down"></i>
        </div>
        <div id="downtime-content" class="feature-content">
            @include('documentation.sections.features.downtime-management')
        </div>
    </div>
</section>