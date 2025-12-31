	<header>
        @php($user = auth()->user())
				<div class="topbar d-flex align-items-center">
					<nav class="navbar navbar-expand gap-3">
						<div class="topbar-logo-header">
							<div class="">
								{{-- <img src="assets/images/logo-icon.png" class="logo-icon" alt="logo icon"> --}}
							</div>
							<div class="">
								<h4 class="logo-text">Admin Panel</h4>
							</div>
						</div>
						<div class="mobile-toggle-menu d-block d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"><i class='bx bx-menu'></i></div>
						<div class="top-menu-left d-none d-lg-block">
							<ul class="nav">
							  <li class="nav-item">
								<a class="nav-link" href="app-emailbox.html"><i class='bx bx-home-alt'></i></a>
							  </li>
							  <li class="nav-item">
								<a class="nav-link" href="app-chat-box.html"><i class='bx bx-cube'></i></a>
							  </li>
						  </ul>
						 </div>
						<div class="search-bar flex-grow-1">
							<div class="position-relative search-bar-box">
								<input type="text" class="form-control search-control" placeholder="Type to search..."> <span class="position-absolute top-50 search-show translate-middle-y"><i class='bx bx-search'></i></span>
								<span class="position-absolute top-50 search-close translate-middle-y"><i class='bx bx-x'></i></span>
							</div>
						</div>
						<div class="top-menu ms-auto">
							<ul class="navbar-nav align-items-center gap-1">

								<li class="nav-item dark-mode d-none d-sm-flex">
									<a class="nav-link dark-mode-icon" href="javascript:;"><i class='bx bx-moon'></i>
									</a>
								</li>

							</ul>
						</div>
						<div class="user-box dropdown px-3">
							<a class="d-flex align-items-center nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
								<img src="assets/images/avatars/avatar-2.png" class="user-img" alt="user avatar">
								<div class="user-info ps-3">
									<p class="user-name mb-0">{{ @$user->name ?? 'Admin' }}</p>
									<p class="designattion mb-0">{{  isAdmin() ? 'Admin' : 'Location' }}</p>
								</div>
							</a>
							<ul class="dropdown-menu dropdown-menu-end">
								{{-- <li><a class="dropdown-item" href="javascript:;"><i class="bx bx-user"></i><span>Profile</span></a>
								</li> --}}
								<li><a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="bx bx-cog"></i><span>Settings</span></a>
								</li>
								<li>
									<div class="dropdown-divider mb-0"></div>
								</li>
								<li>
                                     <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i
                            class="fa-solid fa-sign-out-alt"></i> Logout</a>
								</li>
							</ul>
						</div>
					</nav>
				</div>
			</header>
