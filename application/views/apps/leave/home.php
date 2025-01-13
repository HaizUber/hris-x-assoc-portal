<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Leave</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>">
    <style>

        h1 {
            text-align: center;
            font-size: 20px;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
        }
        table td {
            padding: 10px;
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
            width: 90%;
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
    </style>
</head>
<body>
    <div class="container">

        <!-- Success Message -->
        <?php if ($this->session->flashdata('success')) : ?>
            <p class="success"><?= $this->session->flashdata('success'); ?></p>
        <?php endif; ?>

        <form action="<?= site_url('leave/submit'); ?>" method="post">
            <table>
            <tr>
                <td>Employee ID:</td>
            <td>
                <input type="text" name="empID" id="empID" required>
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
                        <input type="number" name="lvaHours" id="lvaHours">
                    </td>
                </tr>
                <tr>
                <td>
                    Fractional Leave:
                    <span style="margin-left: 10px; display: inline-flex; align-items: center;">
                    <input type="checkbox" name="lvaFractional" id="lvaFractional" style="margin-right: 5px;">
                    <label for="lvaFractional" style="margin: 0;">Check for fractional leave</label>
                    </span>
                </td>
                </tr>


                <tr>
                    <td>Start Time:</td>
                    <td>
                        <select name="startTimeHour">
                            <?php for ($i = 0; $i <= 23; $i++) : ?>
                                <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT); ?>"><?= str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
                            <?php endfor; ?>
                        </select>
                        :
                        <select name="startTimeMinute">
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT); ?>"><?= str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
                            <?php endfor; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>End Time:</td>
                    <td>
                        <select name="endTimeHour">
                            <?php for ($i = 0; $i <= 23; $i++) : ?>
                                <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT); ?>"><?= str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
                            <?php endfor; ?>
                        </select>
                        :
                        <select name="endTimeMinute">
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
