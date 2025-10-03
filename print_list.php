<?php
require_once '../config/settings.php';
require_once '../includes/functions.php';
check_admin();

// Get filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$where = "";

if ($filter == 'attended') {
    $where = "WHERE attended = 1";
} elseif ($filter == 'not_attended') {
    $where = "WHERE attended = 0";
} elseif ($filter == 'paid') {
    $where = "WHERE payment_status = 'success'";
}

// Get registrations
$sql = "SELECT * FROM registrations $where ORDER BY primary_name ASC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Attendee List - <?php echo EVENT_NAME; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border-bottom: 2px solid #333;
        }
        
        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 14px;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #333;
            padding: 5px;
            text-align: left;
        }
        
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .check-box {
            width: 40px;
            text-align: center;
        }
        
        .signature-section {
            margin-top: 30px;
            padding: 20px;
            border-top: 1px solid #333;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                font-size: 10px;
            }
            
            .page-break {
                page-break-after: always;
            }
            
            @page {
                margin: 10mm;
            }
        }
        
        .no-print {
            position: fixed;
            top: 0;
            right: 0;
            padding: 10px;
            background: white;
            border: 1px solid #333;
            z-index: 1000;
        }
        
        .stats {
            margin: 10px 0;
            padding: 10px;
            background: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">🖨️ Print</button>
        <button onclick="window.close()">❌ Close</button>
        <select onchange="location.href='?filter='+this.value">
            <option value="all" <?php echo $filter == 'all' ? 'selected' : ''; ?>>All Registrations</option>
            <option value="paid" <?php echo $filter == 'paid' ? 'selected' : ''; ?>>Paid Only</option>
            <option value="attended" <?php echo $filter == 'attended' ? 'selected' : ''; ?>>Attended Only</option>
            <option value="not_attended" <?php echo $filter == 'not_attended' ? 'selected' : ''; ?>>Not Attended</option>
        </select>
    </div>

    <div class="header">
        <h1><?php echo EVENT_NAME; ?> - Attendee List</h1>
        <p>Date: <?php echo date('d M Y'); ?> | Time: <?php echo date('h:i A'); ?></p>
        <p>Event Date: March 15, 2025 | Venue: Convention Center</p>
    </div>

    <?php
    $total_count = mysqli_num_rows($result);
    $attended_count = 0;
    $total_amount = 0;
    ?>

    <div class="stats">
        <strong>Filter:</strong> <?php echo ucwords(str_replace('_', ' ', $filter)); ?> | 
        <strong>Total Records:</strong> <?php echo $total_count; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">S.No</th>
                <th style="width: 120px;">Unique ID</th>
                <th>Name</th>
                <th style="width: 150px;">Email</th>
                <th style="width: 100px;">Phone</th>
                <th style="width: 50px;">Tickets</th>
                <th style="width: 80px;">Amount</th>
                <th class="check-box">✓</th>
                <th style="width: 120px;">Signature</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $count = 1;
            mysqli_data_seek($result, 0);
            while($row = mysqli_fetch_assoc($result)): 
                if($row['attended'] == 1) $attended_count++;
                $total_amount += $row['total_amount'];
            ?>
            <tr>
                <td><?php echo $count++; ?></td>
                <td style="font-size: 10px;"><?php echo $row['unique_id']; ?></td>
                <td><strong><?php echo $row['primary_name']; ?></strong></td>
                <td style="font-size: 10px;"><?php echo $row['primary_email']; ?></td>
                <td><?php echo $row['primary_phone']; ?></td>
                <td style="text-align: center;"><?php echo 1 + $row['additional_attendees']; ?></td>
                <td>₹<?php echo number_format($row['total_amount'], 0); ?></td>
                <td class="check-box"><?php echo $row['attended'] == 1 ? '✓' : '☐'; ?></td>
                <td></td>
            </tr>
            
            <?php 
            // Add additional attendees
            $sql2 = "SELECT * FROM attendees WHERE registration_id = " . $row['id'];
            $result2 = mysqli_query($conn, $sql2);
            while($attendee = mysqli_fetch_assoc($result2)):
            ?>
            <tr style="background-color: #f9f9f9;">
                <td><?php echo $count++; ?></td>
                <td style="font-size: 10px;"><?php echo $attendee['attendee_unique_id']; ?></td>
                <td>&nbsp;&nbsp;└ <?php echo $attendee['attendee_name']; ?></td>
                <td colspan="2"><em>Guest of <?php echo $row['primary_name']; ?></em></td>
                <td style="text-align: center;">-</td>
                <td>-</td>
                <td class="check-box"><?php echo $attendee['attended'] == 1 ? '✓' : '☐'; ?></td>
                <td></td>
            </tr>
            <?php endwhile; ?>
            
            <?php 
            // Add page break every 25 rows
            if($count % 25 == 0): ?>
                </tbody></table>
                <div class="page-break"></div>
                <table><thead>
                    <tr>
                        <th style="width: 40px;">S.No</th>
                        <th style="width: 120px;">Unique ID</th>
                        <th>Name</th>
                        <th style="width: 150px;">Email</th>
                        <th style="width: 100px;">Phone</th>
                        <th style="width: 50px;">Tickets</th>
                        <th style="width: 80px;">Amount</th>
                        <th class="check-box">✓</th>
                        <th style="width: 120px;">Signature</th>
                    </tr>
                </thead><tbody>
            <?php endif; ?>
            
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="signature-section">
        <p><strong>Summary:</strong></p>
        <p>Total Registrations: <?php echo $total_count; ?> | 
           Already Checked In: <?php echo $attended_count; ?> | 
           Total Revenue: ₹<?php echo number_format($total_amount, 2); ?></p>
        <br><br>
        <table style="border: none; width: 100%;">
            <tr>
                <td style="border: none; width: 33%; text-align: center;">
                    _______________________<br>
                    Event Coordinator
                </td>
                <td style="border: none; width: 33%; text-align: center;">
                    _______________________<br>
                    Check-in Staff
                </td>
                <td style="border: none; width: 33%; text-align: center;">
                    _______________________<br>
                    Authorized Signature
                </td>
            </tr>
        </table>
    </div>
</body>
</html>