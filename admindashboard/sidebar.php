      <div class="sidebar" data-background-color="dark">
        <div class="sidebar-logo">
          <!-- Logo Header -->
          <div class="logo-header" data-background-color="dark">
            <a href="index.php" class="logo">
              <h2 style="color:#fff;">RapidPrint</h2>
            </a>
            <div class="nav-toggle">
              <button class="btn btn-toggle toggle-sidebar">
                <i class="gg-menu-right"></i>
              </button>
              <button class="btn btn-toggle sidenav-toggler">
                <i class="gg-menu-left"></i>
              </button>
            </div>
            <button class="topbar-toggler more">
              <i class="gg-more-vertical-alt"></i>
            </button>
          </div>
          <!-- End Logo Header -->
        </div>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <ul class="nav nav-secondary">
              <li class="nav-item active">
                <a href="index.php">
                  <i class="fas fa-home"></i>
                  <p>Dashboard</p>
                  <span class="caret"></span>
                </a>

              </li>
              <li class="nav-section">
                <span class="sidebar-mini-icon">
                  <i class="fa fa-ellipsis-h"></i>
                </span>
                <h4 class="text-section">Components</h4>
              </li>

              <!-- Manage Users (Admin Only) -->
              <?php if (is_Admin()) { ?>
                <li class="nav-item">
                  <a data-bs-toggle="collapse" href="#users">
                    <i class="fas fa-layer-group"></i>
                    <p>Manage Users</p>
                    <span class="caret"></span>
                  </a>
                  <div class="collapse" id="users">
                    <ul class="nav nav-collapse">
                      <li>
                        <a href="viewusers.php">
                          <span class="sub-item">View Users</span>
                        </a>
                      </li>
                      <li>
                        <a href="register.php">
                          <span class="sub-item">Register New User</span>
                        </a>
                      </li>
                    </ul>
                  </div>
                </li>
              <?php } ?>

              <!-- View Membership (Admin Only) -->
              <?php if (is_Admin()) { ?>
                <li class="nav-item">
                  <a href="view_membership.php">
                    <i class="fas fa-id-card-alt"></i>
                    <p>View Membership</p>
                  </a>
                </li>
              <?php } ?>

              <!-- Manage Membership (Students Only) -->
              <?php if (is_Student()) { ?>
                <li class="nav-item">
                  <a data-bs-toggle="collapse" href="#membership">
                    <i class="fas fa-id-card"></i>
                    <p>Manage Membership</p>
                    <span class="caret"></span>
                  </a>
                  <div class="collapse" id="membership">
                    <ul class="nav nav-collapse">
                      <li>
                        <a href="view_membership_stud.php">
                          <span class="sub-item">View Membership</span>
                        </a>
                      </li>
                      <li>
                        <a href="apply_membership.php">
                          <span class="sub-item">Apply for Membership</span>
                        </a>
                      </li>
                    </ul>
                  </div>
                </li>
              <?php } ?>






              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#order">
                  <i class="fas fa-layer-group"></i>
                  <p>Manage orders</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="order">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="vieworders.php">
                        <span class="sub-item">View orders</span>
                      </a>
                    </li>
                    <?php
                    if (Is_Student()) {
                    ?>
                      <li>
                        <a href="order.php">
                          <span class="sub-item">New order</span>
                        </a>
                      </li>
                    <?php
                    }
                    ?>
                    <li>

                  </ul>
                </div>
              </li>
              <li class="nav-item">
                <a href="ManagePackages.php">
                  <i class="fas fa-desktop"></i>
                  <p>Manage packages</p>
                  <span class="badge badge-success"></span>
                </a>
              </li>

              <li class="nav-item">
                <a href="ManageBranches.php">
                  <i class="fas fa-desktop"></i>
                  <p>Manage Branches</p>
                  <span class="badge badge-success"></span>
                </a>
              </li>



              <!-- Edit Profile -->
              <?php if (is_Student()) { ?>
                <li class="nav-item">
                  <a href="edit_profile.php">
                    <i class="fas fa-user-edit"></i>
                    <p>Edit Profile</p>
                  </a>
                </li>
              <?php } ?>

              <?php
              if (is_Staff()) {
              ?>
                <li class="nav-item">
                  <a href="manage_invoices.php">
                    <i class="fas fa-desktop"></i>
                    <p>Manage Invoices</p>
                    <span class="badge badge-success"></span>
                  </a>
                </li>
              <?php
              }
              ?>

              <?php
              if (is_Staff()) {
              ?>
                <li class="nav-item">
                  <a href="Account_info.php"> <!-- Updated link -->
                    <i class="fas fa-desktop"></i>
                    <p>Account Info</p>
                    <span class="badge badge-success"></span>
                  </a>
                </li>
              <?php
              }


              ?>
              <?php
              if (is_Admin()) {
              ?>
                <li class="nav-item">
                  <a href="ViewRewardStaff.php"> <!-- Updated link -->
                    <i class="fas fa-desktop"></i>
                    <p>View Reward Staff</p>
                    <span class="badge badge-success"></span>
                  </a>
                </li>
              <?php
              }
              ?>

              <?php
              if (is_Admin()) {
              ?>
                <li class="nav-item">
                  <a href="View_invoices.php">
                    <i class="fas fa-desktop"></i>
                    <p>View Invoices</p>
                    <span class="badge badge-success"></span>
                  </a>
                </li>
              <?php
              }
              ?>


              <li class="nav-item">
                <a href="../../documentation/index.html">
                  <i class="fas fa-file"></i>
                  <p>Documentation</p>
                  <span class="badge badge-secondary">1</span>
                </a>
              </li>

            </ul>
          </div>
        </div>
      </div>