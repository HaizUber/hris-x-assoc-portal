<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Balance</title>
    <style>
        .table-container {
            margin-top: 20px;
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 50px;
            margin-top: 50px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="table-container">
        <h2>Leave Balance</h2>

        <!-- Display Sick Leave Balance -->
        <?php if (isset($leaveBalances['sickLeaveBalance'])): ?>
        <h3>Sick Leave</h3>
        <table>
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Leave Balance</th>
                    <th>Leave Remaining</th>
                    <th>Leave Used</th>
                    <th>Total Unpaid Sick Leave</th> 
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $leaveBalances['empID']; ?></td>
                    <td>
    <?= $leaveBalances['sickLeaveBalance']; ?> 
    <?= ($leaveBalances['sickLeaveBalance'] == 1 || $leaveBalances['sickLeaveBalance'] == 0) ? 'day' : 'days'; ?>
</td>
<td>
    <?= $leaveBalances['totalRemainingSickLeave']; ?> 
    <?= ($leaveBalances['totalRemainingSickLeave'] == 1 || $leaveBalances['totalRemainingSickLeave'] == 0) ? 'day' : 'days'; ?>
</td>
<td>
    <?= $leaveBalances['totalSickLeaveUsed']; ?> 
    <?= ($leaveBalances['totalSickLeaveUsed'] == 1 || $leaveBalances['totalSickLeaveUsed'] == 0) ? 'day' : 'days'; ?>
</td>

                    <td>
                        <?php 
                            // Calculate unpaid sick leave (if any)
                            $unpaidSickLeave = $leaveBalances['totalSickLeaveUsed'] - $leaveBalances['sickLeaveBalance'];
                            echo ($unpaidSickLeave > 0) ? $unpaidSickLeave . " " . (($unpaidSickLeave == 1) ? 'day' : 'days') : "0 day";
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php else: ?>
        <p>No sick leave balance data found for this employee.</p>
        <?php endif; ?>

        <!-- Display Vacation Leave Balance -->
        <?php if (isset($leaveBalances['vacationLeaveBalance'])): ?>
        <h3>Vacation Leave</h3>
        <table>
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Leave Balance</th>
                    <th>Leave Remaining</th>
                    <th>Leave Used</th>
                    <th>Total Unpaid Vacation Leave</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $leaveBalances['empID']; ?></td>
                    <td>
    <?= $leaveBalances['vacationLeaveBalance']; ?> 
    <?= ($leaveBalances['vacationLeaveBalance'] == 1 || $leaveBalances['vacationLeaveBalance'] == 0) ? 'day' : 'days'; ?>
</td>
<td>
    <?= $leaveBalances['totalRemainingVacationLeave']; ?> 
    <?= ($leaveBalances['totalRemainingVacationLeave'] == 1 || $leaveBalances['totalRemainingVacationLeave'] == 0) ? 'day' : 'days'; ?>
</td>
<td>
    <?= $leaveBalances['totalVacationLeaveUsed']; ?> 
    <?= ($leaveBalances['totalVacationLeaveUsed'] == 1 || $leaveBalances['totalVacationLeaveUsed'] == 0) ? 'day' : 'days'; ?>
</td>

                    <td>
                        <?php 
                            // Calculate unpaid vacation leave (if any)
                            $unpaidVacationLeave = $leaveBalances['totalVacationLeaveUsed'] - $leaveBalances['vacationLeaveBalance'];
                            echo ($unpaidVacationLeave > 0) ? $unpaidVacationLeave . " " . (($unpaidVacationLeave == 1) ? 'day' : 'days') : "0 day";
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php else: ?>
        <p>No vacation leave balance data found for this employee.</p>
        <?php endif; ?>
    </div>
</body>
</html>
