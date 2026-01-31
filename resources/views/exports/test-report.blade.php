<!DOCTYPE html>
<html>
<head>
    <title>Test Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Test Report</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $item['id'] }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['value'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
