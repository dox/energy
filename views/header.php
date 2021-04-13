<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
  <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="index.php"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#logo"/></svg> <?php echo site_name; ?></a>
  <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <input class="form-control form-control-dark w-100" type="text" oninput='onInput()' id='input' list='dlist' placeholder="Search" aria-label="Search">
  <ul class="navbar-nav px-3">
    <li class="nav-item text-nowrap">
			<?php
			if (isset($_SESSION['logon']) && $_SESSION['logon'] == true) {
				echo "<a class=\"nav-link\" href=\"index.php?logout=true\">Sign out</a>";
			} else {
				echo "<a class=\"nav-link\" href=\"index.php?n=logon\">Sign in</a>";
			}
			?>

    </li>
  </ul>
</header>

<datalist id='dlist'>
  <?php
  $metersClass = new meters();
  //printArray($metersClass->all());

  foreach ($metersClass->all() AS $meter) {
    $output = "<option id=\"" . $meter['uid'] . "\" value=\"" . escape($meter['name']) . "\">";

    //$output .= "</div>";

    echo $output;
  }
  ?>
</datalist>
