if(window.google!==undefined){google.setOnLoadCallback(function(){var chart=document.getElementById("chart");var table=document.querySelector("table > tbody");var dataRaw=window.printData;var dataTable=google.visualization.arrayToDataTable(dataRaw);var instance=new google.visualization.PieChart(chart);instance.draw(dataTable,{pieHole:0.5,legend:{position:"labeled"},chartArea:{width:"95%",height:"95%"},pieSliceText:"none",sliceVisibilityThreshold:0,vAxis:{format:"short",},hAxis:{format:"short",}});var slices=document.querySelectorAll("svg > g > path");dataRaw.forEach(function(value,index){if(index===0){return;}var slice=slices[(index-1)];var container=document.createElement("tr");var color=document.createElement("td");var label=document.createElement("td");var votes=document.createElement("td");color.innerHTML='<div style="display: inline-block;background: %color%;width: 0.75em;height: 0.75em;"></div>'.replace("%color%",slice.getAttribute("fill"));label.innerHTML=value[0];votes.innerHTML=value[1];container.appendChild(color);container.appendChild(label);container.appendChild(votes);table.appendChild(container);});setTimeout(window.print,1000);});}