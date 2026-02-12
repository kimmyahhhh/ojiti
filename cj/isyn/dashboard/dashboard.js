(function (jQuery) {
  "use strict";

  // Removed unused #myChart and #d-activity logic

if (document.querySelectorAll('#d-main').length) {
    const options = {
        series: [],
        chart: {
            fontFamily: '"Inter", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"',
            height: 245,
            type: 'area', // Changed to area for gradient fill
            toolbar: {
                show: false
            },
            sparkline: {
                enabled: false,
            },
        },
        colors: ["#3a57e8", "#4bc7d2"],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth', // Changed from straight to smooth for better aesthetic
            width: 3, // Thicker lines for visibility
        },
        markers: {
          size: 5, // Slightly larger
          colors: ["#3a57e8", "#4bc7d2"],
          strokeColors: "#fff",
          strokeWidth: 3, // Thicker stroke
          hover: {
            size: 7,
          }
        },
        yaxis: {
          show: true,
          labels: {
            show: true,
            style: {
              colors: "#64748b", // Muted slate color
              fontSize: '11px',
              fontFamily: 'Inter, sans-serif',
              fontWeight: 500,
            },
            formatter: (val) => {
                if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
                if (val >= 1000) return (val / 1000).toFixed(1) + 'k';
                return val;
            },
            offsetX: -5,
          },
        },
        legend: {
            show: true,
            position: 'top',
            horizontalAlign: 'right',
            fontFamily: 'Inter, sans-serif',
            fontSize: '12px',
            markers: {
                radius: 12, // Circular legend markers
            },
            itemMargin: {
                horizontal: 10,
                vertical: 0
            }
        },
        xaxis: {
            labels: {
                minHeight:22,
                maxHeight:22,
                show: true,
                style: {
                  colors: "#64748b", // Muted slate color
                  fontSize: '11px',
                  fontFamily: 'Inter, sans-serif',
                  fontWeight: 500,
                },
            },
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
            categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
        },
        grid: {
            show: true,
            strokeDashArray: 5,
            borderColor: '#e2e8f0', // Lighter grid lines
            padding: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 10
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: "vertical",
                shadeIntensity: 0.5,
                inverseColors: false,
                opacityFrom: 0.5,
                opacityTo: 0.05,
                stops: [0, 100]
            }
        },
        tooltip: {
            enabled: true,
            theme: 'light',
            style: {
                fontSize: '12px',
                fontFamily: 'Inter, sans-serif',
            },
            y: {
                formatter: function(val) {
                    return "₱" + parseFloat(val).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            }
        },
    };

    const chart = new ApexCharts(document.querySelector("#d-main"), options);
    chart.render();

  // Function to fetch data
  function loadChartData(year) {
      $.ajax({
          url: "routes/dashboard/dashboard.route.php",
          type: "POST",
          data: { action: "GetMainChartData", year: year },
          dataType: "JSON",
          success: function(response) {
              const netTotal = response.NET.reduce((a, b) => a + b, 0);
              const grossTotal = response.GROSS.reduce((a, b) => a + b, 0);

              if (netTotal === 0 && grossTotal === 0) {
                  // No data: Clear chart series or show empty state
                  chart.updateSeries([]);
                  // Optionally, you could update options to show a "No Data" message in the subtitle
                  // or just leave it empty. Here we clear the series so no lines/areas are drawn.
              } else {
                  chart.updateSeries([{
                      name: 'Total (Gross)',
                      data: response.GROSS
                  }, {
                      name: 'Product Sold (Net)',
                      data: response.NET
                  }]);
              }
          },
          error: function(xhr, status, error) {
              console.error("Chart data fetch failed:", error);
          }
      });
  }
  
  // Inventory Chart Logic
  let invChartInstance = null;
  function loadInventoryChart(year) {
    if (!document.querySelector("#d-inventory")) return;

    // Use AJAX to get the monthly data
    $.ajax({
        url: "routes/dashboard/dashboard.route.php",
        type: "POST",
        data: { action: "GetInventoryChartData", year: year },
        dataType: "JSON",
        success: function(response) {
            
            // FORCE RE-RENDER: Always destroy and recreate to prevent mixed charts (Bar vs Area)
            if (invChartInstance) {
                invChartInstance.destroy();
                invChartInstance = null;
            }
            
            // Ensure container is empty
            document.querySelector("#d-inventory").innerHTML = "";

            const options = {
                series: [{
                    name: 'Cost',
                    data: response.COST
                }, {
                    name: 'SRP',
                    data: response.SRP
                }],
                chart: {
                    fontFamily: '"Inter", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"',
                    height: 245,
                    type: 'area', // Area chart for gradient fill
                    toolbar: { show: false },
                    sparkline: { enabled: false },
                },
                colors: ["#3a57e8", "#2dce89"], // Blue for Cost, Green for SRP
                dataLabels: { enabled: false },
                stroke: {
                    curve: 'smooth', // Smooth curves
                    width: 3, // Thicker lines
                },
                markers: {
                    size: 5, // Larger dots
                    colors: ["#3a57e8", "#2dce89"],
                    strokeColors: '#fff',
                    strokeWidth: 3,
                    hover: {
                        size: 7,
                    }
                },
                yaxis: {
                    show: true,
                    labels: {
                        style: { 
                            colors: "#64748b",
                            fontSize: '11px',
                            fontFamily: 'Inter, sans-serif',
                            fontWeight: 500,
                        },
                        offsetX: -5,
                        formatter: (val) => {
                            if (val >= 1000000) return "₱" + (val / 1000000).toFixed(1) + 'M';
                            if (val >= 1000) return "₱" + (val / 1000).toFixed(1) + 'k';
                            return "₱" + val;
                        }
                    },
                },
                legend: { 
                    show: true,
                    position: 'top',
                    horizontalAlign: 'right', 
                    fontFamily: 'Inter, sans-serif',
                    fontSize: '12px',
                    markers: {
                        radius: 12, // Circular legend markers
                    },
                    itemMargin: {
                        horizontal: 10,
                        vertical: 0
                    }
                }, 
                xaxis: {
                    labels: {
                        style: { 
                            colors: "#64748b",
                            fontSize: '11px',
                            fontFamily: 'Inter, sans-serif',
                            fontWeight: 500,
                        },
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
                },
                grid: { 
                    show: true,
                    strokeDashArray: 5,
                    borderColor: '#e2e8f0', 
                    padding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 10
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: "vertical",
                        shadeIntensity: 0.5,
                        inverseColors: false,
                        opacityFrom: 0.5,
                        opacityTo: 0.05,
                        stops: [0, 100]
                    }
                },
                tooltip: { 
                    enabled: true,
                    theme: 'light',
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Inter, sans-serif',
                    },
                    y: {
                        formatter: function(val) {
                            return "₱" + parseFloat(val).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }
                    }
                },
            };
            invChartInstance = new ApexCharts(document.querySelector("#d-inventory"), options);
            invChartInstance.render();
        },
        error: function(xhr, status, error) {
            console.error("Inventory Chart data fetch failed:", error);
        }
    });
  }

  // Load initial data (current year/date)
  const currentDate = new Date();
  const currentYear = currentDate.getFullYear();
  
  // Year Picker Logic
  if ($("#dashboard-year").length) {
      $("#dashboard-year").on('change', function() {
          const selectedYear = $(this).val();
          
          // Refresh charts
          loadChartData(selectedYear);
          loadInventoryChart(selectedYear);
          
          // Refresh stats
          // Stats usually take a range. If user selects a Year, do we show stats for that whole year?
          // Or just today?
          // Usually "Year" filter implies "Stats for that Year".
          // Let's construct a range: Jan 1 to Dec 31 of selected year.
          
          const dFrom = `01/01/${selectedYear}`;
          const dTo = `12/31/${selectedYear}`;
          
          loadDashboardStats(dFrom, dTo);
      });
      
      // Initial Load with default year
      const defaultYear = $("#dashboard-year").val() || currentYear;
      const dFrom = `01/01/${defaultYear}`;
      const dTo = `12/31/${defaultYear}`;
      
      loadChartData(defaultYear);
      loadInventoryChart(defaultYear);
      loadDashboardStats(dFrom, dTo);
  } else {
      // Fallback if element missing
      loadChartData(currentYear);
      loadInventoryChart(currentYear);
      loadDashboardStats(); 
  }

  // Removed old dropdown listener

  document.addEventListener('ColorChange', (e) => {
    console.log(e)
    const newOpt = {
      colors: [e.detail.detail1, e.detail.detail2],
      fill: {
        type: 'gradient',
        gradient: {
            shade: 'dark',
            type: "vertical",
            shadeIntensity: 0,
            gradientToColors: [e.detail.detail1, e.detail.detail2], // optional, if not defined - uses the shades of same color in series
            inverseColors: true,
            opacityFrom: .4,
            opacityTo: .1,
            stops: [0, 50, 60],
            colors: [e.detail.detail1, e.detail.detail2],
        }
    },
   }
    chart.updateOptions(newOpt)
  })

    // Fetch Dashboard Stats for Circular Progress Widgets
    function loadDashboardStats(dateFrom = null, dateTo = null) {
        // If no date provided, use current date formatted appropriately for PHP
        let payload = { action: "GetDashboardStats" };
        
        if (dateFrom && dateTo) {
            payload.dateFrom = dateFrom;
            payload.dateTo = dateTo;
        } else {
            // Fallback to single date or today if not provided (though we try to always provide range now)
            const now = new Date();
            const pad = (n) => n < 10 ? '0' + n : n;
            const dateStr = pad(now.getMonth() + 1) + '/' + pad(now.getDate()) + '/' + now.getFullYear();
            payload.date = dateStr; // Legacy fallback
        }

        $.ajax({
            url: "routes/dashboard/dashboard.route.php",
            type: "POST",
            data: payload,
            dataType: "JSON",
            success: function(response) {
                // Helper to format currency
                const formatCurrency = (val) => "₱" + parseFloat(val).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                const currentYear = new Date().getFullYear();

                // Revenue
                $('#val-revenue').text(formatCurrency(response.revenue));

                // Expenses
                $('#val-expenses').text(formatCurrency(response.expenses));

                // Income
                $('#val-income').text(formatCurrency(response.income));

                // Accounts Receivable
                $('#val-receivable').text(formatCurrency(response.receivable));

                // Accounts Payable
                $('#val-payable').text(formatCurrency(response.payable));

                // Income Budget
                $('#val-income-budget').text(formatCurrency(response.income_budget));

                // Expenses Budget
                $('#val-expenses-budget').text(formatCurrency(response.expenses_budget));
                
                // Inventory Cost
                $('#val-inv-cost').text(formatCurrency(response.inventory_cost));

                // Inventory SRP
                $('#val-inv-srp').text(formatCurrency(response.inventory_srp));

                // Net Income
                $('#val-net-income').text(formatCurrency(response.income));
                
                // Today's Sales
                $('#val-today').text(formatCurrency(response.today_sales));

                // Members
                $('#val-members').text(response.members);
            },
            error: function(xhr, status, error) {
                console.error("Dashboard stats fetch failed:", error);
                console.log("Response text:", xhr.responseText);
            }
        });
    }
    loadDashboardStats();
}
if ($('.d-slider1').length > 0) {
    const options = {
        centeredSlides: false,
        loop: false,
        slidesPerView: 4,
        autoplay:false,
        spaceBetween: 32,
        breakpoints: {
            320: { slidesPerView: 1 },
            550: { slidesPerView: 2 },
            991: { slidesPerView: 3 },
            1400: { slidesPerView: 3 },
            1500: { slidesPerView: 4 },
            1920: { slidesPerView: 6 },
            2040: { slidesPerView: 7 },
            2440: { slidesPerView: 8 }
        },
        pagination: {
            el: '.swiper-pagination'
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev'
        },  

        // And if we need scrollbar
        scrollbar: {
            el: '.swiper-scrollbar'  
        }
    } 
    let swiper = new Swiper('.d-slider1',options);

    document.addEventListener('ChangeMode', (e) => {
      if (e.detail.rtl === 'rtl' || e.detail.rtl === 'ltr') {
        swiper.destroy(true, true)
        setTimeout(() => {
            swiper = new Swiper('.d-slider1',options);
        }, 500);
      }
    })
}

})(jQuery)
