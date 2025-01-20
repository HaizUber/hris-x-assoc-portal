<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Leave</title>
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https:////cdn.datatables.net/2.2.1/css/dataTables.dataTables.min.css">

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
    /* Container for the table */
    .table-container {
        width: 90%;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow-x: auto; /* Allows horizontal scrolling if the table overflows */
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
            margin-bottom: 10px;
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
                <option value="F">Non-Fractional</option>
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
        $query = $this->associates_db->get();

        // Get the list of empApprovingOfficer IDs
        $approvingEmployeeIDs = array_column($query->result_array(), 'empApprovingOfficer');
    }

    // Filter the leaveRecords array based on the approvingEmployeeIDs
    $userLeaveRecords = array_filter($leaveRecords, function($leave) use ($approvingEmployeeIDs) {
        return in_array($leave['empID'], $approvingEmployeeIDs);
    });

    // Sort the filtered leaveRecords array
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

<div class="table-container">
    <table id="leaveTable" class="display">
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
                <th>Medical Certificate</th>
                <th>Actions</th> 
            </tr>
        </thead>
        <tbody id="leaveTableBody">
            <?php if (!empty($userLeaveRecords)) : ?>
                <?php foreach ($userLeaveRecords as $leave) : ?>
                    <tr class="status-row" data-status="<?= $leave['lvaStatus']; ?>" data-date="<?= $leave['lvaDateFiled']; ?>" data-filedno="<?= $leave['lvaFiledNo']; ?>" data-filedtype="<?= $leave['lvaFiledType']; ?>">
                        <td><?= $leave['lvaFiledNo']; ?></td>
                        <td><?= $leave['empID']; ?></td>
                        <td><?= date('Y-m-d', strtotime($leave['lvaDateFiled'])); ?></td> <!-- Ensure date is in YYYY-MM-DD format -->
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
                            <?php if (!empty($leave['lvaMedCert'])) : ?>
                                <a href="/das/uploads/medcert/<?= $leave['lvaMedCert']; ?>" target="_blank" class="med-cert-link">
                                    View Certificate
                                </a>
                            <?php else : ?>
                                No Certificate
                            <?php endif; ?>
                        </td>
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
</div>

