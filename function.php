<?php
// ذخیره تاریخچه قیمت محصول هنگام ذخیره
function beban_track_price_changes($post_id, $post) {
    if ($post->post_type !== 'product' || wp_is_post_revision($post_id)) {
        return;
    }

    $product = wc_get_product($post_id);
    if (!$product || !$product->get_price()) {
        return;
    }

    $current_price = $product->get_price();
    $price_history = get_post_meta($post_id, '_price_history', true);
    $price_history = is_array($price_history) ? $price_history : [];

    if (!empty($price_history) && end($price_history)['price'] == $current_price) {
        return;
    }

    $price_history[] = [
        'date' => current_time('Y-m-d H:i:s'),
        'price' => floatval($current_price)
    ];

    if (count($price_history) > 30) {
        $price_history = array_slice($price_history, -30);
    }

    update_post_meta($post_id, '_price_history', $price_history);
}
add_action('save_post', 'beban_track_price_changes', 10, 2);


// شورت‌کد نمودار قیمت با فیلتر نمایش بر اساس دستگاه
function beban_price_chart_shortcode($atts) {
    $atts = shortcode_atts([
        'device' => 'all', // mobile, desktop, all
    ], $atts);

    // فیلتر دستگاه
    if ($atts['device'] === 'mobile' && !wp_is_mobile()) {
        return '';
    }
    if ($atts['device'] === 'desktop' && wp_is_mobile()) {
        return '';
    }

    if (!is_product()) {
        return '';
    }

    global $product;
    if (!$product) {
        return '';
    }

    $price_history = get_post_meta($product->get_id(), '_price_history', true);
    $price_history = is_array($price_history) ? $price_history : [];

    if (empty($price_history) || count($price_history) < 2) {
        return '<p>نمودار تغییر قیمت در دسترس نیست.</p>';
    }

    $data_points = [];
    $labels = [];
    foreach ($price_history as $index => $entry) {
        $data_points[] = [
            'x' => $index,
            'y' => floatval($entry['price'])
        ];
        $labels[] = $entry['date'];
    }

    wp_enqueue_script('chart-js-fallback', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js', [], '4.4.4', true);

    ob_start();
    ?>
    <div id="chartContainer">
        <div id="chartLoader" class="chart-loader"></div>
        <canvas id="priceChart" width="400" height="200" style="display: none;"></canvas>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var canvas = document.getElementById('priceChart');
            if (!canvas || !canvas.getContext('2d')) {
                return;
            }

            var chartData = <?php echo wp_json_encode($data_points); ?>;
            if (!chartData || chartData.length < 2) {
                return;
            }

            var ctx = canvas.getContext('2d');
            var chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: [{
                        label: 'تغییرات قیمت (تومان)',
                        data: chartData,
                        borderColor: '#EF394F',
                        borderWidth: 2,
                        radius: 0,
                        tension: 0.4
                    }]
                },
                options: {
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutQuart'
                    },
                    interaction: {
                        intersect: false
                    },
                    plugins: {
                        legend: false,
                        tooltip: {
                            titleFont: { family: 'Vazirmatn', size: 14 },
                            bodyFont: { family: 'Vazirmatn', size: 14 },
                            callbacks: {
                                title: function () { return ''; },
                                label: function (context) {
                                    var price = context.parsed.y;
                                    var date = <?php echo wp_json_encode($labels); ?>[context.dataIndex];
                                    return 'قیمت: ' + price.toLocaleString('fa-IR') + ' تومان (' + date + ')';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            type: 'linear',
                            display: false
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'قیمت (تومان)',
                                font: { family: 'Vazirmatn', size: 14 }
                            },
                            beginAtZero: false,
                            ticks: {
                                font: { family: 'Vazirmatn', size: 14 },
                                callback: function (value) {
                                    return value.toLocaleString('fa-IR');
                                }
                            }
                        }
                    }
                }
            });

            setTimeout(function () {
                document.getElementById('chartLoader').style.display = 'none';
                document.getElementById('priceChart').style.display = 'block';
            }, 1000);
        });
    </script>

    <style>
        #chartContainer {
            position: relative;
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
        }
        #priceChart {
            width: 100% !important;
            height: auto !important;
            max-width: 100%;
        }
        .chart-loader {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 200px;
            border-radius: 8px;
            background: #f9f9f9;
        }
        .chart-loader::after {
            content: "";
            width: 40px;
            height: 40px;
            border: 5px solid #b12ba4;
            border-top: 5px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('price_chart', 'beban_price_chart_shortcode');
