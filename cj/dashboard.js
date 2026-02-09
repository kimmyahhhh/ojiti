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
            curve: 'straight',
            width: 1, // Thin lines
        },
        markers: {
          size: 4, // Smaller markers
          colors: ["#3a57e8", "#4bc7d2"],
          strokeColors: "#fff",
          strokeWidth: 2,
          hover: {
            size: 6,
          }
        },
        yaxis: {
          show: true,
          labels: {
            show: true,
            style: {
              colors: "#8A92A6",
            },
            offsetX: -5,
          },
        },
        legend: {
            show: true,
            position: 'top',
            horizontalAlign: 'right',
        },
        xaxis: {
            labels: {
                minHeight:22,
                maxHeight:22,
                show: true,
                style: {
                  colors: "#8A92A6",
                },
            },
            lines: {
                show: false 
            },
            categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
        },
        grid: {
            show: true,
            strokeDashArray: 5,
            borderColor: '#f1f1f1',
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                type: "vertical",
                shadeIntensity: 0,
                gradientToColors: undefined, 
                inverseColors: true,
                opacityFrom: .4,
                opacityTo: .1,
                stops: [0, 50, 80],
            }
        },
        tooltip: {
          enabled: true,
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
                    curve: 'straight', // Straight lines
                    width: 1, // Thin lines
                },
                markers: {
                    size: 4, // Add markers (dots)
                    colors: ["#3a57e8", "#2dce89"],
                    strokeColors: '#fff',
                    strokeWidth: 2,
                    hover: {
                        size: 6,
                    }
                },
                yaxis: {
                    show: true,
                    labels: {
                        style: { colors: "#8A92A6" },
                        offsetX: -5,
                        formatter: (val) => "₱" + parseFloat(val).toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0})
                    },
                },
                legend: { 
                    show: true,
                    position: 'top',
                    horizontalAlign: 'right', 
                }, 
                xaxis: {
                    labels: {
                        style: { colors: "#8A92A6" },
                    },
                    categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
                },
                grid: { 
                    show: true,
                    strokeDashArray: 5,
                    borderColor: '#f1f1f1', 
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        type: "vertical",
                        shadeIntensity: 0,
                        gradientToColors: undefined, 
                        inverseColors: true,
                        opacityFrom: .4,
                        opacityTo: .1,
                        stops: [0, 50, 80],
                    }
                },
                tooltip: { enabled: true },
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

                // Helper to update circle progress
                const updateCircle = (id, val, max) => {
                    const el = document.getElementById(id);
                    if(el && typeof CircleProgress !== 'undefined') {
                        el.innerHTML = ''; // Clear existing SVG
                        
                        let percent = 0;
                        if (max > 0) {
                            percent = (val / max) * 100;
                        } else if (val > 0) {
                            percent = 100; // If max is 0 but we have value (e.g. baseline widgets), show full
                        } else {
                            percent = 0;
                        }
                        
                        // Clamp to 0-100
                        if (percent > 100) percent = 100;
                        if (percent < 0) percent = 0;
                        
                        new CircleProgress('#' + id, {
                            min: 0,
                            max: 100,
                            value: percent,
                            textFormat: 'percent',
                        });
                    }
                };

                // Revenue (Baseline)
                const revVal = parseFloat(response.revenue) || 0;
                // Show 100% filled if we have revenue, empty if 0
                updateCircle('circle-progress-01', revVal, revVal); 
                $('#circle-progress-01').parent().find('h6').text(formatCurrency(response.revenue));
                $('#circle-progress-01').parent().find('.text-gray').text(currentYear);

                // Expenses (Relative to Revenue)
                const expVal = parseFloat(response.expenses) || 0;
                updateCircle('circle-progress-02', expVal, revVal); 
                $('#circle-progress-02').parent().find('h6').text(formatCurrency(response.expenses));
                $('#circle-progress-02').parent().find('.text-gray').text(currentYear);

                // Income (Relative to Revenue)
                const incVal = parseFloat(response.income) || 0;
                // Note: Income can be negative. CircleProgress might not handle negative values well visually.
                // We'll treat negative income as 0 progress but the text will show negative amount.
                updateCircle('circle-progress-03', incVal > 0 ? incVal : 0, revVal);
                $('#circle-progress-03').parent().find('h6').text(formatCurrency(response.income));
                $('#circle-progress-03').parent().find('.text-gray').text(currentYear);

                // Accounts Receivable (Relative to Revenue)
                const arVal = parseFloat(response.receivable) || 0;
                updateCircle('circle-progress-04', arVal, revVal);
                $('#circle-progress-04').parent().find('h6').text(formatCurrency(response.receivable));
                $('#circle-progress-04').parent().find('.text-gray').text(currentYear);

                // Accounts Payable (Relative to Expenses)
                const apVal = parseFloat(response.payable) || 0;
                updateCircle('circle-progress-05', apVal, expVal);
                $('#circle-progress-05').parent().find('h6').text(formatCurrency(response.payable));
                $('#circle-progress-05').parent().find('.text-gray').text(currentYear);
                
                // Inventory Chart
                const invCostVal = parseFloat(response.inventory_cost) || 0;
                const invSrpVal = parseFloat(response.inventory_srp) || 0;

                // REMOVED CONFLICTING CHART RENDERING CODE HERE
                // loadInventoryChart() handles the #d-inventory chart.
                // This function should only update the stats widgets.

                // Inventory Cost (Relative to SRP)
                updateCircle('circle-progress-06', invCostVal, invSrpVal);
                $('#circle-progress-06').parent().find('h6').text(formatCurrency(response.inventory_cost));
                $('#circle-progress-06').parent().find('.text-gray').text("Total Cost");
                $('#circle-progress-06').closest('.card').find('.card-title').text("Inventory Value (Cost)");

                // Inventory SRP (Baseline)
                updateCircle('circle-progress-07', invSrpVal, invSrpVal);
                $('#circle-progress-07').parent().find('h6').text(formatCurrency(response.inventory_srp));
                $('#circle-progress-07').parent().find('.text-gray').text("Total SRP");
                $('#circle-progress-07').closest('.card').find('.card-title').text("Inventory Value (SRP)");

                // Net Income (Slide)
                updateCircle('circle-progress-08', incVal > 0 ? incVal : 0, revVal);
                $('#circle-progress-08').parent().find('.counter').text(formatCurrency(response.income));
                
                // Today's Sales (Slide)
                const todayVal = parseFloat(response.today_sales) || 0;
                // Compare today to daily average (Revenue / 365) ?
                // Or just show 100% if > 0. Let's do 100% for now as there's no clear daily target.
                updateCircle('circle-progress-09', todayVal, todayVal);
                $('#circle-progress-09').parent().find('.counter').text(formatCurrency(response.today_sales));

                // Members (Slide)
                const memVal = parseInt(response.members) || 0;
                updateCircle('circle-progress-10', memVal, memVal);
                $('#circle-progress-10').parent().find('.counter').text(response.members);

                // Re-initialize circle progress to show new values if needed
                // Note: CircleProgress usually needs re-init if attributes change dynamically, 
                // or we can just update the text if the visual circle doesn't need to change strictly based on %
                // For now, updating the text is the critical part.
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