<script>
    // Function to approve leave
    function approveLeave(fileNo) {
    console.log('approveLeave function called'); // Check if the function is being triggered

    const comment = prompt('Please provide a comment for approving this leave:');
    console.log('Prompt displayed for comment');
    
    if (comment) {
        console.log('Comment entered:', comment); // Log the comment entered by the user
        fetch('approveLeave/' + fileNo, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                comment: comment, // Send the comment
            }),
        })
        .then(response => {
            console.log('Received response:', response); // Log the response received
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data); // Log the received data

            if (data.status === 'success') {
                alert('Leave request has been approved.');
                console.log('Leave request approved successfully for filedNo:', fileNo);

                const row = document.querySelector(`tr[data-filedno="${fileNo}"]`);
                if (row) {
                    const statusCell = row.querySelector('.status');
                    const approveButton = row.querySelector('.approve-btn'); 
                    const disapproveButton = row.querySelector('.disapprove-btn'); 

                    statusCell.textContent = 'APPROVED';
                    statusCell.classList.remove('pending');
                    statusCell.classList.add('approved');

                    if (approveButton) {
                        approveButton.style.display = 'none';
                        console.log('Approved button hidden for filedNo:', fileNo);
                    }
                    if (disapproveButton) {
                        disapproveButton.style.display = 'none'; 
                        console.log('Disapprove button hidden for filedNo:', fileNo);
                    }
                }
            } else {
                alert('Failed to approve leave request. Please try again.');
                console.log('Approval failed for filedNo:', fileNo);
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
            console.error('Error during approveLeave fetch request for filedNo:', fileNo, error);
        });
    } else {
        console.log('User did not provide a comment for approval.');
    }
}

    function disapproveLeave(fileNo) {
        const comment = prompt('Please provide a comment for disapproving this leave:');

        if (comment) {
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
                            disapproveButton.style.display = 'none'; 
                        }
                        if (approveButton) {
                            approveButton.style.display = 'none'; 
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
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let table = new DataTable('#leaveTable', {
        pageLength: 10,
        responsive: true,
        order: [
            [7, 'asc'],  // Sort by status: PENDING > APPROVED > DISAPPROVED
            [2, 'desc'], // Then, sort by Date Filed (latest to oldest)
        ],
        columnDefs: [
            {
                targets: 7, // The Status column
                orderData: [7, 2], // First, sort by status, then by Date Filed
                render: function(data, type, row) {
                    const statusText = data.replace(/<[^>]+>/g, '').trim();

                    let statusValue, statusClass;
                    if (statusText === 'PENDING') {
                        statusValue = 0;
                        statusClass = 'pending'; // Class for styling
                    } else if (statusText === 'APPROVED') {
                        statusValue = 1;
                        statusClass = 'approved';
                    } else if (statusText === 'DISAPPROVED') {
                        statusValue = 2;
                        statusClass = 'disapproved'; 
                    } else {
                        statusValue = 3;  // Default for unknown statuses
                        statusClass = '';
                    }

                    if (type === 'sort') {
                        return statusValue;
                    }

                    return `<span class="status ${statusClass}">${statusText}</span>`;
                }
            },
            {
                targets: 2, // The Date Filed column 
                render: function(data, type, row) {
                    const date = new Date(data);
                    return date.toLocaleDateString('en-GB');
                },
                type: 'date'
            }
        ],
    });

    function applyFilters() {
        const status = document.getElementById('statusDropdown').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const fractionalLeave = document.getElementById('fractionalDropdown').value;

        // Apply status filter
        if (status !== 'all') {
            const statusRegex = `^${status}$`; // Create a regex that matches the exact status
            table.column(7).search(statusRegex, true, false).draw();  
        } else {
            table.column(7).search('').draw();
        }

        // Apply date range filter
        if (startDate && endDate) {
            const startDateObj = new Date(startDate);
            const endDateObj = new Date(endDate);

            // Convert dates to string format for searching (e.g., 'YYYY-MM-DD')
            const formattedStartDate = startDateObj.toISOString().split('T')[0];
            const formattedEndDate = endDateObj.toISOString().split('T')[0];

            // Apply the date range filter by modifying the column search value
            table.column(2).search(function(settings, data, dataIndex) {
                const rowDate = new Date(data[2]);
                const rowDateStr = rowDate.toISOString().split('T')[0]; // Convert row date to string 'YYYY-MM-DD'

                // Check if the row date falls within the selected range
                return rowDateStr >= formattedStartDate && rowDateStr <= formattedEndDate;
            }).draw();
        } else {
            // If no date range is selected, clear the date range search
            table.column(2).search('').draw();
        }

        // Apply fractional leave filter
        if (fractionalLeave !== 'all') {
            if (fractionalLeave === 'Both') {
                // Show both 'NF' and 'F' records
                table.column(11).search('NF|F', true, false).draw(); 
            } else if (fractionalLeave === 'NF') {
                // Show only 'NF' records
                table.column(11).search('^NF$', true, false).draw(); 
            } else if (fractionalLeave === 'F') {
                // Show only 'F' records
                table.column(11).search('^F$', true, false).draw();  
            }
        } else {
            table.column(11).search('').draw();  // Clear the search when 'all' is selected
        }
    }

    // Listen for changes in the filters
    document.getElementById('statusDropdown').addEventListener('change', applyFilters);
    document.getElementById('startDate').addEventListener('change', applyFilters);
    document.getElementById('endDate').addEventListener('change', applyFilters);
    document.getElementById('fractionalDropdown').addEventListener('change', applyFilters);

    // Clear filters button
    document.getElementById('clearFiltersBtn').addEventListener('click', function() {
        document.getElementById('statusDropdown').value = 'all';
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('fractionalDropdown').value = 'all';

        // Clear the search filters and redraw the table
        table.search('').columns().search('').draw();
    });

});
</script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.2.1/js/dataTables.min.js"></script>
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>
</html>
