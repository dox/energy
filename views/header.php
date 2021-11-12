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
/*
$navbarArray['persons_all'] = array(
  "title" => "Persons",
  "icon" => "person",
  "sublinks" => array(
    array(
      "title" => "Suspended",
      "link" => "./index.php?n=persons_all&filter=suspended"
    ),
    array(
      "title" => "Students",
      "link" => "./index.php?n=persons_all&filter=students"
    ),
    array(
      "title" => "Staff",
      "link" => "./index.php?n=persons_all&filter=staff"
    ),
    array(
      "title" => "All",
      "link" => "./index.php?n=persons_all&filter=all"
    )
  )
);
*/

?>

<header class="p-3 bg-dark text-white shadow">
  <div class="container">
  <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
    <a href="index.php" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
    <svg class="bi me-2" width="40" height="40" role="img" aria-label="<?php echo site_name; ?>"><use xlink:href="inc/icons.svg#logo"></use></svg>
    </a>

    
    <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0 text-small">
      <?php
      foreach ($navbarArray AS $key => $navBarLink) {
        $icon = "<svg class=\"bi d-block mx-auto mb-1\" width=\"24\" height=\"24\"><use xlink:href=\"inc/icons.svg#" . $navBarLink['icon'] . "\"></use></svg>";
      
        if ($key == $_GET['n']) {
          $active = " text-white";
        } else {
          if (in_array($_GET['n'], $navBarLink['pages'])) {
            $active = " text-white";
          } else {
            $active = "text-secondary";
          }
        }
      
        $output  = "<li>";
        $output .= "<a class=\"nav-link " . $active . "\" href=\"" . $navBarLink['link'] . "\" >";
        $output .= $icon;
        $output .= $navBarLink['title'];
        $output .= "</a>";
        $output .= "</li>";
        
        echo $output;
      }
      ?>
    </ul>

    <div class="text-end">
    <!--<button type="button" class="btn btn-outline-light me-2">Login</button>-->
      <?php
      if (isset($_SESSION['logon']) && $_SESSION['logon'] == true) {
        echo "<a href=\"index.php?logout=true\" class=\"btn btn-warning\">Log Off</a>";
      } else {
        echo "<a href=\"index.php?n=logon\" class=\"btn btn-outline-warning\">Log In</a>";
      }
      ?>
    
    </div>
  </div>
  </div>
</header>