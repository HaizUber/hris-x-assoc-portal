<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filed Leave</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }
        h1 {
            text-align: center;
            margin: 20px 0;
            font-size: 2em;
            color: #4CAF50;
        }
        .filters {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin: 20px auto;
            width: 80%;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .dropdown, .date-picker {
            padding: 10px;
            font-size: 1em;
            width: 200px;
            cursor: pointer;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
            text-align: center;
        }

        .date-range {
            display: flex;
            gap: 10px;
        }

        .date-range input {
            width: 48%;
        }

        /* Status styling */
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
        }
        .approved {
            background-color: #4CAF50; /* Green for approved */
        }
        .pending {
            background-color: #ff9800; /* Orange for pending */
        }
        .disapproved {
            background-color: #f44336; /* Red for disapproved */
        }

        table {
            margin: 20px auto;
            border-collapse: collapse;
            width: 90%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        td {
            font-size: 0.9em;
        }
        td:first-child, th:first-child {
            text-align: center;
        }

        @media (max-width: 768px) {
            table {
                font-size: 0.85em;
            }
            th, td {
                padding: 8px 10px;
            }
        }

        .cancel-btn {
            background-color: #f44336; /* Red color */
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .cancel-btn:hover {
            background-color: #d32f2f;
        }

        .clear-btn {
            background-color: #2196F3; /* Blue color */
            color: white;
            padding: 3px 6px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 50px;
        }
        .clear-btn:hover {
            background-color: #1976D2;
        }
    </style>
</head>
<body>
    <h1>Filed Leave</h1>

    <!-- Filters Section -->
    <div class="filters">
        <!-- Status Filter -->
        <div class="filter-group">
            <label for="statusDropdown">Filter by Status</label>
            <select class="dropdown" id="statusDropdown">
                <option value="all">All Statuses</option>
                <option value="PENDING">Pending</option>
                <option value="APPROVED">Approved</option>
                <option value="DISAPPROVED">Disapproved</option>
            </select>
        </div>

        <!-- Date Range Filter -->
        <div class="filter-group">
            <label for="startDate">Date Range</label>
            <div class="date-range">
                <input type="date" id="startDate" class="date-picker" />
                <input type="date" id="endDate" class="date-picker" />
            </div>
        </div>

        <!-- Fractional Leave Dropdown -->
        <div class="filter-group">
            <label for="fractionalDropdown">Fractional Leave</label>
            <select class="dropdown" id="fractionalDropdown">
                <option value="all">Both</option>
                <option value="NF">Fractional</option>
                <option value="nonNF">Non-Fractional</option>
            </select>
        </div>
    <div>
        <!-- Clear Filters Button -->
        <button class="clear-btn" id="clearFiltersBtn">Clear Filters</button>
    </div>
    </div>

    <?php
        // Get the employee_id from the session
        $employee_id = $this->session->userdata('employee_id');

        // Filter the leaveBalance array to only include records for the logged-in user
        $userLeaveBalance = array_filter($leaveBalance, function($leave) use ($employee_id) {
            return $leave['empID'] === $employee_id;
        });

        // Sort the filtered leaveBalance array
        usort($userLeaveBalance, function($a, $b) {
            if ($a['lvaStatus'] === 'PENDING' && $b['lvaStatus'] !== 'PENDING') {
                return -1; // $a comes before $b
            }
            if ($a['lvaStatus'] !== 'PENDING' && $b['lvaStatus'] === 'PENDING') {
                return 1; // $b comes before $a
            }

            return strtotime($b['lvaDateFiled']) - strtotime($a['lvaDateFiled']);
        });
    ?>

    <table id="leaveTable">
        <thead>
            <tr>
                <th>Filed No</th>
                <th>Employee ID</th>
                <th>Date Filed</th>
                <th>Date From</th>
                <th>Date To</th>
                <th>Type</th>
                <th>Reason</th>
                <th>Status</th>
                <th>School Year</th>
                <th>Approved By</th>
                <th>Comments</th>
                <th>Filed Type</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Actions</th> <!-- New Actions Column -->
            </tr>
        </thead>
        <tbody id="leaveTableBody">
            <?php if (!empty($userLeaveBalance)) : ?>
                <?php foreach ($userLeaveBalance as $leave) : ?>
                    <tr class="status-row" data-status="<?= $leave['lvaStatus']; ?>" data-date="<?= $leave['lvaDateFiled']; ?>" data-filedno="<?= $leave['lvaFiledNo']; ?>" data-filedtype="<?= $leave['lvaFiledType']; ?>">
                        <td><?= $leave['lvaFiledNo']; ?></td>
                        <td><?= $leave['empID']; ?></td>
                        <td><?= $leave['lvaDateFiled']; ?></td>
                        <td><?= $leave['lvaDateFrom']; ?></td>
                        <td><?= $leave['lvaDateTo']; ?></td>
                        <td><?= $leave['lvaType']; ?></td>
                        <td><?= $leave['lvaReason']; ?></td>
                        <td>
                            <?php
                            $statusClass = '';
                            switch($leave['lvaStatus']) {
                                case 'APPROVED':
                                    $statusClass = 'approved';
                                    break;
                                case 'PENDING':
                                    $statusClass = 'pending';
                                    break;
                                case 'DISAPPROVED':
                                    $statusClass = 'disapproved';
                                    break;
                                default:
                                    $statusClass = ''; // Handle any undefined status
                            }
                            ?>
                            <span class="status <?= $statusClass; ?>"><?= $leave['lvaStatus']; ?></span>
                        </td>
                        <td><?= $leave['lvaSchoolYear'] ?? 'NULL'; ?></td>
                        <td><?= $leave['lvaApprovedBy'] ?? 'NULL'; ?></td>
                        <td><?= $leave['lvaComments'] ?? 'NULL'; ?></td>
                        <td><?= $leave['lvaFiledType']; ?></td>
                        <td><?= $leave['lvaStartTime']; ?></td>
                        <td><?= $leave['lvaEndTime']; ?></td>
                        <td>
                            <?php if ($leave['lvaStatus'] === 'PENDING') : ?>
                                <button class="cancel-btn" onclick="cancelLeave('<?= $leave['lvaFiledNo']; ?>')">Cancel</button>
                            <?php else : ?>
                                <span>N/A</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="15" style="text-align: center; font-style: italic;">No records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
        const statusDropdown = document.getElementById('statusDropdown');
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');
        const tableBody = document.getElementById('leaveTableBody');
        const fractionalDropdown = document.getElementById('fractionalDropdown');
        const clearFiltersBtn = document.getElementById('clearFiltersBtn');

        statusDropdown.addEventListener('change', filterTable);
        startDateInput.addEventListener('input', filterTable);
        endDateInput.addEventListener('input', filterTable);
        fractionalDropdown.addEventListener('change', filterTable);
        clearFiltersBtn.addEventListener('click', clearFilters);

        function filterTable() {
            const selectedStatus = statusDropdown.value;
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;
            const fractionalType = fractionalDropdown.value;

            const rows = tableBody.querySelectorAll('tr');

            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                const rowDate = row.getAttribute('data-date');
                const rowFiledType = row.getAttribute('data-filedtype');

                const statusMatches = selectedStatus === 'all' || rowStatus === selectedStatus;
                const dateMatches = isDateInRange(rowDate, startDate, endDate);
                const fractionalMatches = fractionalType === 'all' || 
                    (fractionalType === 'NF' && rowFiledType === 'NF') || 
                    (fractionalType === 'nonNF' && rowFiledType !== 'NF');

                if (statusMatches && dateMatches && fractionalMatches) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function isDateInRange(dateStr, startDate, endDate) {
            const date = new Date(dateStr);
            const start = new Date(startDate);
            const end = new Date(endDate);

            return (!startDate || date >= start) && (!endDate || date <= end);
        }

        function clearFilters() {
            // Reset filter inputs
            statusDropdown.value = 'all';
            startDateInput.value = '';
            endDateInput.value = '';
            fractionalDropdown.value = 'all';

            // Display all rows
            const rows = tableBody.querySelectorAll('tr');
            rows.forEach(row => {
                row.style.display = '';
            });
        }

        function cancelLeave(fileNo) {
            if (confirm('Are you sure you want to cancel this leave request?')) {
                fetch('cancelLeave/' + fileNo, {
                    method: 'POST',
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Leave request has been canceled.');
                        const row = document.querySelector(`tr[data-filedno="${fileNo}"]`);
                        if (row) {
                            row.style.display = 'none';
                        }
                    } else {
                        alert('Failed to cancel leave request. Please try again.');
                    }
                })
                .catch(error => {
                    alert('An error occurred. Please try again.');
                });
            }
        }
    </script>
</body>
</html>