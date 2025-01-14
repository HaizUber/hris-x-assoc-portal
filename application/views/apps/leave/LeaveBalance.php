<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Balance</title>
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

        @media (max-width: 768px) {
            table {
                font-size: 0.85em;
            }
            th, td {
                padding: 8px 10px;
            }
        }
    </style>
</head>
<body>
    <h1>Leave Balance</h1>
    <table>
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
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($leaveBalance)) : ?>
                <?php foreach ($leaveBalance as $leave) : ?>
                    <tr>
                        <td><?= $leave['lvaFiledNo']; ?></td>
                        <td><?= $leave['empID']; ?></td>
                        <td><?= $leave['lvaDateFiled']; ?></td>
                        <td><?= $leave['lvaDateFrom']; ?></td>
                        <td><?= $leave['lvaDateTo']; ?></td>
                        <td><?= $leave['lvaType']; ?></td>
                        <td><?= $leave['lvaReason']; ?></td>
                        <td>
                            <?php
                            // Apply the status color-coding
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
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="14" style="text-align: center; font-style: italic;">No records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
