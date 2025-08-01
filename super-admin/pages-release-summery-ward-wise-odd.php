<?php
session_start();
require_once '../includes/db-conn.php';
// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit();
}
// Fetch user details
$user_id = $_SESSION['admin_id'];
$sql = "SELECT name, email, nic, mobile,profile_picture FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Get selected start month, end month, year, ward, type, and ant_type
$selectedStartMonth = $_POST['start_month_select'] ?? date('m');
$selectedEndMonth = $_POST['end_month_select'] ?? date('m');
$selectedYear = $_POST['year_select'] ?? date('Y');
$selectedWard = $_POST['ward_select'] ?? '';
$selectedType = $_POST['type_select'] ?? ''; // Added for MSD/LP
$selectedAntType = $_POST['ant_type_select'] ?? ''; // Added for Antibiotic Type (ant_type)
$startDate = $_POST['start_date'] ?? date('Y-m-01'); // Default to the first day of the current month
$endDate = $_POST['end_date'] ?? date('Y-m-t');     // Default to the last day of the current month

// Ensure start month is not later than end month
if ($selectedStartMonth > $selectedEndMonth) {
    $temp = $selectedStartMonth;
    $selectedStartMonth = $selectedEndMonth;
    $selectedEndMonth = $temp;
}

// Fetch all wards from the database
$wardQuery = "SELECT DISTINCT ward_name FROM releases ORDER BY ward_name";
$wardStmt = $conn->prepare($wardQuery);
$wardStmt->execute();
$wardResult = $wardStmt->get_result();

// Query for antibiotic usage filtered by selected month range, year, ward, type, and ant_type
$query = "
    SELECT ward_name, antibiotic_name, dosage, type, ant_type, SUM(item_count) AS usage_count
    FROM releases
    WHERE MONTH(release_time) BETWEEN ? AND ? 
    AND YEAR(release_time) = ?
    AND (ward_name = ? OR ? = '')
    AND (type = ? OR ? = '')
    AND (ant_type = ? OR ? = '')
    GROUP BY ward_name, antibiotic_name, dosage, type, ant_type
    ORDER BY ward_name, antibiotic_name ASC, usage_count DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("iisssssss", $selectedStartMonth, $selectedEndMonth, $selectedYear, $selectedWard, $selectedWard, $selectedType, $selectedType, $selectedAntType, $selectedAntType);
$stmt->execute();
$result = $stmt->get_result();

// Query to get total usage count per ward for percentage calculation
$wardUsageQuery = "
    SELECT ward_name, SUM(item_count) AS ward_total
    FROM releases
    WHERE MONTH(release_time) BETWEEN ? AND ?
    AND YEAR(release_time) = ?
    AND (ward_name = ? OR ? = '')
    GROUP BY ward_name
";
$wardUsageStmt = $conn->prepare($wardUsageQuery);
$wardUsageStmt->bind_param("iiiss", $selectedStartMonth, $selectedEndMonth, $selectedYear, $selectedWard, $selectedWard);
$wardUsageStmt->execute();
$wardUsageResult = $wardUsageStmt->get_result();

$wardUsage = [];
while ($row = $wardUsageResult->fetch_assoc()) {
    $wardUsage[$row['ward_name']] = $row['ward_total'];
}
$stmt->close();
$wardStmt->close();
$wardUsageStmt->close();
// Get month names for display
$months = [
    '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
    '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
    '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
];
$startMonthName = $months[$selectedStartMonth];
$endMonthName = $months[$selectedEndMonth];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Antibiotic Usage - Mediq</title>
    <?php include_once("../includes/css-links-inc.php"); ?>

    <!-- Include DataTables CSS and JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <style>
        #piechart { width: 50%; height: 400px; margin: auto; }
        @media print { 
            .no-print { display: none; } 
            .page-break { page-break-before: always; }
        }
        @media only screen and (min-width: 768px) {
            .select-bar {display: flex; flex-wrap: wrap;}
        } 
        .select-bar > div {
            margin-right: 15px;
            margin-bottom: 10px;
        }
        .date-range {
            background-color: #f8f9fa; 
            padding: 10px; 
            border-radius: 5px; 
            margin-bottom: 15px;
        }
    </style>
    <script> function printPage() { window.print(); } </script>
</head>

