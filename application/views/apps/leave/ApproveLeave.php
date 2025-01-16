<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Leave</title>
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

        .approve-btn, .disapprove-btn {
            background-color: #4CAF50; /* Green color */
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .approve-btn:hover {
            background-color: #388E3C;
        }

        .disapprove-btn {
            background-color: #f44336; /* Red color */
        }

        .disapprove-btn:hover {
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
    <h1>Approve Leave</h1>

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
    $this->associates_db = $this->load->database('associates', TRUE);

    // Query the tblApprovingOfficer table to get employees that the current user is the approving officer for
    $this->associates_db->select('empID');
    $this->associates_db->from('tblApprovingOfficer');
    $this->associates_db->where('empApprovingOfficer', $employee_id);
    $query = $this->associates_db->get();

    // Get the list of empIDs that the logged-in user can approve
    $approvingEmployeeIDs = array_column($query->result_array(), 'empID');

    // If there are no records, check if the logged-in user is listed as empApprovingOfficer
    if (empty($approvingEmployeeIDs)) {
        $this->associates_db->select('empApprovingOfficer');
        $this->associates_db->from('tblApprovingOfficer');
        $this->associates_db->where('empID', $employee_id);
        $query = $this->dassociates_db->get();

        // Get the list of empApprovingOfficer IDs
        $approvingEmployeeIDs = array_column($query->result_array(), 'empApprovingOfficer');
    }

    // Filter the leaveBalance array based on the approvingEmployeeIDs
    $userLeaveRecords = array_filter($leaveRecords, function($leave) use ($approvingEmployeeIDs) {
        return in_array($leave['empID'], $approvingEmployeeIDs);
    });

    // Sort the filtered leaveBalance array
    usort($userLeaveRecords, function($a, $b) {
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
            <?php if (!empty($userLeaveRecords)) : ?>
                <?php foreach ($userLeaveRecords as $leave) : ?>
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
                            <button class="approve-btn" onclick="approveLeave('<?= $leave['lvaFiledNo']; ?>')">Approve</button>
                            <button class="disapprove-btn" onclick="disapproveLeave('<?= $leave['lvaFiledNo']; ?>')">Disapprove</button>
                        <?php else : ?>
                            <span>
                                <?= $leave['lvaStatus'] === 'APPROVED' ? 'Already Approved' : 'Already Disapproved'; ?>
                            </span>
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

    // Function to filter the table based on selected status, date range, and fractional type
function filterTable() {
    const status = statusDropdown.value.toUpperCase();
    const startDate = startDateInput.value;
    const endDate = endDateInput.value;
    const fractionalType = fractionalDropdown.value;

    const rows = tableBody.getElementsByClassName('status-row');
    Array.from(rows).forEach(row => {
        const rowStatus = row.dataset.status.toUpperCase();
        const rowDate = row.dataset.date;
        const rowFiledType = row.dataset.filedtype; // Get the fractional type attribute
        let showRow = true;

        // Filter by status
        if (status !== 'ALL' && rowStatus !== status) {
            showRow = false;
        }

        // Filter by date range if both dates are provided
        if (startDate && endDate) {
            const rowDateFrom = new Date(rowDate);
            const startDateRange = new Date(startDate);
            const endDateRange = new Date(endDate);

            if (rowDateFrom < startDateRange || rowDateFrom > endDateRange) {
                showRow = false;
            }
        }

        // Filter by fractional type
        if (fractionalType !== 'all') {
            if (
                (fractionalType === 'NF' && rowFiledType !== 'NF') || 
                (fractionalType === 'nonNF' && rowFiledType === 'NF')
            ) {
                showRow = false;
            }
        }

        // Set row visibility based on filters
        row.style.display = showRow ? '' : 'none';
    });
}


    // Function to clear the filters
    clearFiltersBtn.addEventListener('click', () => {
        statusDropdown.value = 'all';
        startDateInput.value = '';
        endDateInput.value = '';
        filterTable();
    });

// Function to approve leave
function approveLeave(fileNo) {
    if (confirm('Are you sure you want to approve this leave request?')) {
        fetch('approveLeave/' + fileNo, {
            method: 'POST',
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Leave request has been approved.');
                const row = document.querySelector(`tr[data-filedno="${fileNo}"]`);
                if (row) {
                    const statusCell = row.querySelector('.status');
                    const approveButton = row.querySelector('.approve-btn'); // Get the approve button
                    const disapproveButton = row.querySelector('.disapprove-btn'); // Get the disapprove button

                    // Update status text and class
                    statusCell.textContent = 'APPROVED';
                    statusCell.classList.remove('pending');
                    statusCell.classList.add('approved');

                    // Remove the approve and disapprove buttons
                    if (approveButton) {
                        approveButton.style.display = 'none'; // Hide the approve button
                    }
                    if (disapproveButton) {
                        disapproveButton.style.display = 'none'; // Hide the disapprove button
                    }
                }
            } else {
                alert('Failed to approve leave request. Please try again.');
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
        });
    }
}

    function disapproveLeave(fileNo) {
    const comment = prompt('Please provide a comment for disapproving this leave:');

    if (comment) {
        console.log('Sending comment:', comment);
        fetch('disapproveLeave/' + fileNo, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                comment: comment, // Send the comment
            }),
        })
        .then(response => response.json())
        .then(data => {
            console.log('Response from server:', data); // Debugging line

            if (data.status === 'success') {
                alert('Leave request has been disapproved.');
                const row = document.querySelector(`tr[data-filedno="${fileNo}"]`);
                if (row) {
                    const statusCell = row.querySelector('.status');
                    const disapproveButton = row.querySelector('.disapprove-btn'); // Get the disapprove button
                    const approveButton = row.querySelector('.approve-btn'); // Get the approve button

                    // Update status text and class
                    statusCell.textContent = 'DISAPPROVED';
                    statusCell.classList.remove('pending');
                    statusCell.classList.add('disapproved');

                    // Hide the disapprove and approve buttons
                    if (disapproveButton) {
                        disapproveButton.style.display = 'none'; // Hide the disapprove button
                    }
                    if (approveButton) {
                        approveButton.style.display = 'none'; // Hide the approve button
                    }
                }
            } else {
                alert('Failed to disapprove leave request. Please try again.');
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
        });
    }
}

    statusDropdown.addEventListener('change', filterTable);
    startDateInput.addEventListener('change', filterTable);
    endDateInput.addEventListener('change', filterTable);
    fractionalDropdown.addEventListener('change', filterTable);
</script>

</body>
</html>
