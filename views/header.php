<header>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	<div class="container">
	  <a class="navbar-brand" href="index.php">
			<svg width="1em" height="1em">
				<use xlink:href="inc/icons.svg#logo"/>
			</svg> Utility Meter Readings</a>
	  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarLinkCollapse" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
	    <span class="navbar-toggler-icon"></span>
	  </button>
	  <div class="collapse navbar-collapse" id="navbarLinkCollapse">
	    <ul class="navbar-nav me-auto mb-2 mb-md-0">
	      <li class="nav-item d-print-none"><a class="nav-link active" aria-current="page" href="index.php?n=locations">
					<svg width="1em" height="1em" class="text-muted">
						<use xlink:href="inc/icons.svg#locations"/>
					</svg> Locations</a>
	      </li>
	      <li class="nav-item d-print-none"><a class="nav-link" aria-current="page" href="index.php?n=summary">
					<svg width="1em" height="1em" class="text-muted">
						<use xlink:href="inc/icons.svg#usage"/>
					</svg> Usage Summary</a>
	      </li>
	    </ul>
			<?php
			if ($_SESSION['logon'] == true) {
				echo "<div class=\"float-end\">";
				echo "<span class=\"btn btn-sm btn-warning\">Logged On</span>";
				echo "</div>";
			}
			?>
	    <div class="d-flex">
	      <ul class="navbar-nav mr-auto">
	        <li class="nav-item dropdown">
	          <a class="nav-link dropdown-toggle" href="#" id="navbarAvatarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <svg width="1em" height="1em">
    						<use xlink:href="inc/icons.svg#settings"/>
    					</svg> Admin
	          </a>
	          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
							<a class="dropdown-item" href="index.php?n=meter_add" class="text-white">Add New Meter</a>
							<a class="dropdown-item" href="index.php?n=settings" class="text-white">Site Settings</a>
		          <a class="dropdown-item" href="index.php?n=logs" class="text-white">Logs</a>

							<div class="dropdown-divider"></div>

							<?php
							if ($_SESSION['logon'] == true) {
								echo "<a class=\"dropdown-item\" href=\"index.php?n=logon&logout=true\" class=\"text-white\">Log Out</a>";
							} else {
								echo "<a class=\"dropdown-item\" href=\"index.php?n=logon\" class=\"text-white\">Log In</a>";
							}
							?>
	          </div>
	        </li>
	      </ul>
	    </div>
	  </div>

	</div>
	</nav>
</header>
