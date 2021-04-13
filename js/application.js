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

function loadJSON(path, success, error) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function()
    {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                if (success)
                    success(JSON.parse(xhr.responseText));
            } else {
                if (error)
                    error(xhr);
            }
        }
    };
    xhr.open("GET", path, true);
    xhr.send();
}

function addData(chart, label, data) {
  chart.data.labels.push(label);
  chart.data.datasets.forEach((dataset) => {
    dataset.data.push(data);
  });

  chart.update();
}
