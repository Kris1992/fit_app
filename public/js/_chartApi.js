'use strict';

(function(window, $)
{
	let myChart = new WeakMap();

	class ChartApi
	{

		constructor($chartWrapper)
		{
			this.$chartWrapper = $chartWrapper;

			/*this.$chartWrapper.on(
                'click',
                '.fc-center > :button',
                this.handleButtonClick.bind(this)
            );*/

            $("#chart_pill-js").on(
                'click',
                this.handleChartPillClick.bind(this)
            );
            this.$chartWrapper.on(
                'click',
                '.fc-right > .btn-group > :button',
                this.handleButtonClick.bind(this)
            );

		}

	//	function daysInMonth (month, year) { 
     //           return new Date(year, month, 0).getDate(); 
      //      } 
      	handleButtonClick(){

      		console.log('click');
      		this.updateChart();
		}

	

		handleChartPillClick(){

			var today = new Date();

			//this.showLoadingIcon();
			//var dates = this.getAllDays(today);
			this.getWorkoutsEnergyByDates(today);

			

			

			//this.renderChartJs(dates);
			//console.log(dates[dates.length-1]);
			//this.getWorkouts(timelineBounds).then((data) => {
			//	this.mapWorkoutToCell(data);
			//	this.removeLoadingIcon('success');
        	//}).catch((errorData) => {
        	//	this.removeLoadingIcon('error');
            //})

		}
		getWorkoutsEnergyByDates(date){
			var dates = this.getAllDays(date);

			

			this.getWorkoutsInfo(dates).then((data) => {
				console.log(data);

				this.renderChartJs(dates);
			});

			//console.log(dates);



		}

		getAllDays(date){
			var month = date.getMonth() + 1; 
            var year = date.getFullYear();

			var numDays = this.getDaysInMonth(month, year);
			var dates = [];
			
			for (var i = 1; i <= numDays; i++) {
				let day = i;
				if(day < 10)
				{
					day = '0'+ day; 
				}

				dates.push(year+'-'+month+'-'+day);
			}

			return dates;
		}

		getDaysInMonth(month, year){
			
			return new Date(year, month, 0).getDate();
		}
		renderChartJs(dates){
			var ctx = document.getElementById('chart-js').getContext('2d');
  			var chartData = {
    			type: 'bar',
    			responsive:  true,
    			data: {
      				labels: dates,
        			datasets: [{
            			label: 'Burnout Energy',
            			data: [10, 20, 30, 40, 50, 60, 70],
            			backgroundColor: 'rgba(255, 99, 132, 0.2)',
            			borderColor: 'rgba(255, 99, 132, 1)',
            			borderWidth: 2
        			}]
    			},
    			options: {
    				title: {
    					display: true,
            			text: 'Burnout Energy per day'
    				},
        			scales: {
            			yAxes: [{
                			ticks: {
                    			beginAtZero: true,
                    			callback: function(value, index, values) {
                        			return value + 'kcal';
                    			}
                			},
                			scaleLabel: {
                    			display: true,
                    			labelString: 'Burnout Energy'
                			}
            			}],
            			xAxes: [{
                			//type: 'time',
                			//distribution: 'series',
			               // bounds: 'ticks',
			                //time: {
			                //  displayFormats: {
			                 //   week: 'll'
			                  //}
			                //},
                			scaleLabel: {
                   	 			display: true,
                    			labelString: 'date'
                			}
            			}]
        			}
    			}
			};

			//var myChart = new Chart(ctx, chartData);
			myChart = new Chart(ctx, chartData);
		}

		getWorkoutsInfo(dates){	
			const url = $('#chartWrapper-js').data('url');

			var timelineBounds = new Object();
			timelineBounds.startDate = dates[0];
			timelineBounds.stopDate = dates[dates.length-1];
			return new Promise(function(resolve, reject){
				$.ajax({
					url,
					method: 'POST',
					data: JSON.stringify(timelineBounds)
				}).then(function(data){
					console.log(data);
					console.log(dates);
					resolve(data);
				}).catch(function(jqXHR){
					const errorData = JSON.parse(jqXHR.responseText);
                    reject(errorData);
				});
			});
		}
		updateChart(){
			//myChart.data.datasets[0].data[2] = Math.random() * 100;
			myChart.data.labels[0].data[2] = Math.random() * 100;
			myChart.update();
		}
		
	
	}

	window.ChartApi = ChartApi;
		

})(window, jQuery);
