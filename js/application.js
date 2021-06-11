function toggleHiddenMeters() {
  var elems = document.querySelectorAll(".d-none");
  [].forEach.call(elems, function(el) {
    el.classList.remove("d-none");
  });
}


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

function nodeAdd() {
  event.preventDefault();

  var name = document.getElementById('name').value;
  var location = document.getElementById('location').value;
  var type = document.getElementById('type').value;
  var unit = document.getElementById('unit').value;
  var serial = document.getElementById('serial').value;
  var mprn = document.getElementById('mprn').value;
  var billed = document.getElementById('billed').checked;
  var retention_days = document.getElementById('retention_days').value;
  var enabled = document.getElementById('enabled').checked;
  var geo = document.getElementById('geo').value;
  var supplier = document.getElementById('supplier').value;
  var account_no = document.getElementById('account_no').value;
  var address = document.getElementById('address').value;

  var formData = new FormData();

  formData.append("name", name);
  formData.append("location", location);
  formData.append("type", type);
  formData.append("unit", unit);
  formData.append("serial", serial);
  formData.append("mprn", mprn);
  formData.append("billed", billed);
  formData.append("retention_days", retention_days);
  formData.append("enabled", enabled);
  formData.append("geo", geo);
  formData.append("supplier", supplier);
  formData.append("account_no", account_no);
  formData.append("address", address);

  var request = new XMLHttpRequest();

  request.open("POST", "../actions/node_add.php", true);
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

function nodeEdit() {
  event.preventDefault();

  var uid = document.getElementById('uid').value;
  var name = document.getElementById('name').value;
  var location = document.getElementById('location').value;
  var type = document.getElementById('type').value;
  var unit = document.getElementById('unit').value;
  var serial = document.getElementById('serial').value;
  var mprn = document.getElementById('mprn').value;
  var billed = document.getElementById('billed').checked;
  var retention_days = document.getElementById('retention_days').value;
  var enabled = document.getElementById('enabled').checked;
  var geo = document.getElementById('geo').value;
  var supplier = document.getElementById('supplier').value;
  var account_no = document.getElementById('account_no').value;
  var address = document.getElementById('address').value;
  
  var formData = new FormData();

  formData.append("uid", uid);
  formData.append("name", name);
  formData.append("location", location);
  formData.append("type", type);
  formData.append("unit", unit);
  formData.append("serial", serial);
  formData.append("mprn", mprn);
  formData.append("billed", billed);
  formData.append("retention_days", retention_days);
  formData.append("enabled", enabled);
  formData.append("geo", geo);
  formData.append("supplier", supplier);
  formData.append("account_no", account_no);
  formData.append("address", address);

  var request = new XMLHttpRequest();

  request.open("POST", "../actions/node_edit.php", true);
  request.send(formData);

  // 4. This will be called after the response is received
  request.onload = function() {
    if (request.status != 200) { // analyze HTTP status of the response
      alert("Something went wrong.  Please refresh this page and try again.");
      alert(`Error ${request.status}: ${request.statusText}`); // e.g. 404: Not Found
    } else {
      window.location.href = "index.php?n=node_edit&nodeUID="+uid;
    }
  }

  request.onerror = function() {
    alert("Request failed");
  };

  return false;
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
