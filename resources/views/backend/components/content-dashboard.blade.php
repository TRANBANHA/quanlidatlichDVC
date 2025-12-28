<div class="page-inner">
    <h3 class="fw-bold mb-3">Chart.js</h3>
    <div class="page-category">
        {{-- Simple yet flexible JavaScript charting for designers & developers. Please check out their
        <a href="http://www.chartjs.org/" target="_blank">full documentation</a>. --}}
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="filter-container mb-3">
                <label for="registrationType">Chọn loại đăng ký: </label>
                <select id="registrationType" class="form-select" onchange="updateChart()">
                    <option value="birth_registration">Đăng ký khai sinh</option>
                    <option value="temp_residence_registration">Tạm trú</option>
                    <option value="absence_registration">Tạm vắng</option>
                    <option value="death_registration">Khai tử</option>
                    <option value="citizens">Công dân</option>
                </select>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">Bar Chart</div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    <script>
        let myBarChart;

        function createChart(dataPoints) {
            const barChart = document.getElementById("barChart").getContext("2d");
            myBarChart = new Chart(barChart, {
                type: "bar",
                data: {
                    labels: [
                        "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                    ],
                    datasets: [{
                        label: "Sales",
                        backgroundColor: "rgb(23, 125, 255)",
                        borderColor: "rgb(23, 125, 255)",
                        data: dataPoints,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                        },
                    },
                },
            });
        }
        const homeCountUrl = "{{ route('home_count') }}";

        function updateChart() {
            const selectedType = $("#registrationType").val(); // Using jQuery to get the value

            $.ajax({
                url: homeCountUrl,
                method: 'GET',
                data: {
                    registrationType: selectedType
                },
                dataType: 'json',
                success: function(data) {
                    // Destroy the previous chart instance if it exists
                    if (myBarChart) {
                        myBarChart.destroy();
                    }
                    // Create a new chart with the fetched data
                    createChart(data);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching data:', textStatus, errorThrown);
                }
            });
        }

        // Initialize the chart with the default selection
        $(document).ready(function() {
            updateChart(); // Initialize the chart with the default value
        });
    </script>
@endsection
