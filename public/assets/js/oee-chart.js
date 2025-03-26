(function() {
    function initChart(chartData) {
        const chartElement = document.getElementById('oeeChart');
        if (!chartElement || !chartData) return null;

        const options = {
            series: [
                { name: 'Availability', data: chartData.availability || [] },
                { name: 'Performance', data: chartData.performance || [] },
                { name: 'Quality', data: chartData.quality || [] },
                { name: 'OEE Score', data: chartData.oee || [] }
            ],
            chart: {
                height: 350,
                type: 'line',
                zoom: { enabled: false },
                animations: { enabled: false }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'straight', width: 2 },
            grid: { row: { colors: ['#f3f3f3', 'transparent'], opacity: 0.5 } },
            colors: ['#2eca6a', '#4154f1', '#ff771d', '#7928ca'],
            xaxis: {
                categories: chartData.labels || [],
                labels: { style: { fontSize: '12px' } }
            },
            yaxis: {
                max: 100,
                min: 0,
                labels: { formatter: (val) => val.toFixed(1) + "%" }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                floating: true,
                offsetY: -25,
                offsetX: -5
            }
        };

        const chart = new ApexCharts(chartElement, options);
        chart.render();
        return chart;
    }

    function updateChart(chart, newData) {
        if (!chart || !newData) return;

        chart.updateOptions({
            xaxis: { categories: newData.labels || [] }
        });
        chart.updateSeries([
            { name: 'Availability', data: newData.availability || [] },
            { name: 'Performance', data: newData.performance || [] },
            { name: 'Quality', data: newData.quality || [] },
            { name: 'OEE Score', data: newData.oee || [] }
        ]);
    }

    // Initialize chart when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        let chart = null;
        const initialData = window.initialChartData;

        if (initialData) {
            chart = initChart(initialData);
        }

        // Listen for Livewire updates
        Livewire.on('chartDataUpdated', function(newData) {
            if (!chart) {
                chart = initChart(newData[0]);
            } else {
                updateChart(chart, newData[0]);
            }
        });
    });
})();