<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Approved</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden; }
        .header { background: #22c55e; color: white; padding: 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .content { padding: 24px; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f1f1; }
        .detail-label { color: #6b7280; font-size: 14px; }
        .detail-value { font-weight: 500; font-size: 14px; }
        .footer { background: #f9fafb; padding: 16px 24px; text-align: center; color: #9ca3af; font-size: 12px; }
        .badge { display: inline-block; background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Booking Approved</h1>
        </div>
        <div class="content">
            <p style="margin: 0 0 16px; font-size: 16px; color: #374151;">Hello {{ $user->full_name ?? $user->name ?? 'User' }},</p>
            <p style="margin: 0 0 20px; font-size: 14px; color: #4b5563;">
                Your booking request has been officially <span class="badge">Approved</span>.
            </p>

            <div class="detail-row">
                <span class="detail-label">Resource</span>
                <span class="detail-value">{{ $resourceName }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Booking ID</span>
                <span class="detail-value">#{{ $booking->booking_id }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Start Date</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($booking->start_date)->format('M d, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">End Date</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($booking->end_date)->format('M d, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Time</span>
                <span class="detail-value">{{ $booking->start_time ?? 'All Day' }} - {{ $booking->end_time ?? '' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Purpose</span>
                <span class="detail-value">{{ $booking->purpose ?? 'Not specified' }}</span>
            </div>

            <p style="margin: 20px 0 0; font-size: 14px; color: #4b5563;">
                You can view and manage all your bookings in the application.
                Thank you for using our scheduling system.
            </p>
        </div>
        <div class="footer">
            This email was sent automatically. Please do not reply.
        </div>
    </div>
</body>
</html>
