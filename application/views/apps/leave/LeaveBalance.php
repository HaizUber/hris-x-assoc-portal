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
        <h2>Leave Balance for S.Y: <?= $leaveBalances['schoolyear']; ?></h2>

        <!-- Display Sick Leave Balance -->
        <?php if (isset($leaveBalances['SL_Balance'])): ?>
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
                        <?= $leaveBalances['SL_Balance']; ?> 
                        <?= ($leaveBalances['SL_Balance'] == 1 || $leaveBalances['SL_Balance'] == 0) ? 'day' : 'days'; ?>
                    </td>
                    <td>
                        <?php 
                            $remainingSL = $leaveBalances['SL_Balance'] - $leaveBalances['used_SL'];
                            echo $remainingSL < 0 ? 0 : $remainingSL;
                        ?>
                        <?= ($remainingSL == 1 || $remainingSL == 0) ? 'day' : 'days'; ?>
                    </td>
                    <td>
                        <?= $leaveBalances['used_SL']; ?> 
                        <?= ($leaveBalances['used_SL'] == 1 || $leaveBalances['used_SL'] == 0) ? 'day' : 'days'; ?>
                    </td>
                    <td>
                        <?php 
                            // Calculate unpaid sick leave
                            $unpaidSickLeave = $leaveBalances['used_SL'] - $leaveBalances['SL_Balance'];
                            echo ($unpaidSickLeave > 0) ? $unpaidSickLeave . " " . (($unpaidSickLeave == 1 || $unpaidSickLeave == 0) ? 'day' : 'days') : "0 day";
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php else: ?>
        <p>No sick leave balance data found for this employee.</p>
        <?php endif; ?>

        <!-- Display Vacation Leave Balance -->
        <?php if (isset($leaveBalances['VL_Balance'])): ?>
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
                <?= $leaveBalances['VL_Balance']; ?> 
                <?= ($leaveBalances['VL_Balance'] == 1 || $leaveBalances['VL_Balance'] == 0) ? 'day' : 'days'; ?>
            </td>
            <td>
                <?php 
                    $remainingVL = $leaveBalances['VL_Balance'] - $leaveBalances['used_VL'];
                    echo $remainingVL < 0 ? 0 : $remainingVL;
                ?>
                <?= ($remainingVL == 1 || $remainingVL == 0) ? 'day' : 'day'; ?>
            </td>
            <td>
                <?= $leaveBalances['used_VL']; ?> 
                <?= ($leaveBalances['used_VL'] == 1 || $leaveBalances['used_VL'] == 0) ? 'day' : 'days'; ?>
            </td>
            <td>
                <?php 
                    // Calculate unpaid vacation leave 
                    $unpaidVacationLeave = $leaveBalances['used_VL'] - $leaveBalances['VL_Balance'];
                    echo ($unpaidVacationLeave > 0) ? $unpaidVacationLeave . " " . (($unpaidVacationLeave == 1 || $unpaidVacationLeave == 0) ? 'day' : 'days') : "0 day";
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
