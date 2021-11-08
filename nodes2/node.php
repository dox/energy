<?php
$meter = new meter($_GET['nodeUID']);
$recentReadings = readings::meter_all_readings($meter->uid, 5);

$dateFrom = date('Y-m-d', strtotime('12 months ago'));
$dateTo = date('Y-m-d');

$consumptionByMonth = array_reverse($meter->consumptionBetweenDatesByMonth($dateFrom, $dateTo), true);
$consumptionTotal = array_sum($consumptionByMonth);

?>
<div class="py-4">
	<div class="dropdown">
		<button class="btn btn-gray-800 d-inline-flex align-items-center me-2 dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
			</svg>
			 New Task
		</button>
		<div class="dropdown-menu dashboard-dropdown dropdown-menu-start mt-2 py-1">
			<a class="dropdown-item d-flex align-items-center" href="#">
				<span class="sidebar-icon">
					<svg class="dropdown-icon me-2"><use xlink:href="inc/icons.svg#add"/></svg>
				</span>
				 Add New Reading
			</a>
			<a class="dropdown-item d-flex align-items-center" href="#">
				<span class="sidebar-icon">
					<svg class="dropdown-icon me-2"><use xlink:href="inc/icons.svg#edit"/></svg>
				</span>
				 Edit Node
			</a>
			<a class="dropdown-item d-flex align-items-center" href="#">
				<span class="sidebar-icon">
					<svg class="dropdown-icon me-2"><use xlink:href="inc/icons.svg#download"/></svg>
				</span>
				 Export Data 
			</a>
			<div role="separator" class="dropdown-divider my-1"></div>
			<a class="dropdown-item d-flex align-items-center" href="#">
				<span class="sidebar-icon">
					<svg class="dropdown-icon text-danger me-2"><use xlink:href="inc/icons.svg#delete"/></svg>
				</span>
				 Delete This Node
			</a>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12 mb-4">
		<div class="card bg-yellow-100 border-0 shadow">
			<div class="card-header d-sm-flex flex-row align-items-center flex-0">
				<div class="d-block mb-3 mb-sm-0">
					<div class="fs-5 fw-normal mb-2">Energy Usage</div>
					<h2 class="fs-3 fw-extrabold"><?php echo number_format($consumptionTotal, 0); ?></h2>
					<div class="small mt-2">
						<span class="fw-normal me-2">Yesterday</span>
						<span class="fas fa-angle-up text-success"></span>
						<span class="text-success fw-bold">10.57%</span>
					</div>
				</div>
				<div class="d-flex ms-auto">
					<a href="#" class="btn btn-secondary text-dark btn-sm me-2">Month</a>
					<a href="#" class="btn btn-dark btn-sm me-3">Year</a>
				</div>
			</div>
			<div class="card-body p-2">
				<div class="ct-chart-sales-value ct-double-octave ct-series-g"></div>
			</div>
		</div>
	</div>
	<div class="col-12 col-sm-6 col-xl-4 mb-4">
		<div class="card border-0 shadow">
			<div class="card-body">
				<div class="row d-block d-xl-flex align-items-center">
					<div class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
						<div class="icon-shape icon-shape-primary rounded me-4 me-sm-0">
							<span class="icon icon-md">
								<svg><use xlink:href="inc/icons.svg#electric"/></svg>
							</span>
						</div>
						<div class="d-sm-none">
							<h2 class="h5">Electricity Usage</h2>
							<h3 class="fw-extrabold mb-1">345,678</h3>
						</div>
					</div>
					<div class="col-12 col-xl-7 px-xl-0">
						<div class="d-none d-sm-block">
							<h2 class="h5">Electricity Usage</h2>
							<h3 class="fw-extrabold mb-1">345k</h3>
						</div>
						<small class="d-flex align-items-center">
							Jan 1 - <?php echo date('M j'); ?>, 
							<svg class="icon icon-xxs text-gray-500 ms-2 me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd"></path>
							</svg>
							 SUA
						</small>
						<div class="small d-flex mt-1">
							<svg class="icon icon-xs text-success" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
							</svg>
							<div>
								<span class="text-success fw-bolder me-1">22%</span>
								 Since last month
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-12 col-sm-6 col-xl-4 mb-4">
		<div class="card border-0 shadow">
			<div class="card-body">
				<div class="row d-block d-xl-flex align-items-center">
					<div class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
						<div class="icon-shape icon-shape-secondary rounded me-4 me-sm-0">
							<span class="icon icon-md">
								<svg><use xlink:href="inc/icons.svg#gas"/></svg>
							</span>
						</div>
						<div class="d-sm-none">
							<h2 class="fw-extrabold h5">Gas Usage</h2>
							<h3 class="mb-1">43,594 m3</h3>
						</div>
					</div>
					<div class="col-12 col-xl-7 px-xl-0">
						<div class="d-none d-sm-block">
							<h2 class="h5">Gas Usage</h2>
							<h3 class="fw-extrabold mb-1">43,594 m3</h3>
						</div>
						<small class="d-flex align-items-center">
							Feb 1 - Apr 1, 
							<svg class="icon icon-xxs text-gray-500 ms-2 me-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd"></path>
							</svg>
							 SUA
						</small>
						<div class="small d-flex mt-1">
							<svg class="icon icon-xs text-danger" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
							</svg>
							<div>
								<span class="text-danger fw-bolder me-1">2%</span>
								 Since last month
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-12 col-sm-6 col-xl-4 mb-4">
		<div class="card border-0 shadow">
			<div class="card-body">
				<div class="row d-block d-xl-flex align-items-center">
					<div class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
						<div class="icon-shape icon-shape-tertiary rounded me-4 me-sm-0">
							<span class="icon icon-md">
								<svg><use xlink:href="inc/icons.svg#water"/></svg>
							</span>
						</div>
						<div class="d-sm-none">
							<h2 class="fw-extrabold h5">Water Usage</h2>
							<h3 class="mb-1">50.88%</h3>
						</div>
					</div>
					<div class="col-12 col-xl-7 px-xl-0">
						<div class="d-none d-sm-block">
							<h2 class="h5">Water Usage</h2>
							<h3 class="fw-extrabold mb-1">50.88%</h3>
						</div>
						<small>Feb 1 - Apr 1</small>
						<div class="small d-flex mt-1">
							<svg class="icon icon-xs text-success" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
							</svg>
							<div>
								<span class="text-success fw-bolder me-1">4%</span>
								 Since last month
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-12 col-xl-8">
		<div class="row">
			<div class="col-12 mb-4">
				<div class="card border-0 shadow">
					<div class="card-header">
						<div class="row align-items-center">
							<div class="col">
								<h2 class="fs-5 fw-bold mb-0">Recent Readings</h2>
							</div>
							<div class="col text-end">
								<a href="index2.php?n=readings&nodeUID=<?php echo $meter->uid; ?>" class="btn btn-sm btn-primary">See all</a>
							</div>
						</div>
					</div>
					<div class="table-responsive">
						<table class="table align-items-center table-flush">
							<thead class="thead-light">
								<tr>
									<th class="border-bottom" scope="col">Date</th>
									<th class="border-bottom" scope="col">Reading</th>
									<th class="border-bottom" scope="col">Username</th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($recentReadings AS $reading) {
									$output  = "<tr>";
									$output .= "<th class=\"text-gray-900\" scope=\"row\">" . date('Y-m-d H:i', strtotime($reading['date'])) . "</th>";
									$output .= "<td class=\"fw-bolder text-gray-500\">" . displayReading($reading['reading1']) . "</td>";
									$output .= "<td class=\"fw-bolder text-gray-500\">" . $reading['username'] . "</td>";
									$output .= "";
									$output .= "</tr>";
									
									echo $output;
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="col-12 col-xxl-6 mb-4">
				<div class="card border-0 shadow">
					<div class="card-header border-bottom d-flex align-items-center justify-content-between">
						<h2 class="fs-5 fw-bold mb-0">Team members</h2>
						<a href="#" class="btn btn-sm btn-primary">See all</a>
					</div>
					<div class="card-body">
						<ul class="list-group list-group-flush list my--3">
							<li class="list-group-item px-0">
								<div class="row align-items-center">
									<div class="col-auto">
										<a href="#" class="avatar">
											<img class="rounded" alt="Image placeholder" src="../../assets/img/team/profile-picture-1.jpg">
										</a>
									</div>
									<div class="col-auto ms--2">
										<h4 class="h6 mb-0">
											<a href="#">Chris Wood</a>
										</h4>
										<div class="d-flex align-items-center">
											<div class="bg-success dot rounded-circle me-1"></div>
											<small>Online</small>
										</div>
									</div>
									<div class="col text-end">
										<a href="#" class="btn btn-sm btn-secondary d-inline-flex align-items-center">
											<svg class="icon icon-xxs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
											</svg>
											 Invite
										</a>
									</div>
								</div>
							</li>
							<li class="list-group-item px-0">
								<div class="row align-items-center">
									<div class="col-auto">
										<a href="#" class="avatar">
											<img class="rounded-circle" alt="Image placeholder" src="../../assets/img/team/profile-picture-2.jpg">
										</a>
									</div>
									<div class="col-auto ms--2">
										<h4 class="h6 mb-0">
											<a href="#">Jose Leos</a>
										</h4>
										<div class="d-flex align-items-center">
											<div class="bg-warning dot rounded-circle me-1"></div>
											<small>In a meeting</small>
										</div>
									</div>
									<div class="col text-end">
										<a href="#" class="btn btn-sm btn-secondary d-inline-flex align-items-center">
											<svg class="icon icon-xxs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd"></path>
											</svg>
											 Message
										</a>
									</div>
								</div>
							</li>
							<li class="list-group-item px-0">
								<div class="row align-items-center">
									<div class="col-auto">
										<a href="#" class="avatar">
											<img class="rounded-circle" alt="Image placeholder" src="../../assets/img/team/profile-picture-3.jpg">
										</a>
									</div>
									<div class="col-auto ms--2">
										<h4 class="h6 mb-0">
											<a href="#">Andrew Breakspear</a>
										</h4>
										<div class="d-flex align-items-center">
											<div class="bg-danger dot rounded-circle me-1"></div>
											<small>Offline</small>
										</div>
									</div>
									<div class="col text-end">
										<a href="#" class="btn btn-sm btn-secondary d-inline-flex align-items-center">
											<svg class="icon icon-xxs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd"></path>
											</svg>
											 Message
										</a>
									</div>
								</div>
							</li>
							<li class="list-group-item px-0">
								<div class="row align-items-center">
									<div class="col-auto">
										<a href="#" class="avatar">
											<img class="rounded-circle" alt="Image placeholder" src="../../assets/img/team/profile-picture-4.jpg">
										</a>
									</div>
									<div class="col-auto ms--2">
										<h4 class="h6 mb-0">
											<a href="#">Neil Sims</a>
										</h4>
										<div class="d-flex align-items-center">
											<div class="bg-danger dot rounded-circle me-1"></div>
											<small>Offline</small>
										</div>
									</div>
									<div class="col text-end">
										<a href="#" class="btn btn-sm btn-secondary d-inline-flex align-items-center">
											<svg class="icon icon-xxs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd"></path>
											</svg>
											 Message
										</a>
									</div>
								</div>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="col-12 col-xxl-6 mb-4">
				<div class="card border-0 shadow">
					<div class="card-header border-bottom d-flex align-items-center justify-content-between">
						<h2 class="fs-5 fw-bold mb-0">Progress track</h2>
						<a href="#" class="btn btn-sm btn-primary">See tasks</a>
					</div>
					<div class="card-body">
						<div class="row mb-4">
							<div class="col-auto">
								<svg class="icon icon-sm text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
									<path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
									<path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
								</svg>
							</div>
							<div class="col">
								<div class="progress-wrapper">
									<div class="progress-info">
										<div class="h6 mb-0">Rocket - SaaS Template</div>
										<div class="small fw-bold text-gray-500">
											<span>75 %</span>
										</div>
									</div>
									<div class="progress mb-0">
										<div class="progress-bar bg-success" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%;"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="row align-items-center mb-4">
							<div class="col-auto">
								<svg class="icon icon-sm text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
									<path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
									<path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
								</svg>
							</div>
							<div class="col">
								<div class="progress-wrapper">
									<div class="progress-info">
										<div class="h6 mb-0">Themesberg - Design System</div>
										<div class="small fw-bold text-gray-500">
											<span>60 %</span>
										</div>
									</div>
									<div class="progress mb-0">
										<div class="progress-bar bg-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="row align-items-center mb-4">
							<div class="col-auto">
								<svg class="icon icon-sm text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
									<path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
									<path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
								</svg>
							</div>
							<div class="col">
								<div class="progress-wrapper">
									<div class="progress-info">
										<div class="h6 mb-0">Homepage Design in Figma</div>
										<div class="small fw-bold text-gray-500">
											<span>45 %</span>
										</div>
									</div>
									<div class="progress mb-0">
										<div class="progress-bar bg-warning" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%;"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="row align-items-center mb-3">
							<div class="col-auto">
								<svg class="icon icon-sm text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
									<path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
									<path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
								</svg>
							</div>
							<div class="col">
								<div class="progress-wrapper">
									<div class="progress-info">
										<div class="h6 mb-0">Backend for Themesberg v2</div>
										<div class="small fw-bold text-gray-500">
											<span>34 %</span>
										</div>
									</div>
									<div class="progress mb-0">
										<div class="progress-bar bg-danger" role="progressbar" aria-valuenow="34" aria-valuemin="0" aria-valuemax="100" style="width: 34%;"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-12 col-xl-4">
		<div class="col-12 px-0 mb-4">
			<div class="card border-0 shadow">
				<div class="card-header d-flex flex-row align-items-center flex-0 border-bottom">
					<div class="d-block">
						<div class="h6 fw-normal text-gray mb-2">Total CO2e</div>
						<h2 class="h3 fw-extrabold">452</h2>
						<div class="small mt-2">
							<span class="fas fa-angle-up text-success"></span>
							<span class="text-success fw-bold">18.2%</span>
						</div>
					</div>
					<div class="d-block ms-auto">
						<div class="d-flex align-items-center text-end mb-2">
							<span class="dot rounded-circle bg-gray-800 me-2"></span>
							<span class="fw-normal small">July</span>
						</div>
						<div class="d-flex align-items-center text-end">
							<span class="dot rounded-circle bg-secondary me-2"></span>
							<span class="fw-normal small">August</span>
						</div>
					</div>
				</div>
				<div class="card-body p-2">
					<div class="ct-chart-ranking ct-golden-section ct-series-a"></div>
				</div>
			</div>
		</div>
		<div class="col-12 px-0 mb-4">
			<div class="card border-0 shadow">
				<div class="card-body">
					<div class="d-flex align-items-center justify-content-between border-bottom pb-3">
						<div>
							<div class="h6 mb-0 d-flex align-items-center">
								<svg class="icon icon-xs text-gray-500 me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd"></path>
								</svg>
								 Global Rank
							</div>
						</div>
						<div>
							<a href="#" class="d-flex align-items-center fw-bold">
								#755 
								<svg class="icon icon-xs text-gray-500 ms-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
								</svg>
							</a>
						</div>
					</div>
					<div class="d-flex align-items-center justify-content-between border-bottom py-3">
						<div>
							<div class="h6 mb-0 d-flex align-items-center">
								<svg class="icon icon-xs text-gray-500 me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"></path>
								</svg>
								 Usage Rank
							</div>
							<div class="small card-stats">
								United States 
								<svg class="icon icon-xs text-success" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
								</svg>
							</div>
						</div>
						<div>
							<a href="#" class="d-flex align-items-center fw-bold">
								#32 
								<svg class="icon icon-xs text-gray-500 ms-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
								</svg>
							</a>
						</div>
					</div>
					<div class="d-flex align-items-center justify-content-between pt-3">
						<div>
							<div class="h6 mb-0 d-flex align-items-center">
								<svg class="icon icon-xs text-gray-500 me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1H8a3 3 0 00-3 3v1.5a1.5 1.5 0 01-3 0V6z" clip-rule="evenodd"></path>
									<path d="M6 12a2 2 0 012-2h8a2 2 0 012 2v2a2 2 0 01-2 2H2h2a2 2 0 002-2v-2z"></path>
								</svg>
								 Category Rank
							</div>
							<div class="small card-stats">
								Computers Electronics > Technology 
								<svg class="icon icon-xs text-success" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
								</svg>
							</div>
						</div>
						<div>
							<a href="#" class="d-flex align-items-center fw-bold">
								#11 
								<svg class="icon icon-xs text-gray-500 ms-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
								</svg>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-12 px-0">
			<div class="card border-0 shadow">
				<div class="card-body">
					<h2 class="fs-5 fw-bold mb-1">Acquisition</h2>
					<p>Tells you where your visitors originated from, such as search engines, social networks or website referrals.</p>
					<div class="d-block">
						<div class="d-flex align-items-center me-5">
							<div class="icon-shape icon-sm icon-shape-danger rounded me-3">
								<svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11 4a1 1 0 10-2 0v4a1 1 0 102 0V7zm-3 1a1 1 0 10-2 0v3a1 1 0 102 0V8zM8 9a1 1 0 00-2 0v2a1 1 0 102 0V9z" clip-rule="evenodd"></path>
								</svg>
							</div>
							<div class="d-block">
								<label class="mb-0">Bounce Rate</label>
								<h4 class="mb-0">33.50%</h4>
							</div>
						</div>
						<div class="d-flex align-items-center pt-3">
							<div class="icon-shape icon-sm icon-shape-purple rounded me-3">
								<svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
									<path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
								</svg>
							</div>
							<div class="d-block">
								<label class="mb-0">Sessions</label>
								<h4 class="mb-0">9,567</h4>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
var data = {
	// A labels array that can contain any sort of values
	labels: ['<?php echo implode("','", array_keys($consumptionByMonth)); ?>'],
	// Our series array that contains series objects or in this case series data arrays
	series: [
		[<?php echo implode(",", $consumptionByMonth); ?>]
	]
};

new Chartist.Line('.ct-chart-sales-value', data, {
	low: 0,
	showArea: true,
	fullWidth: true,
	plugins: [
		//Chartist.plugins.tooltip()
	],
	axisX: {
		// On the x-axis start means top and end means bottom
		position: 'end',
		showGrid: true
	},
	axisY: {
		// On the y-axis start means left and end means right
		showGrid: false,
		showLabel: true
	}
});
</script>