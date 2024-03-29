<?php
admin_gatekeeper();

//check if creating new setting
if (isset($_POST['name'])) {
  $settingsClass->create($_POST);
}

//check if updating existing setting
if (isset($_POST['uid'])) {
  $settingsClass->update($_POST);
}

$settings = $settingsClass->all();
?>

<div class="container px-4 py-5">
  <?php
  $title     = "Admin Settings";
  $actions[] = array('name' => 'View Logs', 'icon' => 'logs', 'href' => 'index.php?n=logs');
  $actions[] = array('name' => 'Add Setting', 'icon' => 'add', 'data-bs-target' => '#exampleModal');
  $actions[] = array('name' => 'separator');
  $actions[] = array('name' => 'Purge Old Readings', 'icon' => 'delete', 'href' => 'javascript:purgeOldLogs();', 'class' => 'text-danger');
  
  echo pageHeader($title, $actions);
  ?>

  <div class="alert alert-danger text-center"><strong>Warning!</strong> Making changes to these settings can disrupt the running of this site.  Proceed with caution.</div>
  
  <div class="accordion" id="accordionExample">
    <?php
    foreach ($settings AS $setting) {
      $itemName = "collapse-" . $setting['uid'];
  
      $output  = "<div class=\"accordion-item\">";
        $output .= "<h2 class=\"accordion-header\" id=\"" . $setting['uid'] . "\">";
        $output .= "<button class=\"accordion-button collapsed\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#" . $itemName . "\" aria-expanded=\"true\" aria-controls=\"" . $itemName . "\">";
        $output .= "<strong>" . $setting['name'] . "</strong>: " . $setting['description'];
        $output .= "</button></h2>";
  
        $output .= "<div id=\"" . $itemName . "\" class=\"accordion-collapse collapse\" aria-labelledby=\"" . $setting['uid'] . "\" data-bs-parent=\"#accordionExample\">";
          $output .= "<div class=\"accordion-body\">";
  
          $output .= "<form method=\"post\" id=\"form-" .  $setting['uid'] . "\" action=\"" . $_SERVER['REQUEST_URI'] . "\" class=\"needs-validation\" novalidate>";
          $output .= "<div class=\"input-group\">";
            $output .= "<input type=\"text\" class=\"form-control\" id=\"value\" name=\"value\" value=\"" . escape($setting['value']). "\">";
            $output .= "<button class=\"btn btn-primary\" type=\"submit\" id=\"button-addon2\">Update</button>";
          $output .= "</div>";
          $output .= "<input type=\"hidden\" id=\"uid\" name=\"uid\" value=\"" . $setting['uid']. "\">";
          $output .= "</form>";
  
  
          $output .= "</div>";
        $output .= "</div>";
      $output .= "</div>";
  
      echo $output;
    }
    ?>
  </div>
</div>

<div class="b-example-divider"></div>

<div class="container px-4 py-5">
  <h2 class="m-3">Icons Available in <code>./inc/icons.svg</code></h2>
  
  <?php
  $iconsArray = array(
    "logo" => "Logo",
    "dashboard" => "Dashboard",
    "home" => "Home",
    "nodes" => "Nodes",
    "locations" => "Locations",
    "alerts" => "Alerts",
    "graph-up" => "Graph Up",
    "graph-down" => "Graph Down",
    "settings" => "Settings",
    "electric" => "Electric",
    "gas" => "Gas",
    "water" => "Water",
    "refuse" => "Refuse",
    "temperature" => "temperature",
    "co2" => "CO2",
    "hidden" => "Hidden",
    "users-class" => "Users",
    "add" => "Add",
    "edit" => "Edit",
    "delete" => "Delete",
    "logs" => "logs",
    "report" => "Report",
    "download" => "Download",
    "github" => "GitHub"
  );
  
  
  
  echo "<ul class=\"list-group\">";
  foreach ($iconsArray AS $icon => $name) {
    $output  = "<li class=\"list-group-item list-group-item-action\">";
    $output .= "<div class=\"d-flex w-100 justify-content-between\">";
    $output .= "<span><svg width=\"2em\" height=\"2em\">";
    $output .= "<use xlink:href=\"inc/icons.svg#" . $icon . "\"/>";
    $output .= "</svg> " . $name . "</span>";
    $output .= "<small>[" . $icon . "]</small>";
    $output .= "</div>";
    $output .= "</li>";
  
    echo $output;
  }
  
  echo "</ul>";
  ?>
</div>



<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" id="termForm" action="index.php?n=settings">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add New Setting</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div class="mb-3">
            <label for="name">Setting Name</label>
            <input type="text" class="form-control" name="name" id="name" aria-describedby="termNameHelp">
          </div>

          <div class="mb-3">
            <label for="date_start">Setting Description</label>
            <input type="text" class="form-control" name="description" id="description" aria-describedby="termStartDate">
          </div>

          <div class="mb-3">
            <label for="date_end">Setting Value</label>
            <input type="text" class="form-control" name="value" id="value" aria-describedby="termEndDate">
          </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary"><svg width="2em" height="2em"><use xlink:href="inc/icons.svg#sliders"/></svg> Add Setting</button>
      </div>
      </form>
    </div>
  </div>
</div>

<script>
function dismiss(el){
  document.getElementById(el).parentNode.style.display='none';
};
</script>
