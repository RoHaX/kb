<div class="insert-post-ads1" style="margin-top:20px;">
</div>


  <div class="row">
	<div class="col-md-4">
	</div>
	<div class="col-md-4">
	</div>
  
  </div>

</body>

    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

      const ctx = document.getElementById("chart_e").getContext('2d');
      const myChartE = new Chart(ctx, {
        type: 'pie',
        data: {
          labels: ["JHV", "Basar", "Ausflug",
          "Trenkwalder", "Elternsp.", "VS", "saturday"],
          datasets: [{
            label: 'Einnahmen',
            backgroundColor: ['#6cd900', '#2db300', '#40ff00', '#86b300', '#00d936', '#00d936'],
            borderColor: '#fff',
            data: [3000, 4000, -2000, 5000, 8000, 9000, 2000],
          }]
        },
		options: {
			plugins: {
				legend: {
					display: false,
				}
			}
		},
      });
	  
      const ctxa = document.getElementById("chart_a").getContext('2d');
      const myChartA = new Chart(ctxa, {
        type: 'pie',
        data: {
          labels: ["JHV", "Basar", "Ausflug",
          "Trenkwalder", "Elternsp.", "VS", "saturday"],
          datasets: [{
            label: 'Ausgaben',
            backgroundColor: ['#ff99b3', '#ff73b9', '#ff4c79', '#b3002d', '#ff99b3', '#ff73dc'],
            borderColor: '#fff',
            data: [3000, 4000, -2000, 5000, 8000, 9000, 2000],
          }]
        },
		options: {
			plugins: {
				legend: {
					display: false,
				}
			}
		},
      });
	  
	/*  

      const ctx2 = document.getElementById("charteinnahmen").getContext('2d');

      const myChart2 = new Chart(ctx2, {

        type: 'pie',

        data: {

          labels: ["rice", "yam", "tomato", "potato", "beans", "maize", "oil"],

          datasets: [{

            label: 'food Items',

            backgroundColor: 'rgba(161, 198, 247, 1)',

            borderColor: 'rgb(47, 128, 237)',

            data: [30, 40, 20, 50, 80, 90, 20],

          }]

        },

      });

*/
</script>

</html>

