<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Leave</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 50px;
            margin-bottom: 50px;
        }

        h1 {
            text-align: center;
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-spacing: 10px;
        }

        table td {
            padding: 8px;
            vertical-align: middle;
        }

        table td:first-child {
            text-align: right;
            font-weight: bold;
            color: #333;
        }

        table td:last-child {
            text-align: left;
        }

        input, select, textarea, button {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
        }

        button {
            width: auto;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .note {
            font-size: 12px;
            color: red;
            margin-top: 10px;
        }

        .actions {
            text-align: center;
            margin-top: 20px;
        }

        .success, .error {
            text-align: center;
            font-size: 14px;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }

        .success {
            background-color: #28a745;
            color: #fff;
        }

        .error {
            background-color: #dc3545;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">

        <!-- Success Message -->
        <?php if ($this->session->flashdata('success')) : ?>
            <p class="success"><?= $this->session->flashdata('success'); ?></p>
        <?php endif; ?>
        
        <!-- Error Message -->
        <?php if ($this->session->flashdata('error')) : ?>
            <p class="error"><?= $this->session->flashdata('error'); ?></p>
        <?php endif; ?>


        <form action="<?= site_url('leave/submit'); ?>" method="post" enctype="multipart/form-data">
            <h1>File Leave</h1>
        
            <!-- Redirect Link to Leave Balance -->
        <p style="text-align: right; margin-top: 20px;">
            <a href="<?= site_url('leave/filedleave'); ?>" style="color: #007bff; text-decoration: underline;">View Filed Leave</a>
        </p>
            <table>
            <tr>
                <td>Employee ID:</td>
                <td>
                    <input type="text" name="empID" id="empID" value="<?php echo $this->session->userdata('employee_id'); ?>" readonly />
                </td>
            </tr>

                <tr>
                    <td>Type of Leave:</td>
                    <td>
                        <select name="lvaType" id="lvaType" required>
                            <option value="">Select Leave Type</option>
                            <?php foreach ($leaveTypes as $type) : ?>
                                <option value="<?= $type['leaveCode']; ?>"><?= $type['leaveDescription']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Date of Leave:</td>
                    <td>
                        <label for="lvaDateFrom">From</label>
                        <input type="date" name="lvaDateFrom" id="lvaDateFrom" required>
                        <label for="lvaDateTo">To</label>
                        <input type="date" name="lvaDateTo" id="lvaDateTo" required>
                    </td>
                </tr>
                <tr>
                    <td>Reason:</td>
                    <td>
                        <textarea name="lvaReason" id="lvaReason" rows="4" required></textarea>
                    </td>
                </tr>
                <tr>
                    <td>Total # of Hours:</td>
                    <td>
                        <input type="number" name="lva" id="lva" readonly>
                    </td>
                </tr>
                <div id="medCertField" style="display: none;">
                <div class="note">
                    Note: If Sick Leave is more than Two(2) days please submit a medical certificate (pdf)
                </div>
                <label for="medCert">Medical Certificate (PDF):</label>
                <input type="file" name="medCert" id="medCert" accept="application/pdf">
                </div>
                <tr>
                    <td colspan="2" style="text-align: left; padding-left: 10px;">
                    <label for="lvaFractional" style="margin-right: 10px;">Fractional Leave:</label>
                    <span style="display: inline-flex; align-items: center;">
                    <input type="checkbox" name="lvaFractional" id="lvaFractional" style="margin-right: 5px;">
                    <label for="lvaFractional" style="margin: 0;">Check for fractional leave</label>
                    </span>
                    </td>
                </tr>
                <tr id="fractionalLeaveRow" style="display: none;">
                    <td>Start Time:</td>
                    <td>
                    <select name="startTimeHour" id="startTimeHour">
                        <?php for ($i = 0; $i <= 23; $i++) : ?>
                            <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT); ?>"><?= str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
                        <?php endfor; ?>
                    </select>
                        :
                    <select name="startTimeMinute" id="startTimeMinute">
                        <?php for ($i = 0; $i <= 59; $i++) : ?>
                            <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT); ?>"><?= str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
                        <?php endfor; ?>
                    </select>
                    </td>
                </tr>
            <tr id="fractionalLeaveEndTimeRow" style="display: none;">
                <td>End Time:</td>
                <td>
                <select name="endTimeHour" id="endTimeHour">
                <?php for ($i = 0; $i <= 23; $i++) : ?>
                    <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT); ?>"><?= str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
                <?php endfor; ?>
                </select>
                :
                <select name="endTimeMinute" id="endTimeMinute">
                <?php for ($i = 0; $i <= 59; $i++) : ?>
                    <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT); ?>"><?= str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
                <?php endfor; ?>
                </select>
                </td>
            </tr>
            </table>
            <div class="note">
                Note: Indicate No. of days for full leave application, otherwise click on the fractional leave checkbox for fractional leave and indicate the start and end time.
            </div>
            <div class="actions">
                <button type="reset">Clear Form</button>
                <button type="submit">Submit Application</button>
            </div>
        </form>
    </div>
