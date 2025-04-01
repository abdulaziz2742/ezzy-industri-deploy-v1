<section id="simulation" class="mb-5">
    <h2 class="section-title">Simulasi Efisiensi</h2>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="mb-4">Kalkulasi OEE</h4>
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Total Production Time (jam)</label>
                            <input type="number" class="form-control" id="productionTime">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Planned Downtime (jam)</label>
                            <input type="number" class="form-control" id="plannedDowntime">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unplanned Downtime (jam)</label>
                            <input type="number" class="form-control" id="unplannedDowntime">
                        </div>
                        <button type="submit" class="btn btn-primary">Hitung OEE</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <h4 class="mb-4">Hasil Kalkulasi</h4>
                    <div class="card bg-light">
                        <div class="card-body">
                            <p>Availability Rate: <span id="availabilityRate">-</span></p>
                            <p>Performance Rate: <span id="performanceRate">-</span></p>
                            <p>Quality Rate: <span id="qualityRate">-</span></p>
                            <h5>Overall OEE: <span id="overallOEE">-</span></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>