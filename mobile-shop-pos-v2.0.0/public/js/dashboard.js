'use strict';

// Hardcoded base URL to avoid any caching/extension issues
var DASHBOARD_BASE_URL = window.location.protocol + '//' + window.location.host + '/mobile-shop-pos/';

$(document).ready(function() {
    console.log('Dashboard loaded, base URL:', DASHBOARD_BASE_URL);
    
    checkDocumentVisibility(checkLogin);//check document visibility in order to confirm user's log in status
    
    //get earnings for current  year on page load
    getEarnings();
    
    //WHEN "YEAR" IS CHANGED IN ORDER TO CHANGE THE YEAR OF ACCOUNT BEING SHOWN
    $("#earningAndExpenseYear").change(function(){
        var year = $(this).val();
        
        if(year){
            $("#yearAccountLoading").html("<i class='"+spinnerClass+"'></i> Loading...");
            
            //get earnings for current  year on page load
            getEarnings(year);
        }
    });
});


/*
********************************************************************************************************************************
********************************************************************************************************************************
********************************************************************************************************************************
********************************************************************************************************************************
********************************************************************************************************************************
*/
/*
********************************************************************************************************************************
********************************************************************************************************************************
********************************************************************************************************************************
********************************************************************************************************************************
********************************************************************************************************************************
*/

/**
 * 
 * @param {type} year
 * @returns {undefined}
 */
function getEarnings(year){
    var yearToFetch = year || '';
    // Remove trailing slash to avoid redirect
    var url = DASHBOARD_BASE_URL + "dashboard/earningsGraph" + (yearToFetch ? "/" + yearToFetch : "");
    console.log('Fetching earnings from:', url);
    
    $.ajax({
        type: 'GET',
        url: url,
        dataType: "json"
    }).done(function(response){
        // Check if session expired
        if(response.status === 0 && response.error) {
            console.log('Session expired, please login again');
            $("#logInModal").modal("show");
            return;
        }

        var lineChartData = {
          labels : ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"],
          datasets : [{
            label: "Monthly Sales",
            fillColor : "rgba(92, 184, 92, 0.2)", // Light green fill
            strokeColor : "rgba(92, 184, 92, 1)", // Green line
            pointColor : "rgba(92, 184, 92, 1)", // Green points
            pointStrokeColor : "#fff",
            pointHighlightFill : "#fff",
            pointHighlightStroke : "rgba(92, 184, 92, 1)",
            data : response.total_earnings
          }]
        };

        //show the expense title
        document.getElementById('earningsTitle').innerHTML = "Monthly Sales (" + response.earningsYear +")";

        var earningsGraph = document.getElementById("earningsGraph").getContext("2d");

        window.myLine = new Chart(earningsGraph).Line(lineChartData, {
          responsive : true,
          scaleGridLineColor : "rgba(255,255,255,0.1)",
          scaleShowHorizontalLines: true,
          scaleShowVerticalLines: false,
          bezierCurve : true, // Smooth curves
          bezierCurveTension : 0.4, // Curve smoothness
          pointDot : true,
          pointDotRadius : 4,
          pointDotStrokeWidth : 2,
          datasetStroke : true,
          datasetStrokeWidth : 3,
          datasetFill : true,
          scaleOverride : false, // Let Chart.js auto-calculate scale
          scaleStartValue : 0, // Start from 0
          scaleBeginAtZero : true // Ensure scale begins at zero
        });

        //remove the loading info
        $("#yearAccountLoading").html("");
    }).fail(function(xhr, status, error){
        console.log('Earnings request failed:', status, error);
        $("#yearAccountLoading").html("<span style='color:red'>Failed to load earnings</span>");
    });
}

/*
********************************************************************************************************************************
********************************************************************************************************************************
********************************************************************************************************************************
********************************************************************************************************************************
********************************************************************************************************************************
*/


/**
 * 
 * @returns {undefined}
 */
function loadPaymentMethodChart(year){
    var yearToGet = year ? year : "";
    // Remove trailing slash to avoid redirect
    var url = DASHBOARD_BASE_URL + "dashboard/paymentmethodchart" + (yearToGet ? "/" + yearToGet : "");
    console.log('Fetching payment methods from:', url);
    
    $.ajax({
        type: 'GET',
        url: url,
        dataType: "json",
        success: function(response) {
            // Check if session expired
            if(response.status === 0 && response.error) {
                console.log('Session expired, please login again');
                $("#logInModal").modal("show");
                return;
            }
            
            var cash = response.cash;
            var pos = response.pos;
            var cashAndPos = response.cashAndPos;

            if(response.status === 1) {
                if((cash === 0 && pos === 0 && cashAndPos === 0)) {
                  var paymentMethodData = [{
                    value: 1,
                    color:"#4D5360",
                    highlight: "#616774",
                    label: "No Payment"
                  }];
                } 

                else {
                  var paymentMethodData = [{
                    value: cash,
                    color:"#084D5F",
                    highlight: "#0B6B85",
                    label: "Cash Only"
                  }, {
                    value: pos,
                    color: "#557f7c",
                    highlight: "#556f7c",
                    label: "POS Only"
                  }, {
                    value: cashAndPos,
                    color: "#333",
                    highlight: "pink",
                    label: "Cash and POS"
                  }];
                }
            } 

            else {//if status is 0
                var paymentMethodData = [{
                  value: 1,
                  color:"#4D5360",
                  highlight: "#616774",
                  label: "No Payment"
                }];
            }
          
            var ctx = document.getElementById("paymentMethodChart").getContext("2d");
            window.myPie = new Chart(ctx).Pie(paymentMethodData);

            //remove the loading info
            $("#yearAccountLoading").html("");
            
            //append the year we are showing
            $("#paymentMethodYear").html(" - "+response.year);
        },
        error: function(xhr, status, error) {
            console.log('Payment method chart request failed:', status, error);
        }
    });
}