</body>
</html>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fractionalLeaveCheckbox = document.getElementById('lvaFractional');
    const fractionalLeaveRow = document.getElementById('fractionalLeaveRow');
    const fractionalLeaveEndTimeRow = document.getElementById('fractionalLeaveEndTimeRow');
    const medCertField = document.getElementById('medCertField');
    const clearFormButton = document.querySelector('button[type="reset"]');
    const totalHoursInput = document.getElementById('lva');
    const dateFrom = document.getElementById('lvaDateFrom');
    const dateTo = document.getElementById('lvaDateTo');
    const startTimeHour = document.getElementById('startTimeHour');
    const startTimeMinute = document.getElementById('startTimeMinute');
    const endTimeHour = document.getElementById('endTimeHour');
    const endTimeMinute = document.getElementById('endTimeMinute');

    // Function to calculate the total number of hours
    function calculateTotalHours() {
    const dateFrom = document.getElementById('lvaDateFrom');
    const dateTo = document.getElementById('lvaDateTo');
    const startTimeHour = document.getElementById('startTimeHour');
    const startTimeMinute = document.getElementById('startTimeMinute');
    const endTimeHour = document.getElementById('endTimeHour');
    const endTimeMinute = document.getElementById('endTimeMinute');
    const fractionalLeave = fractionalLeaveCheckbox.checked;

    const start = new Date(dateFrom.value);
    const end = new Date(dateTo.value);

    let totalHours = 0;

    if (fractionalLeave) {
        const startTime = new Date(start);
        startTime.setHours(parseInt(startTimeHour.value), parseInt(startTimeMinute.value), 0, 0);

        const endTime = new Date(end);
        endTime.setHours(parseInt(endTimeHour.value), parseInt(endTimeMinute.value), 0, 0);

        // Loop through each day in the date range
        let currentDay = new Date(start);
        while (currentDay <= end) {
            // For each day, add the fractional hours
            let dayStartTime = currentDay.toISOString().split('T')[0] + " " + startTime.getHours() + ":" + startTime.getMinutes();
            let dayEndTime = currentDay.toISOString().split('T')[0] + " " + endTime.getHours() + ":" + endTime.getMinutes();

            let dayStart = new Date(dayStartTime);
            let dayEnd = new Date(dayEndTime);
            
            // Calculate the difference in hours for each day
            const diff = (dayEnd - dayStart) / (1000 * 60 * 60); // Convert to hours
            totalHours += diff;
            
            // Move to the next day
            currentDay.setDate(currentDay.getDate() + 1);
        }
    } else {
        // Calculate the total hours for full days
        const days = (end - start) / (1000 * 60 * 60 * 24) + 1; // Calculate full days
        totalHours = days * 8; // Assuming 8 hours per day for full leaves
    }

    totalHoursInput.value = totalHours.toFixed(2); // Display the total hours
}


    // Event listeners
    dateFrom.addEventListener('change', calculateTotalHours);
    dateTo.addEventListener('change', calculateTotalHours);
    fractionalLeaveCheckbox.addEventListener('change', calculateTotalHours);
    startTimeHour.addEventListener('change', calculateTotalHours);
    startTimeMinute.addEventListener('change', calculateTotalHours);
    endTimeHour.addEventListener('change', calculateTotalHours);
    endTimeMinute.addEventListener('change', calculateTotalHours);

    // Check the initial state of the checkbox and toggle fields accordingly
    if (fractionalLeaveCheckbox.checked) {
        fractionalLeaveRow.style.display = 'table-row';
        fractionalLeaveEndTimeRow.style.display = 'table-row';
    } else {
        fractionalLeaveRow.style.display = 'none';
        fractionalLeaveEndTimeRow.style.display = 'none';
    }

    // Add event listener to toggle visibility based on checkbox
    fractionalLeaveCheckbox.addEventListener('change', function () {
        if (this.checked) {
            fractionalLeaveRow.style.display = 'table-row';
            fractionalLeaveEndTimeRow.style.display = 'table-row';
        } else {
            fractionalLeaveRow.style.display = 'none';
            fractionalLeaveEndTimeRow.style.display = 'none';
        }
    });

    // Function to toggle Medical Certificate field visibility
    function toggleMedCertField() {
        const leaveType = document.getElementById('lvaType').value;
        const dateFrom = new Date(document.getElementById('lvaDateFrom').value);
        const dateTo = new Date(document.getElementById('lvaDateTo').value);
        const duration = (dateTo - dateFrom) / (1000 * 60 * 60 * 24) + 1;

        // Show or hide the field
        if (leaveType === 'SL' && duration > 2) {
            medCertField.style.display = 'block';
        } else {
            medCertField.style.display = 'none';
        }
    }

    // Add event listeners to relevant fields
    document.getElementById('lvaType').addEventListener('change', toggleMedCertField);
    document.getElementById('lvaDateFrom').addEventListener('change', toggleMedCertField);
    document.getElementById('lvaDateTo').addEventListener('change', toggleMedCertField);

    // Reset medCertField visibility on form reset
    clearFormButton.addEventListener('click', function () {
        medCertField.style.display = 'none';
        fractionalLeaveRow.style.display = 'none';
        fractionalLeaveEndTimeRow.style.display = 'none';
    });
});
</script>
