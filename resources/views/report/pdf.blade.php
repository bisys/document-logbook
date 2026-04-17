<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h3 {
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h3>ITSP Document Logbook Report</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Document Type</th>
                <th>Number</th>
                <th>Document Number</th>
                <th>User Name</th>
                <th>Department</th>
                <th>Document Status</th>
                <th>Has Revision</th>
                <th>Created Date</th>
                <th>Hardfile Received Date</th>
                <th>Payment Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row['document_type'] }}</td>
                <td>{{ $row['number'] }}</td>
                <td>{{ $row['document_number'] }}</td>
                <td>{{ $row['user_name'] }}</td>
                <td>{{ $row['department'] }}</td>
                <td>{{ $row['status'] }}</td>
                <td>{{ $row['has_revision'] }}</td>
                <td>{{ $row['created_at'] }}</td>
                <td>{{ $row['hardfile_received_date'] }}</td>
                <td>{{ $row['payment_receipt'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
