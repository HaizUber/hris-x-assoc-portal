<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
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
    </style>
</head>
<body>

    <h1><?php echo $title; ?></h1>

    <?php if (!empty($vacationLeaves)): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Date Filed</th>
                    <th>Leave From</th>
                    <th>Leave To</th>
                    <th>Days</th>
                    <th>Reason</th>
                    <th>Approved By</th>
                    <th>Medical Certificate</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vacationLeaves as $leave): ?>
                    <tr>
                        <td><?php echo date('Y-m-d', strtotime($leave['lvaDateFiled'])); ?></td> 
                        <td><?php echo date('Y-m-d', strtotime($leave['lvaDateFrom'])); ?></td> 
                        <td><?php echo date('Y-m-d', strtotime($leave['lvaDateTo'])); ?></td> 
                        <td><?php echo round($leave['lvaDays'] / 8); ?> days</td>
                        <td><?php echo $leave['lvaReason']; ?></td> 
                        <td><?php echo $leave['lvaApprovedBy']; ?></td> 
                        <td>
                            <?php if ($leave['lvaMedCert']): ?>
                                <a href="<?php echo base_url('uploads/' . $leave['lvaMedCert']); ?>" target="_blank">View Certificate</a>
                            <?php else: ?>
                                No certificate uploaded
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No vacation leave records found for this period.</p>
    <?php endif; ?>

</body>
</html>
