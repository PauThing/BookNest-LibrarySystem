<?php
//set the session timeout to 8 hours (8 hours * 60 minutes * 60 seconds)
ini_set('session.gc_maxlifetime', 8 * 60 * 60);
session_start();

include('../clients/navbar.php');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.css" integrity="sha512-Z0kTB03S7BU+JFU0nw9mjSBcRnZm2Bvm0tzOX9/OuOuz01XQfOpa0w/N9u6Jf2f1OAdegdIPWZ9nIZZ+keEvBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="../clients/styles/index.css">

    <title>Dashboard</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="big-container">
        <div class="monthr-container">
            <form class="monthr-form" id="monthr-form">
                <div class="wrap">
                    <div class="selectInput">
                        <label><i class="fa fa-filter"></i> Filter: </label>
                        <select id="yearR">
                            <option value="<?php echo date("Y"); ?>"><?php echo date("Y"); ?></option>
                            <?php
                            $currentYear = date("Y");
                            for ($i = $currentYear - 1; $i >= $currentYear - 5; $i--) {
                                echo "<option value=\"$i\">$i</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <canvas id="resChart" style="width:100%; max-width:60em; max-height:60em;"></canvas>
                </div>

                <br />

                <div class="wrap">
                    <div class="selectInput">
                        <label><i class="fa fa-filter"></i> Filter: </label>
                        <select id="yearC">
                            <option value="<?php echo date("Y"); ?>"><?php echo date("Y"); ?></option>
                            <?php
                            $currentYear = date("Y");
                            for ($i = $currentYear - 1; $i >= $currentYear - 5; $i--) {
                                echo "<option value=\"$i\">$i</option>";
                            }
                            ?>
                        </select>
                        <select id="monthC">
                            <?php
                            for ($i = 1; $i <= 12; $i++) {
                                echo "<option value=\"$i\">" . date("F", mktime(0, 0, 0, $i, 1)) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <canvas id="catChart" style="width:100%; max-width:26em; max-height:50em;"></canvas>
                </div>

                <br />

                <div class="wrap">
                    <div class="selectInput">
                        <label><i class="fa fa-filter"></i> Filter: </label>
                        <select id="yearB">
                            <option value="<?php echo date("Y"); ?>"><?php echo date("Y"); ?></option>
                            <?php
                            $currentYear = date("Y");
                            for ($i = $currentYear - 1; $i >= $currentYear - 5; $i--) {
                                echo "<option value=\"$i\">$i</option>";
                            }
                            ?>
                        </select>
                        <select id="monthB">
                            <?php
                            for ($i = 1; $i <= 12; $i++) {
                                echo "<option value=\"$i\">" . date("F", mktime(0, 0, 0, $i, 1)) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <canvas id="bookChart" style="width:100%; max-width:60em; max-height:60em;"></canvas>
                </div>
            </form>
        </div>
    </div>

    <span>
        <?php
        if (isset($_SESSION['message'])) {
            echo "<script>alert('" . $_SESSION['message'] . "');</script>";
        }

        unset($_SESSION['message']);
        ?>
    </span>
</body>

<script>
    //data visualization
    const yearR = document.getElementById("yearR");
    const yearC = document.getElementById("yearC");
    const yearB = document.getElementById("yearB");
    const monthC = document.getElementById("monthC");
    const monthB = document.getElementById("monthB");

    //get current month and year
    const currentDate = new Date();
    const currentMonth = currentDate.getMonth() + 1;

    //set default selected value
    monthC.value = currentMonth;
    monthB.value = currentMonth;

    //function to destroy the existing chart if it exists
    function destroyR() {
        var existingChart = Chart.getChart("resChart");
        if (existingChart) {
            existingChart.destroy();
        }
    }

    function destroyC() {
        var existingChart2 = Chart.getChart("catChart");
        if (existingChart2) {
            existingChart2.destroy();
        }
    }

    function destroyB() {
        var existingChart3 = Chart.getChart("bookChart");
        if (existingChart3) {
            existingChart3.destroy();
        }
    }

    //Reservation
    //function to create or update the chart
    function updateRChart(selectedYear) {
        destroyR();

        //fetch data from the PHP script
        fetch(`../admin/dashboard/reservationdata.php?year=${selectedYear}`)
            .then(response => response.json())
            .then(data => {
                const monthLabels = [
                    "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];

                //initialize an array to hold the data for each month
                const monthData = Array(12).fill(0);

                //update the data array based on the fetched data
                data.forEach(item => {
                    const monthIndex = item.month - 1;
                    monthData[monthIndex] = item.reserverecords;
                });

                new Chart("resChart", {
                    type: "bar",
                    data: {
                        labels: monthLabels,
                        datasets: [{
                            backgroundColor: "#FFECA2",
                            borderColor: "#FFB700",
                            borderWidth: 1,
                            data: monthData
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                grid: {
                                    color: "#737373"
                                },
                                ticks: {
                                    stepSize: 1,
                                    color: 'white',
                                    font: {
                                        size: 13
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    color: "#737373"
                                },
                                ticks: {
                                    color: '#FFECA2',
                                    font: {
                                        size: 13
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: "Discussion Room Reservation Analysis " + selectedYear,
                                color: "white",
                                font: {
                                    size: 18,
                                    family: 'Poppins',
                                }
                            }
                        }
                    }
                });
            });
    }

    yearR.addEventListener("change", () => {
        const selectedYear = yearR.value;
        updateRChart(selectedYear);
    });
    updateRChart(yearR.value);

    //Book Categories
    //function to create or update the chart
    function updateCChart(selectedYear, selectedMonth) {
        destroyC();

        //fetch data from the PHP script
        fetch(`../admin/dashboard/categorydata.php?year=${selectedYear}&month=${selectedMonth}`)
            .then(response => response.json())
            .then(data => {
                var xValues = data.map(item => item.category);
                var yValues = data.map(item => item.borrowrecords);

                var pieColors = ["#FFBAB8", "#FFD9A4", "#FFF6A3", "#D8FFA8", "#B5E3FF", "#CBAFE6"];
                var pieBorders = ["#B80600", "#FF9500", "#FCE300", "#3E7100", "#0064A2", "#51237F"];

                new Chart("catChart", {
                    type: "pie",
                    data: {
                        labels: xValues,
                        datasets: [{
                            backgroundColor: pieColors,
                            borderColor: pieBorders,
                            borderWidth: 1,
                            data: yValues
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: true,
                                labels: {
                                    color: "white"
                                }
                            },
                            title: {
                                display: true,
                                text: "Book Category Trends: " + selectedMonth + " / " + selectedYear,
                                color: "white",
                                font: {
                                    size: 18,
                                    family: 'Poppins',
                                }
                            }
                        }
                    }
                });
            });
    }

    yearC.addEventListener("change", () => {
        const selectedYear = yearC.value;
        const selectedMonth = monthC.value;
        updateCChart(selectedYear, selectedMonth);
    });
    monthC.addEventListener("change", () => {
        const selectedYear = yearC.value;
        const selectedMonth = monthC.value;
        updateCChart(selectedYear, selectedMonth);
    });
    updateCChart(yearC.value, monthC.value);

    //Books
    //function to create or update the chart
    function updateBChart(selectedYear, selectedMonth) {
        destroyB();

        //fetch data from the PHP script
        fetch(`../admin/dashboard/bookdata.php?year=${selectedYear}&month=${selectedMonth}`)
            .then(response => response.json())
            .then(data => {
                var xValues = data.map(item => item.book_title);
                var yValues = data.map(item => item.borrowrecords);

                //define the period for the moving average
                const movingAveragePeriod = 3;

                //calculate the moving average
                const movingAverages = calculateMovingAverage(yValues, movingAveragePeriod);

                new Chart("bookChart", {
                    type: "bar",
                    data: {
                        labels: xValues,
                        datasets: [{
                                backgroundColor(c){
                                    const value = c.raw;
                                    let alpha = (10 + value) / 40;
                                    if (alpha > 1) {
                                        alpha = 1;
                                    };
                                    return `rgba(255, 236, 162, ${alpha})`;
                                },
                                borderColor: "#FFB700",
                                borderWidth: 1,
                                maxBarThickness: 60,
                                data: yValues
                            },
                            {
                                label: `Average Number (${movingAveragePeriod} Months)`,
                                fill: false,
                                borderColor: "#00A7E1",
                                borderWidth: 2,
                                data: movingAverages,
                                type: 'line'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            line: {
                                type: 'linear',
                                position: 'left',
                                grid: {
                                    color: "#737373"
                                },
                                ticks: {
                                    stepSize: 1,
                                    color: "white",
                                    font: {
                                        size: 13
                                    }
                                }
                            },
                            y: {
                                ticks: {
                                    display: false
                                }
                            },
                            x: {
                                grid: {
                                    color: "#737373"
                                },
                                ticks: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: "Book Borrowing Trends: " + selectedMonth + " / " + selectedYear,
                                color: "white",
                                font: {
                                    size: 18,
                                    family: 'Poppins',
                                }
                            }
                        }
                    }
                });
            });
    }

        //function to calculate moving average
        function calculateMovingAverage(data, period) {
            const movingAverages = [];

            for (let i = 0; i < data.length; i++) {
                if (i < period - 1) {
                    //not enough data points for the initial period
                    movingAverages.push(null);
                } else {
                    const average = data.slice(i - period + 1, i + 1).reduce((sum, value) => sum + value, 0) / period;
                    movingAverages.push(average);
                }
            }

            return movingAverages;
        }

    yearB.addEventListener("change", () => {
        const selectedYear = yearB.value;
        const selectedMonth = monthB.value;
        updateBChart(selectedYear, selectedMonth);
    });
    monthB.addEventListener("change", () => {
        const selectedYear = yearB.value;
        const selectedMonth = monthB.value;
        updateBChart(selectedYear, selectedMonth);
    });
    updateBChart(yearB.value, monthB.value);
</script>

</html>