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
    <h2>Leave Balance for S.Y: <?= !empty($leaveBalances['schoolyear']) ? $leaveBalances['schoolyear'] : $latestSchoolYear; ?></h2>

        <!-- Display Sick Leave Balance -->
        <?php if (isset($leaveBalances['SL_Balance']) && $leaveBalances['SL_Balance'] !== null): ?>
        <h3>Sick Leave</h3>
        <table>
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Employee Name</th>
                    <th>Leave Balance</th>
                    <th>Leave Used</th>
                    <th>Remaining Leave</th>
                    <th>Sick Leave without Pay</th>
                </tr>
            </thead>
            <tbody>
    <tr>
        <td><?= $leaveBalances['empID']; ?></td>
        <td>
            <?php
            if (isset($userDetails)) {
                echo $userDetails['last_name'] . ', ' . $userDetails['first_name'] . ' ' . $userDetails['middle_name'];
            } else {
                echo 'N/A';
            }
            ?>
        </td>
        <td>
            <?= $leaveBalances['SL_Balance']; ?> 
            <?= ($leaveBalances['SL_Balance'] == 1 || $leaveBalances['SL_Balance'] == 0) ? 'day' : 'days'; ?>
        </td>
        <td>
            <?= $leaveBalances['used_SL']; ?> 
            <?= ($leaveBalances['used_SL'] == 1 || $leaveBalances['used_SL'] == 0) ? 'day' : 'days'; ?>
        </td>
        <td>
            <?php 
                $remainingSL = $leaveBalances['SL_Balance'] - $leaveBalances['used_SL'];
                // Ensure "day" or "days" is displayed correctly based on the remaining balance
                echo ($remainingSL > 0) ? $remainingSL . " " . (($remainingSL == 1 || $remainingSL == 0) ? 'day' : 'days') : "0 day";
            ?>
        </td>
        <td>
            <?php 
                $unpaidSickLeave = $leaveBalances['used_SL'] - $leaveBalances['SL_Balance'];
                // Ensure "day" or "days" is displayed correctly for unpaid sick leave
                echo ($unpaidSickLeave > 0) ? $unpaidSickLeave . " " . (($unpaidSickLeave == 1 || $unpaidSickLeave == 0) ? 'day' : 'days') : "0 day";
            ?>
        </td>
    </tr>
</tbody>

        </table>
        <?php else: ?>
        <!-- Table with values set to 0 when data is not found -->
        <h3>Sick Leave</h3>
        <table>
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Employee Name</th>
                    <th>Leave Balance</th>
                    <th>Leave Used</th>
                    <th>Remaining Leave</th>
                    <th>Sick Leave without Pay</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $_SESSION['employee_id']; ?></td>
                    <td><?= isset($userDetails) ? $userDetails['last_name'] . ', ' . $userDetails['first_name'] . ' ' . $userDetails['middle_name'] : 'N/A'; ?></td>
                    <td>0 day</td>
                    <td>0 day</td>
                    <td>0 day</td>
                    <td>0 day</td>
                </tr>
            </tbody>
        </table>
        <?php endif; ?>

        <!-- Display Vacation Leave Balance -->
        <?php if (isset($leaveBalances['VL_Balance']) && $leaveBalances['VL_Balance'] !== null): ?>
        <h3>Vacation Leave</h3>
        <table>
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Employee Name</th>
                    <th>Leave Balance</th>
                    <th>Leave Used</th>
                    <th>CDLD</th> <!-- CDLD column here -->
                    <th>Remaining Leave</th>
                    <th>Vacation Leave without Pay</th>
                </tr>
            </thead>
            <tbody>
    <tr>
        <!-- Employee ID -->
        <td><?= $leaveBalances['empID']; ?></td>

        <!-- Employee Name -->
        <td>
            <?php
            if (isset($userDetails)) {
                echo $userDetails['last_name'] . ', ' . $userDetails['first_name'] . ' ' . $userDetails['middle_name'];
            } else {
                echo 'N/A';
            }
            ?>
        </td>

        <!-- Vacation Leave Balance -->
        <td>
            <?= $leaveBalances['VL_Balance']; ?> 
            <?= ($leaveBalances['VL_Balance'] == 1 || $leaveBalances['VL_Balance'] == 0) ? 'day' : 'days'; ?>
        </td>

        <!-- Used Vacation Leave (excluding CDLD) -->
        <td>
            <?= $leaveBalances['used_VL']; ?> 
            <?= ($leaveBalances['used_VL'] == 1 || $leaveBalances['used_VL'] == 0) ? 'day' : 'days'; ?>
        </td>

        <!-- CDLD -->
        <td>
            <?= isset($cdldVlCount) && $cdldVlCount !== null ? $cdldVlCount : 0; ?> days
        </td>

        <!-- Remaining Vacation Leave (including CDLD) -->
        <td>
            <?php 
                // Calculate remaining VL: VL Balance - Used VL - CDLD
                $remainingVL = $leaveBalances['VL_Balance'] - $leaveBalances['used_VL'] - (isset($cdldVlCount) ? $cdldVlCount : 0);
                // Ensure "day" or "days" is displayed correctly based on the remaining balance
                echo ($remainingVL > 0) ? $remainingVL . " " . (($remainingVL == 1 || $remainingVL == 0) ? 'day' : 'days') : "0 day";
            ?>
        </td>

        <!-- Unpaid Vacation Leave -->
        <td>
            <?php 
                // Calculate remaining VL after used VL and CDLD
                $remainingVL = $leaveBalances['VL_Balance'] - $leaveBalances['used_VL'] - (isset($cdldVlCount) ? $cdldVlCount : 0);
                log_message('debug', "Remaining VL Calculation: {$leaveBalances['VL_Balance']} - {$leaveBalances['used_VL']} - {$cdldVlCount} = {$remainingVL}");

                // If remaining VL is negative, calculate unpaid vacation leave
                $unpaidVacationLeave = max(0, -$remainingVL);  // We take the negative of the remaining VL if it's negative
                log_message('debug', "Unpaid Vacation Leave Calculation: max(0, -{$remainingVL}) = {$unpaidVacationLeave}");

                // Display the result for unpaid leave with correct "day" or "days"
                echo number_format($unpaidVacationLeave, 2) . " " . (($unpaidVacationLeave == 1 || $unpaidVacationLeave == 0) ? 'day' : 'days');
            ?>
        </td>
    </tr>
</tbody>

        </table>
        <?php else: ?>
        <!-- Table with values set to 0 when data is not found -->
        <h3>Vacation Leave</h3>
        <table>
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Employee Name</th>
                    <th>Leave Balance</th>
                    <th>Leave Used</th>
                    <th>CDLD</th> <!-- CDLD column here -->
                    <th>Remaining Leave</th>
                    <th>Vacation Leave without Pay</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $_SESSION['employee_id']; ?></td>
                    <td><?= isset($userDetails) ? $userDetails['last_name'] . ', ' . $userDetails['first_name'] . ' ' . $userDetails['middle_name'] : 'N/A'; ?></td>
                    <td>0 day</td>
                    <td>0 day</td>
                    <td>0 day</td>
                    <td>0 day</td>
                    <td>0 day</td>
                </tr>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</body>
</html>
