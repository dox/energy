<?php
$navbarArray['nodes'] = array(
    "title" => "Nodes",
    "icon" => "nodes",
    "link" => "index.php?n=nodes",
    "pages" => array("node", "node_edit", "readings")
);
$navbarArray['map'] = array(
      "title" => "Map",
      "icon" => "locations",
      "link" => "index.php?n=map",
      "pages" => array("locations", "location", "location_edit")
);
$navbarArray['reports'] = array(
      "title" => "Reports",
      "icon" => "report",
      "link" => "index.php?n=reports",
      "pages" => array()
);
$navbarArray['settings'] = array(
      "title" => "Settings",
      "icon" => "settings",
      "link" => "index.php?n=settings",
      "pages" => array()
);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php" ><svg class="me-2 text-light" width="40" height="40" role="img" aria-label="<?php echo site_name; ?>"><use xlink:href="inc/icons.svg#logo"></use></svg></a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php
        foreach ($navbarArray AS $key => $navBarLink) {
          $icon = "<svg class=\"m-1\" width=\"20\" height=\"20\"><use xlink:href=\"inc/icons.svg#" . $navBarLink['icon'] . "\"></use></svg>";
          
          if ($key == $_GET['n']) {
            $active = " text-white";
          } else {
            if (in_array($_GET['n'], $navBarLink['pages'])) {
              $active = " text-white";
            } else {
              $active = "text-secondary";
            }
          }
          
          $output  = "<li class=\"nav-item\">";
          $output .= "<a class=\"nav-link px-2 " . $active . "\" href=\"" . $navBarLink['link'] . "\" >";
          $output .= $icon;
          $output .= $navBarLink['title'];
          $output .= "</a>";
          $output .= "</li>";
          
          echo $output;
        }
        ?>
      </ul>
      <form class="d-flex">
        <?php
        if (isset($_SESSION['logon']) && $_SESSION['logon'] == true) {
          echo "<a href=\"index.php?logout=true\" class=\"btn btn-success me-2\">Log Off</a>";
        } else {
          echo "<a href=\"index.php?n=logon\" class=\"btn btn-outline-success\">Log In</a>";
        }
        ?>
      </form>
    </div>
  </div>
</nav>