<body>
    <?php include_once("../includes/header.php") ?>
    <?php include_once("../includes/sadmin-sidebar.php") ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Usage Details Ward Wise</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item">Pages</li>
                    <li class="breadcrumb-item active">Antibiotic Usage Details</li>
                </ol>
            </nav>
        </div>
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Antibiotic Release Details</h5>

                            <form method="POST">
                                <div class="form-row mb-3 select-bar">
                                    <div class="col-sm-3">
                                        <label for="year_select" class="col-form-label">Select Year:</label>
                                        <select name="year_select" id="year_select" class="form-select">
                                            <?php
                                            $currentYear = date('Y');
                                            for ($i = 2020; $i <= $currentYear; $i++) {
                                                echo "<option value='$i'" . ($i == $selectedYear ? ' selected' : '') . ">$i</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                  
                                    <div class="col-sm-3">
                                        <label for="start_month_select" class="col-form-label">From Month:</label>
                                        <select name="start_month_select" id="start_month_select" class="form-select">
                                            <?php
                                            foreach ($months as $monthNum => $monthName) {
                                                echo "<option value='$monthNum'" . ($monthNum == $selectedStartMonth ? ' selected' : '') . ">$monthName</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-sm-3">
                                        <label for="end_month_select" class="col-form-label">To Month:</label>
                                        <select name="end_month_select" id="end_month_select" class="form-select">
                                            <?php
                                            foreach ($months as $monthNum => $monthName) {
                                                echo "<option value='$monthNum'" . ($monthNum == $selectedEndMonth ? ' selected' : '') . ">$monthName</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="d-flex">
                                    <div class="col-sm-3">
                                        <label for="start_date" class="col-form-label">Select Start Date:</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo $_POST['start_date'] ?? ''; ?>">
                                    </div>

                                    <div class="col-sm-3">
                                        <label for="end_date" class="col-form-label">Select End Date:</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo $_POST['end_date'] ?? ''; ?>">
                                    </div>
                                </div>
                                  
                                    <div class="col-sm-3">
                                        <label for="ward_select" class="col-form-label">Select Ward:</label>
                                        <select name="ward_select" id="ward_select" class="form-select">
                                            <option value="">All Wards</option>
                                            <?php
                                            // Reset the ward result pointer to the beginning
                                            $wardResult->data_seek(0);
                                            while ($wardRow = $wardResult->fetch_assoc()) {
                                                $wardName = $wardRow['ward_name'];
                                                echo "<option value='$wardName'" . ($wardName == $selectedWard ? ' selected' : '') . ">$wardName</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>                                 
                                    <!-- Added MSD/LP Filter -->
                                    <div class="col-sm-3">
                                        <label for="type_select" class="col-form-label">Select Stock:</label>
                                        <select name="type_select" id="type_select" class="form-select">
                                            <option value="">All Types</option>
                                            <option value="msd" <?php echo ($selectedType == 'msd') ? 'selected' : ''; ?>>MSD</option>
                                            <option value="lp" <?php echo ($selectedType == 'lp') ? 'selected' : ''; ?>>LP</option>
                                        </select>
                                    </div>
                                
                                    <div class="col-sm-3">
                                       <label for="ant_type_select" class="col-form-label">Select Route:</label>
                                       <select name="ant_type_select" id="ant_type_select" class="form-select">
                                        <option value="">All Types</option>
                                        <option value="oral" <?php echo ($selectedAntType == 'oral') ? 'selected' : ''; ?>>Oral</option>
                                        <option value="intravenous" <?php echo ($selectedAntType == 'intravenous') ? 'selected' : ''; ?>>Intravenous</option>
                                        <option value="topical" <?php echo ($selectedAntType == 'topical') ? 'selected' : ''; ?>>Topical</option>
                                        <option value="other" <?php echo ($selectedAntType == 'other') ? 'selected' : ''; ?>>Other</option>
                                       </select>
                                    </div>

                                    <div class="col-sm-7">
                                        <button type="submit" class="btn btn-primary mt-4">Filter</button>
                                        <button type="button" class="btn btn-danger mt-4 ml-2 print-btn no-print" onclick="printPage()">Print Report</button>
                                        <button type="button" class="btn btn-success mt-4 ml-2" onclick="exportTableToExcel()">Export to Excel</button>
                                        <button type="button" class="btn btn-warning mt-4 ml-2" onclick="exportTableToPDF()">Export to PDF</button>
                                    </div>
                                </div>
                            </form>                           
                            <!-- Date Range Display -->
                            <div class="date-range">
                                <h6>Report Period: <?php echo $startMonthName . ' to ' . $endMonthName . ' ' . $selectedYear; ?></h6>
                                <?php if (!empty($selectedWard)): ?>
                                    <h6>Ward: <?php echo $selectedWard; ?></h6>
                                <?php else: ?>
                                    <h6>Ward: All Wards</h6>
                                <?php endif; ?>
                                
                                <?php if (!empty($selectedType)): ?>
                                    <h6>Stock Type: <?php echo strtoupper($selectedType); ?></h6>
                                <?php endif; ?>
                                
                                <?php if (!empty($selectedAntType)): ?>
                                    <h6>Route: <?php echo ucfirst($selectedAntType); ?></h6>
                                <?php endif; ?>
                            </div>
                        </div>                       
                        <!-- DataTable -->
                        <div style="padding: 15px;">
                          <table class="table datatable">
                            <thead class="align-middle text-center">
                                <tr>
                                    <th>#</th>
                                    <th>Ward Name</th>
                                    <th>Antibiotic Name</th>
                                    <th>Dosage</th>
                                    <th>Usage Count</th>
                                    <th>Percentage (%)</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <?php 
                                if ($result->num_rows > 0) {
                                    $rowNumber = 1;
                                    $previousWard = "";
                                    while ($row = $result->fetch_assoc()) {
                                        $wardName = $row['ward_name'];
                                        $totalUsageInWard = $wardUsage[$wardName];
                                        $percentage = round(($row['usage_count'] / $totalUsageInWard) * 100, 2);
                                        
                                        // Group by ward name
                                        if ($wardName != $previousWard) {
                                            // Insert a row for the ward name (row with colspan)
                                            echo "<tr><td colspan='6' class='text-center card-title' style='background-color: #f8f9fa; font-weight: bold;'>$wardName</td></tr>";
                                            $previousWard = $wardName;
                                        }

                                        // Regular data row
                                        echo "<tr>";
                                        echo "<td class='text-center'>{$rowNumber}</td>";
                                        echo "<td class='text-center'>{$row['ward_name']}</td>";
                                        echo "<td class='text-center'>{$row['antibiotic_name']}</td>";
                                        echo "<td class='text-center'>{$row['dosage']}</td>";
                                        echo "<td class='text-center'>{$row['usage_count']}</td>";
                                        echo "<td class='text-center'>{$percentage}%</td>";
                                        echo "</tr>";
                                        $rowNumber++;
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>No data available for the selected date range, ward, and type</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include_once("../includes/js-links-inc.php") ?>
    <?php include_once("../includes/footer.php") ?>
    <script type="text/javascript">
        function exportTableToExcel() {
            // Get the table element
            let table = document.querySelector(".datatable");
            
            // Create filename with date range
            let startMonth = document.getElementById('start_month_select').options[document.getElementById('start_month_select').selectedIndex].text;
            let endMonth = document.getElementById('end_month_select').options[document.getElementById('end_month_select').selectedIndex].text;
            let year = document.getElementById('year_select').value;
            let filename = "Antibiotic_Usage_" + startMonth + "_to_" + endMonth + "_" + year + ".xlsx";
            
            // Convert the table to a worksheet
            let workbook = XLSX.utils.book_new();
            let worksheet = XLSX.utils.table_to_sheet(table);
            
            // Add the worksheet to the workbook
            XLSX.utils.book_append_sheet(workbook, worksheet, "Antibiotic_Usage");

            // Save the file
            XLSX.writeFile(workbook, filename);
        }
    </script>
    <script>
        function exportTableToPDF() {
            const { jsPDF } = window.jspdf;
            let doc = new jsPDF();

            // Get date range for title
            let startMonth = document.getElementById('start_month_select').options[document.getElementById('start_month_select').selectedIndex].text;
            let endMonth = document.getElementById('end_month_select').options[document.getElementById('end_month_select').selectedIndex].text;
            let year = document.getElementById('year_select').value;
            
            // Title
            doc.setFont("helvetica", "bold");
            doc.setFontSize(16);
            doc.text("Antibiotic Usage Report", 105, 10, { align: "center" });
            
            // Subtitle with date range
            doc.setFontSize(12);
            doc.text(startMonth + " to " + endMonth + " " + year, 105, 18, { align: "center" });

            // Get table data
            let table = document.querySelector(".datatable");
            let data = [];
            let headers = [];

            // Get headers
            let headerCells = table.querySelectorAll("thead tr th");
            headerCells.forEach(header => headers.push(header.innerText));
            
            // Get rows
            let rows = table.querySelectorAll("tbody tr");
            rows.forEach(row => {
                let rowData = [];
                let cells = row.querySelectorAll("td");
                cells.forEach(cell => rowData.push(cell.innerText));
                data.push(rowData);
            });

            // Add table to PDF
            doc.autoTable({
                head: [headers],
                body: data,
                startY: 25,
                theme: "striped",
                styles: { fontSize: 10 },
                headStyles: { fillColor: [44, 62, 80], textColor: 255, fontStyle: "bold" },
                alternateRowStyles: { fillColor: [240, 240, 240] },
            });

            // Create filename with date range
            let filename = "Antibiotic_Usage_" + startMonth + "_to_" + endMonth + "_" + year + ".pdf";
            
            // Save PDF
            doc.save(filename);
        }
        
        // Add validation to ensure end month is not before start month
        document.addEventListener('DOMContentLoaded', function() {
            const startMonthSelect = document.getElementById('start_month_select');
            const endMonthSelect = document.getElementById('end_month_select');
            
            function validateMonths() {
                if (parseInt(startMonthSelect.value) > parseInt(endMonthSelect.value)) {
                    alert('Start month cannot be after end month. Adjusting end month.');
                    endMonthSelect.value = startMonthSelect.value;
                }
            }
            
            startMonthSelect.addEventListener('change', validateMonths);
            endMonthSelect.addEventListener('change', validateMonths);
        });
    </script>
</body>
</html>