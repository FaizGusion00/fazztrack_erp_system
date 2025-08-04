<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job QR Code - {{ $job->job_id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
        }
        .qr-container {
            text-align: center;
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border: 2px solid #333;
            border-radius: 8px;
        }
        .job-info {
            margin-bottom: 20px;
            text-align: left;
        }
        .job-info h2 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 18px;
        }
        .job-info p {
            margin: 5px 0;
            font-size: 14px;
        }
        .qr-code {
            margin: 20px 0;
        }
        .qr-code img {
            max-width: 200px;
            height: auto;
        }
        @media print {
            body { margin: 0; }
            .qr-container { border: none; }
        }
    </style>
</head>
<body>
    <div class="qr-container">
        <div class="job-info">
            <h2>Job #{{ $job->job_id }}</h2>
            <p><strong>Phase:</strong> {{ $job->phase }}</p>
            <p><strong>Order:</strong> {{ $job->order->job_name }}</p>
            <p><strong>Client:</strong> {{ $job->order->client->name }}</p>
            <p><strong>Status:</strong> {{ $job->status }}</p>
            @if($job->assignedUser)
            <p><strong>Assigned to:</strong> {{ $job->assignedUser->name }}</p>
            @endif
        </div>
        
        <div class="qr-code">
            @if($job->qr_code)
                <img src="data:image/png;base64,{{ base64_encode(QrCode::format('png')->size(200)->generate($job->qr_code)) }}" alt="QR Code">
            @else
                <p>QR Code not generated</p>
            @endif
        </div>
        
        <div style="margin-top: 20px; font-size: 12px; color: #666;">
            <p>Scan this QR code to start/end the job</p>
            <p>Generated on: {{ now()->format('M d, Y H:i') }}</p>
        </div>
    </div>
    
    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html> 