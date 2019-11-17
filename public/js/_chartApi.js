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

      	handleButtonClick() {
      		var date = new Date(2018, 11, 24, 10, 33, 30, 0);// zmienic na date
			this.showChartByEnergy(date, true);
		}

		handleChartPillClick() {
			var today = new Date();
			this.showChartByEnergy(today);
		}

		showChartByEnergy(date, update = false) {
			var dates = this.getAllDaysInCurrentMonth(date);

			this.getEnergyByDates(dates).then((energyList) => {
				if (update == false ) {
					this.renderChartJs(dates, energyList);
				} else {
					this.updateChartJs(dates, energyList);
				}
				
			});
		}

		getAllDaysInCurrentMonth(date) {
			var month = date.getMonth() + 1; 
            var year = date.getFullYear();

			var numDays = this.getNumDaysInMonth(month, year);
			var dates = [];
			
			for (var i = 1; i <= numDays; i++) {
				let day = i;
				if(day < 10) {
					day = '0'+ day; 
				}

				dates.push(year+'-'+month+'-'+day);
			}

			return dates;
		}

		getNumDaysInMonth(month, year){
			return new Date(year, month, 0).getDate();
		}

		renderChartJs(dates, energyList) {
			var ctx = document.getElementById('chart-js').getContext('2d');
  			var chartData = {
    			type: 'bar',
    			responsive:  true,
    			data: {
      				labels: dates,
        			datasets: [{
            			label: 'Burnout Energy',
            			data: energyList,
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

			myChart = new Chart(ctx, chartData);
		}

		getEnergyByDates(dates) {	
			const url = $('#chartWrapper-js').data('url');

			return new Promise(function(resolve, reject){
				$.ajax({
					url,
					method: 'POST',
					data: JSON.stringify(dates)
				}).then(function(energyList){
					resolve(energyList);
				}).catch(function(jqXHR){
					const errorData = JSON.parse(jqXHR.responseText);
                    reject(errorData);
				});
			});
		}

		updateChartJs(dates, energyList) {
			myChart.data.datasets[0].data = energyList;
			myChart.data.labels= dates;
			myChart.update();
		}
		
	
	}

	window.ChartApi = ChartApi;
		

})(window, jQuery);
