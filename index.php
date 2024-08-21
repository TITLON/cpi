<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:site" content="@espensirnes">  <!-- Optional: your site's Twitter handle -->
	<meta name="twitter:title" content="Kjerneinflasjon tolvmånedersendring">  <!-- A brief title for the content/website -->
	<meta name="twitter:description" content="KPI-JAE, sesongjustert">
	<meta name="twitter:image" content="https://titlon.uit.no/cpi/kpi.png">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KPI tolvmåneders endring</title>
    <!-- Include Plotly.js library -->
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
	<style>
        /* Ensure no margins or padding */
        body, html {
            margin: 0;
            padding: 0;
        }

        /* Styling for the container holding the two divs */
        .container {
            display: flex;  /* Use Flexbox to place divs side by side */
            max-width: 800px;  /* Set max width */
            margin: 0;
        }

        /* Style for the div that links to index.html */
        .link-div {
            flex: 1;  /* Occupy half the available space */
            height: 50px;
            background-color: #e0e4e2 ;  /* Example background color */
            cursor: pointer;  /* Change cursor to hand pointer on hover */
        }

        .link-div:hover {
            opacity: 0.8;  /* Slight fade on hover for a touch of interactivity */
        }

        /* Style for the other div */
        .other-div {
            flex: 1;  /* Occupy half the available space */
            height: 50px;
            background-color: #a89edf;  /* Example background color */
        }
		.link-div, .other-div {
			/* ... other styles ... */

			/* Use Flexbox for centering */
			display: flex;
			align-items: center; /* Centers vertically */
			justify-content: center; /* Centers horizontally */

			/* Style for the text */
			font-family: Arial, sans-serif; /* Use Arial as the font */
			font-size: 20px; /* Example font size */
			color: white; /* Font color (change if needed) */
		}
    </style>
</head>
<body>
<div class="container">
		<div class="other-div">Totalindeks</div>
        <div class="link-div" onclick="location.href='https://titlon.uit.no/cpi/kpi_jae.html';">Kjerneinflasjon</div>
		<div class="link-div" onclick="location.href='https://titlon.uit.no/cpi/kpi_jae_instant.html';">Umiddelbar kjerneinflasjon</div>


    </div>
<!-- Container for the plot -->



<div id="plotContainer" style="max-width:800px;"></div>
<p style="font-family: 'Arial';">&nbsp&nbsp&nbspStartår:&nbsp&nbsp&nbsp<input type="range" min="1978" max="2023" value="" 
		class="slider" id="dateSlider"  >
<label id="sliderLabel"></label></p>
<div id="dataTableContainer" style="max-width:800px;"></div>
<a href="https://www.ssb.no/statbank/table/03013/" style="font-family: 'Arial'; color: darkblue; text-decoration: none;">&nbsp&nbsp&nbspKilde: SSB</a>

<!-- JavaScript -->
<script>
	//Sets initial from year to display:
	const currentYear = new Date().getFullYear();
    const desiredYear = currentYear - 2;

    // Set the value of the input range and the label to the calculated year.
    document.getElementById('dateSlider').value = desiredYear;
    document.getElementById('sliderLabel').textContent = desiredYear;

    // Define the URL and JSON-query
		const url = "https://data.ssb.no/api/v0/no/table/03013/";
		const jsonQuery = {
			"query": [
				{
					"code": "Konsumgrp",
					"selection": {
						"filter": "vs:CoiCop2016niva1",
						"values": ["TOTAL"]
					}
				},
				{
					"code": "ContentsCode",
					"selection": {
						"filter": "item",
						"values": [
							"KpiIndMnd",
							"Manedsendring",
							"Tolvmanedersendring"
						]
					}
				}
			],
			"response": {
				"format": "json-stat2"
			}
		};

		// Make the API request
		fetch(url, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify(jsonQuery)
		})
		.then(response => response.json())
		.then(data => {
			const dates = Object.values(data.dimension.Tid.category.label);
			const valuesLen = data.value.length;
			const oneThird = Math.floor(valuesLen / 3);  // Ensure you're using floor() for proper slicing

			const kpiIndMnd = data.value.slice(0, oneThird);
			const manedsendring = data.value.slice(oneThird, 2 * oneThird);
			const tolvmanedersendring = data.value.slice(2 * oneThird);




			// Plotting with Plotly.js
			document.getElementById('dateSlider').min = dates[0].substr(0, 4);
			document.getElementById('dateSlider').max = dates[dates.length-1].substr(0, 4);

			var layout = {
				title: 'Konsumprisindeksen (KPI) tolvmånedersendring',
				yaxis: {
					range: [0, 8]
				},
				legend: {
					x: 0.05,  // Adjust these values as needed for your specific placement
					y: 0.95,
					xanchor: 'left',
					yanchor: 'top',
					bgcolor: 'rgba(255,255,255,0.7)' // Optional, makes legend slightly transparent
				}
			};
			
			const reversedDates = [...dates].reverse();
			const reversed12endr = [...tolvmanedersendring].reverse();

			var traceTable = {
				type: 'table',
				header: {
					values: ['Date', 'Inflasjon (KPI)'],
					align: 'left',
					line: {width: 1, color: 'black'},
					fill: {color: '#d3d3d3'}
				},
				cells: {
					values: [reversedDates, reversed12endr],
					align: 'left',
					line: {color: 'black', width: 1},
					height: 30
				}
			};

			var layoutTable = {
				title: ''
			};

			Plotly.newPlot('dataTableContainer', [traceTable], layoutTable);  // Assumes you have another div with the id 'dataTableContainer' for the table

					
			document.getElementById("dateSlider").addEventListener("input", function() {
				// Get the selected year from the slider
				const selectedYear = parseInt(this.value);

				// Update the label to show the selected year
				document.getElementById("sliderLabel").textContent = selectedYear;

				// Filter the data based on the selected year
				const filteredDates = dates.filter(date => parseInt(date.split("-")[0]) >= selectedYear);
				const startIndex = dates.indexOf(filteredDates[0]);
				const filteredTolvmanedersendring = tolvmanedersendring.slice(startIndex);

				// Update the plot with the filtered data
				Plotly.newPlot('plotContainer', [
					{
						x: filteredDates,
						y: filteredTolvmanedersendring,
						name: 'Inflasjon (KPI)',
						type: 'bar'
					},
					{
						x: filteredDates,
						y: Array(dates.length).fill(2.0),  // Creates an array of the same length as 'dates' filled with the value 5
						type: 'scatter',
						mode: 'lines',
						name: 'Inflasjonsmål',
						line: {
							color: 'gray',
							width: 1.5,
							dash: 'dot'
						}
					}
			], layout);
			});
			document.getElementById("dateSlider").dispatchEvent(new Event("input"));
					
		})
			
			

		.catch(error => {
						console.error("Error fetching or processing data:", error);
					});





	








</script>

</body>
</html>