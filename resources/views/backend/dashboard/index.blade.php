@extends('backend.components.layout')
@section('title', 'Thống kê & Báo cáo')

@section('content')
<!-- Main content -->
@endsection

@push('styles')
<style>
    .content-wrapper {
        background-color: #f8f9fc;
        min-height: 100vh;
    }

    .content {
        padding-top: 1rem;
    }

    .container-fluid {
        max-width: 1800px;
        margin: 0 auto;
        padding-top: 20px;
    }

    .card {
        transition: all 0.3s ease;
        border-radius: 12px;
        border: none;
        height: 100%;
        background-color: #fff;
        margin-bottom: 0;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1.5rem rgba(0, 0, 0, .08) !important;
    }

    .icon-bg {
        width: 52px;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background-color: rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }

    .card:hover .icon-bg {
        transform: scale(1.1) rotate(10deg);
        background-color: rgba(255, 255, 255, 0.3);
    }

    .bg-gradient-primary {
        background: linear-gradient(45deg, #4e73df, #224abe);
    }

    .bg-gradient-success {
        background: linear-gradient(45deg, #1cc88a, #13855c);
    }

    .bg-gradient-warning {
        background: linear-gradient(45deg, #f6c23e, #dda20a);
    }

    .bg-gradient-danger {
        background: linear-gradient(45deg, #e74a3b, #be2617);
    }

    .chart-wrapper {
        position: relative;
        min-height: 300px;
        padding: 1rem 0;
    }

    .btn-group {
        background-color: rgba(78, 115, 223, 0.1);
        padding: 4px;
        border-radius: 30px;
    }

    .btn-group .btn {
        border-radius: 20px;
        padding: 0.4rem 1.2rem;
        font-weight: 500;
        border: none;
        transition: all 0.2s ease;
    }

    .btn-group .btn:hover {
        background-color: rgba(78, 115, 223, 0.15);
    }

    .btn-group .btn.active {
        background-color: #4e73df;
        color: white;
        box-shadow: 0 2px 4px rgba(78, 115, 223, 0.2);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script>
    Chart.register(ChartDataLabels);

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    padding: 20,
                    font: {
                        size: 12
                    }
                }
            }
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        // Thêm active class cho nút mặc định
        document.querySelector('.btn-group .btn:first-child').classList.add('active');

        // Biểu đồ theo thời gian
        new Chart(document.getElementById('timeChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($hoSoTheoThang->map(function ($item) {
                    return "Tháng {$item->month}/{$item->year}";
                })) !!},
                datasets: [{
                    label: 'Số lượng hồ sơ',
                    data: {{ json_encode($hoSoTheoThang->pluck('total')) }},
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#4e73df',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#4e73df',
                    pointBorderWidth: 2
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    datalabels: {
                        align: 'end',
                        anchor: 'end',
                        offset: 5,
                        color: '#4e73df',
                        font: {
                            weight: 'bold'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false,
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Biểu đồ theo phường
        new Chart(document.getElementById('wardChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($hoSoTheoPhuong->pluck('ten_don_vi')) !!},
                datasets: [{
                    data: {{ json_encode($hoSoTheoPhuong->pluck('total')) }},
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: {
                ...commonOptions,
                cutout: '70%',
                plugins: {
                    ...commonOptions.plugins,
                    datalabels: {
                        formatter: (value, ctx) => {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value * 100) / total);
                            return percentage + '%';
                        },
                        color: '#fff',
                        font: {
                            weight: 'bold',
                            size: 11
                        }
                    }
                }
            }
        });

        // Biểu đồ theo dịch vụ
        new Chart(document.getElementById('serviceChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($hoSoTheoDichVu->pluck('ten_dich_vu')) !!},
                datasets: [{
                    label: 'Số lượng hồ sơ',
                    data: {{ json_encode($hoSoTheoDichVu->pluck('total')) }},
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    borderRadius: 8,
                    borderSkipped: false,
                    maxBarThickness: 35
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        offset: 5,
                        color: '#4e73df',
                        font: {
                            weight: 'bold'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false,
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Biểu đồ đánh giá
        new Chart(document.getElementById('ratingChart'), {
            type: 'bar',
            data: {
                labels: ['1 sao', '2 sao', '3 sao', '4 sao', '5 sao'],
                datasets: [{
                    label: 'Số lượng đánh giá',
                    data: [
                        {{ $ratingCounts[1] ?? 0 }},
                        {{ $ratingCounts[2] ?? 0 }},
                        {{ $ratingCounts[3] ?? 0 }},
                        {{ $ratingCounts[4] ?? 0 }},
                        {{ $ratingCounts[5] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#e74a3b', // 1 sao - đỏ
                        '#f6c23e', // 2 sao - vàng
                        '#36b9cc', // 3 sao - xanh dương nhạt 
                        '#1cc88a', // 4 sao - xanh lá
                        '#4e73df' // 5 sao - xanh dương đậm
                    ],
                    borderRadius: 8,
                    borderSkipped: false,
                    maxBarThickness: 35
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        offset: 5,
                        formatter: (value) => value > 0 ? value : '',
                        color: '#333',
                        font: {
                            weight: 'bold'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false,
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endpush