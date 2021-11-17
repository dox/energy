function toggleHiddenMeters() {
  var elems = document.querySelectorAll(".d-none");
  [].forEach.call(elems, function(el) {
    el.classList.remove("d-none");
  });
}

// below this line needs checking for v2


function onInput() {
  var val = document.getElementById("input").value;
  var opts = document.getElementById('dlist').childNodes;
  for (var i = 0; i < opts.length; i++) {
    if (opts[i].value === val) {
      // An item was selected from the list!
      window.location.href = 'index.php?n=node&meterUID='+opts[i].id;
      //alert(opts[i].id);
      break;
    }
  }
}

function readTextFile(file, callback) {
  var rawFile = new XMLHttpRequest();
  rawFile.overrideMimeType("application/json");
  rawFile.open("GET", file, true);
  rawFile.onreadystatechange = function() {
    if (rawFile.readyState === 4 && rawFile.status == "200") {
      callback(rawFile.responseText);
    }
  }
  rawFile.send(null);
}

function purgeOldLogs() {
  event.preventDefault();

  var isGood=confirm('Are you sure you want to purge old logs from the database?  Each meter has its own retention policy.  This action cannot be undone!');

	if(isGood) {
    var formData = new FormData();

    //formData.append("readingUID", this_id);

    var request = new XMLHttpRequest();

    request.open("POST", "../actions/purge_old_readings.php", true);
    request.send(formData);

    // 4. This will be called after the response is received
    request.onload = function() {
      if (request.status != 200) { // analyze HTTP status of the response
        alert("Something went wrong.  Please refresh this page and try again.");
        alert(`Error ${request.status}: ${request.statusText}`); // e.g. 404: Not Found
      } else {
        //readingRow.className = 'visually-hidden';
      }
    }

    request.onerror = function() {
      alert("Request failed");
    };

    return false;
  }
}



function nodeDelete( elem ) {
  event.preventDefault();

  var uid = elem.id;

  var formData = new FormData();

  formData.append("uid", uid);

  var request = new XMLHttpRequest();

  request.open("POST", "../actions/node_delete.php", true);
  request.send(formData);

  // 4. This will be called after the response is received
  request.onload = function() {
    if (request.status != 200) { // analyze HTTP status of the response
      alert("Something went wrong.  Please refresh this page and try again.");
      alert(`Error ${request.status}: ${request.statusText}`); // e.g. 404: Not Found
    } else {
      window.location.href = "index.php?n=nodes";
    }
  }

  request.onerror = function() {
    alert("Request failed");
  };

  return false;
}




function sortTable(n,id) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById(id);
  switching = true;
  //Set the sorting direction to ascending:
  dir = "asc";
  /*Make a loop that will continue until
  no switching has been done:*/
  while (switching) {
    //start by saying: no switching is done:
    switching = false;
    rows = table.getElementsByTagName("TR");
    /*Loop through all table rows (except the
    first, which contains table headers):*/
    for (i = 1; i < (rows.length - 1); i++) {
      //start by saying there should be no switching:
      shouldSwitch = false;
      /*Get the two elements you want to compare,
      one from current row and one from the next:*/
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];
      /*check if the two rows should switch place,
      based on the direction, asc or desc:*/
	  if(n == "0") {
		  if (dir == "asc") {
			if (parseFloat(x.innerHTML.toLowerCase()) > parseFloat(y.innerHTML.toLowerCase())) {
			  //if so, mark as a switch and break the loop:
			  shouldSwitch= true;
			  break;
			}
		  } else if (dir == "desc") {
			if (parseFloat(x.innerHTML.toLowerCase()) < parseFloat(y.innerHTML.toLowerCase())) {
			  //if so, mark as a switch and break the loop:
			  shouldSwitch= true;
			  break;
			}
		  }
	  } else {
		  if (dir == "asc") {
			if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
			  //if so, mark as a switch and break the loop:
			  shouldSwitch= true;
			  break;
			}
		  } else if (dir == "desc") {
			if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
			  //if so, mark as a switch and break the loop:
			  shouldSwitch= true;
			  break;
			}
		  }
	  }
    }
    if (shouldSwitch) {
      /*If a switch has been marked, make the switch
      and mark that a switch has been done:*/
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      //Each time a switch is done, increase this count by 1:
      switchcount ++;
    } else {
      /*If no switching has been done AND the direction is "asc",
      set the direction to "desc" and run the while loop again.*/
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}
