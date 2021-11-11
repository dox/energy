<header class="p-3 bg-dark text-white shadow">
  <div class="container">
  <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
    <a href="index.php" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
    <svg class="bi me-2" width="40" height="40" role="img" aria-label="<?php echo site_name; ?>"><use xlink:href="inc/icons.svg#logo"></use></svg>
    </a>

    
    <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0 text-small">
      <li>
      <a href="index.php" class="nav-link text-secondary">
        <svg class="bi d-block mx-auto mb-1" width="24" height="24"><use xlink:href="inc/icons.svg#dashboard"></use></svg>
        Home
      </a>
      </li>
      <li>
      <a href="index.php?n=nodes" class="nav-link text-white">
        <svg class="bi d-block mx-auto mb-1" width="24" height="24"><use xlink:href="inc/icons.svg#nodes"></use></svg>
        Nodes
      </a>
      </li>
      <li>
      <a href="index.php?n=map" class="nav-link text-white">
        <svg class="bi d-block mx-auto mb-1" width="24" height="24"><use xlink:href="inc/icons.svg#locations"></use></svg></svg>
        Map
      </a>
      </li>
      <li>
        <a href="index.php?n=reports" class="nav-link text-white">
        <svg class="bi d-block mx-auto mb-1" width="24" height="24"><use xlink:href="inc/icons.svg#report"></use></svg>
        Reports
        </a>
      </li>
      <li>
        <a href="index.php?n=settings" class="nav-link text-white">
          <svg class="bi d-block mx-auto mb-1" width="24" height="24"><use xlink:href="inc/icons.svg#settings"></use></svg>
          Settings
        </a>
        </li>
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