<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link" href="index.php">
                <i class="bi bi-house-door"></i> <span>Home</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-toggle="collapse" href="#antibiotics-nav" role="button">
                <i class="bi bi-capsule"></i> <span>Antibiotics</span> <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="antibiotics-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="../super-admin/pages-add-antibiotic.php">
                        <i class="bi bi-circle"></i> <span>Add Antibiotic</span>
                    </a>
                </li>
                <li>
                    <a href="../super-admin/pages-manage-antibiotic.php">
                        <i class="bi bi-circle"></i> <span>Manage Antibiotic</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-toggle="collapse" href="#analyze-nav" role="button">
                <i class="bi bi-bar-chart"></i> <span>Antibiotics Analyze</span> <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="analyze-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="../super-admin/pages-release-summery.php">
                        <i class="bi bi-circle"></i> <span>Antibiotic Release With Chart</span>
                    </a>
                </li>
            </ul>
        </li>


        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-toggle="collapse" href="#analyze-nav2" role="button">
                <i class="bi bi-bar-chart"></i> <span>Ward Wise Analyze</span> <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="analyze-nav2" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="../super-admin/pages-release-summery-ward-wise.php">
                        <i class="bi bi-circle"></i> <span>Antibiotic Release</span>
                    </a>
                </li>
                <li>
                    <a href="../super-admin/pages-release-summery-charts-ward-wise.php">
                        <i class="bi bi-circle"></i> <span>Ward Wise Charts</span>
                    </a>
                </li>

                <li>
                    <a href="../super-admin/pages-release-summery-charts-ward-wise-antibiotic-single.php">
                        <i class="bi bi-circle"></i> <span>Ward Wise Charts Antibiotic Single</span>
                    </a>
                </li>

                <li>
                    <a href="../super-admin/pages-release-summery-charts-ward-wise-single.php">
                        <i class="bi bi-circle"></i> <span>Ward Wise Charts Single</span>
                    </a>
                </li>

                <li>
                    <a href="../super-admin/pages-release-summery-chart-ward-wise-category.php">
                        <i class="bi bi-circle"></i> <span>Ward Wise Category Charts</span>
                    </a>
                </li>
            </ul>
        </li>


        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-toggle="collapse" href="#wards-nav" role="button">
                <i class="bi bi-building-fill-gear"></i> <span>Wards</span> <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="wards-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="../super-admin/pages-add-new-ward.php">
                        <i class="bi bi-circle"></i> <span>Add New Wards</span>
                    </a>
                </li>
                <li>
                    <a href="../super-admin/pages-manage-wards.php">
                        <i class="bi bi-circle"></i> <span>Manage Wards</span>
                    </a>
                </li>
            </ul>
        </li>


        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-toggle="collapse" href="#stock-nav" role="button">
                <i class="bi bi-backpack4"></i> <span>Stock</span> <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="stock-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="../super-admin/pages-manage-stock.php">
                        <i class="bi bi-circle"></i> <span>Dispensing Stock (Main)</span>
                    </a>
                </li>

                <li>
                    <a href="../super-admin/pages-manage-return-stock.php">
                        <i class="bi bi-circle"></i> <span>Return Stock</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="book-transactions.php">
                <i class="bi bi-book"></i> <span>Books Numbers</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-toggle="collapse" href="#users-nav" role="button">
                <i class="bi bi-people"></i> <span>Users</span> <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="users-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="add-user.php">
                        <i class="bi bi-circle"></i> <span>Add User</span>
                    </a>
                </li>
                <li>
                    <a href="manage-admins.php">
                        <i class="bi bi-circle"></i> <span>Manage Users</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-toggle="collapse" href="#admins-nav" role="button">
                <i class="bi bi-person-gear"></i> <span>Admins</span> <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="admins-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="add-admin.php">
                        <i class="bi bi-circle"></i> <span>Add Admin</span>
                    </a>
                </li>
                <li>
                    <a href="manage-super-admins.php">
                        <i class="bi bi-circle"></i> <span>Manage Admin</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-clipboard-data"></i></i></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>

            <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li>
                    <a href="pages-release-details.php">
                        <i class="bi bi-circle"></i><span>Dispensing Details</span>
                    </a>
                </li>

                <li>
                    <a href="pages-return-details.php">
                        <i class="bi bi-circle"></i><span>Return Details</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="user-profile.php">
                <i class="bi bi-person-circle"></i> <span>Profile</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="logout.php">
                <i class="bi bi-box-arrow-right"></i> <span>Log Out</span>
            </a>
        </li>

    </ul>
</aside><!-- End Sidebar -->
