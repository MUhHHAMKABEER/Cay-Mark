<?php

namespace App\Services\Admin;

class AdminEmailTemplateOps
{
    public static function update($request, $templateName)
    {
        $request->validate([
            'editor_mode' => 'nullable|in:simple,advanced',
            'content' => 'required_if:editor_mode,advanced|nullable|string',
            'email_title' => 'nullable|string',
            'heading' => 'nullable|string',
            'message' => 'nullable|string',
            'button_text' => 'nullable|string',
            'button_url' => 'nullable|string',
        ]);

        $path = resource_path('views/emails/' . $templateName . '.blade.php');
        if (!file_exists($path)) {
            return back()->with('error', 'Template not found.');
        }

        if ($request->input('editor_mode') === 'advanced' && $request->filled('content')) {
            $content = $request->content;
        } else {
            $content = self::buildTemplateFromSimpleFields($request);
        }

        file_put_contents($path, $content);
        return back()->with('success', 'Template updated successfully.');
    }

    /**
     * Build full Blade email HTML from simple editor fields.
     */
    protected static function buildTemplateFromSimpleFields($request): string
    {
        $title = $request->input('email_title', 'CayMark');
        $heading = $request->input('heading', '');
        $message = $request->input('message', '');
        $buttonText = $request->input('button_text', '');
        $buttonUrl = $request->input('button_url', '#');

        // Preserve Blade syntax (e.g. {{ $user->name }}) in message paragraphs
        $messageHtml = $message !== ''
            ? implode("\n        ", array_map(function ($p) {
                $p = trim($p);
                if ($p === '') return '';
                return '<p>' . $p . '</p>';
            }, preg_split('/\n\s*\n/', $message)))
            : '<p></p>';

        $buttonBlock = '';
        if ($buttonText !== '') {
            $buttonBlock = '
        <div style="text-align: center;">
            <a href="' . e($buttonUrl) . '" class="button">' . e($buttonText) . '</a>
        </div>';
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .button {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6b7280;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CayMark</h1>
        <p>Island Exchange & Auction House</p>
    </div>
    
    <div class="content">
        <h2>{$heading}</h2>
        
        {$messageHtml}
        {$buttonBlock}
        
        <p>Best regards,<br>The CayMark Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} CayMark. All rights reserved.</p>
    </div>
</body>
</html>
HTML;
    }
}

