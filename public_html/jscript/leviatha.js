$(document).ready(function() {
    
    $('.action-expand').click(function() {
        $('.js-itemnode').toggle(200);
    });
    
    $(function() {
    
        var dataArray = [ [0, 48], [1, 66] ]; 
        
        var data = [{
            label: "Item Level",
            data: dataArray
        }]
        
        var options = {
            legend: {
                show: true,
                margin: 5,
                backgroundOpacity: 0.2
            },
            bars: {
                show: true,
                align: "left"
            },
            grid: {
                borderWidth: 1,
                borderColor: "#ddd"
            },
            xaxis: {
                ticks: [[0, "Loot Average"], [1, "Item Level"]]
            },
            yaxis: {
            }
        };
        
        var plotarea = ('#plot');
        $.plot(plotarea, data, options);
    });
});