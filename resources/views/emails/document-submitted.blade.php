<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Document Submitted</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f6f9; padding: 30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 30px 40px; text-align: center;">
                            <h1 style="color: #000000ff; margin: 0; font-size: 22px; font-weight: 600;">📄 New Document Submitted</h1>
                        </td>
                    </tr>
                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px 40px;">
                            <p style="color: #4a5568; font-size: 15px; line-height: 1.6; margin: 0 0 20px;">
                                Dear Accounting Staff,
                            </p>
                            <p style="color: #4a5568; font-size: 15px; line-height: 1.6; margin: 0 0 20px;">
                                A new document has been submitted and is waiting for your review.
                            </p>

                            <!-- Document Info Card -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #edf2f7; border-radius: 6px; margin: 20px 0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table width="100%" cellpadding="4" cellspacing="0">
                                            <tr>
                                                <td style="color: #718096; font-size: 13px; width: 140px; vertical-align: top;">Document Type</td>
                                                <td style="color: #2d3748; font-size: 13px; font-weight: 600;">{{ $documentType }}</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #718096; font-size: 13px; vertical-align: top;">Document Number</td>
                                                <td style="color: #2d3748; font-size: 13px; font-weight: 600;">{{ $document->document_number }}</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #718096; font-size: 13px; vertical-align: top;">Submitted By</td>
                                                <td style="color: #2d3748; font-size: 13px; font-weight: 600;">{{ $submitter->name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #718096; font-size: 13px; vertical-align: top;">Department</td>
                                                <td style="color: #2d3748; font-size: 13px; font-weight: 600;">{{ $submitter->department->department ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #718096; font-size: 13px; vertical-align: top;">Submission Date</td>
                                                <td style="color: #2d3748; font-size: 13px; font-weight: 600;">{{ $document->created_at->format('M d, Y, H:i') }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #4a5568; font-size: 15px; line-height: 1.6; margin: 20px 0;">
                                Please review the document by opening it through the link below.
                            </p>

                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 25px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $url }}" style="display: inline-block; color: #009dffff; text-decoration: none; padding: 12px 30px; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                            Open Document
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f7fafc; padding: 20px 40px; border-top: 1px solid #e2e8f0;">
                            <p style="color: #a0aec0; font-size: 12px; margin: 0; text-align: center;">
                                This email was automatically generated by the {{ config('mail.from.name') }}. Please do not reply to this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
