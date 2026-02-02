<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AdminEmailTemplateUpdateRequest;
use Illuminate\Support\Facades\File;
use App\Services\Admin\AdminEmailTemplateOps;

class EmailTemplateController extends Controller
{
    /**
     * Email Template Management - List all templates
     */
    public function index()
    {
        $templatesPath = resource_path('views/emails');
        $templates = [];

        if (File::exists($templatesPath)) {
            $files = File::files($templatesPath);
            
            foreach ($files as $file) {
                $filename = $file->getFilename();
                $name = str_replace('.blade.php', '', $filename);
                
                $templates[] = [
                    'name' => $name,
                    'filename' => $filename,
                    'path' => $file->getPathname(),
                    'modified' => $file->getMTime(),
                ];
            }
        }

        // Group by category
        $categories = [
            'General User' => ['registration-step1', 'registration-complete'],
            'Buyer' => ['auction-won-invoice', 'payment-reminder-6hours', 'payment-reminder-24hours', 'payment-reminder-48hours', 'payment-successful'],
            'Seller' => ['listing-submitted', 'listing-approved', 'listing-rejected', 'auction-ended-sold', 'payout-processing-started', 'auction-ending-soon-24h', 'auction-ending-soon-1h'],
            'Payment & Invoice' => ['payment-successful-invoice'],
            'Account & Security' => ['password-reset', 'password-changed', 'account-deactivated', 'account-reactivated', 'email-updated', 'confirm-email'],
        ];

        return view('admin.email-template-management', compact('templates', 'categories'));
    }

    /**
     * View/Edit email template
     */
    public function edit($templateName)
    {
        $templatePath = resource_path("views/emails/{$templateName}.blade.php");
        
        if (!File::exists($templatePath)) {
            return redirect()->route('admin.email-templates')
                ->with('error', 'Template not found.');
        }

        $content = File::get($templatePath);
        $defaultContent = $this->getDefaultTemplate($templateName);

        return view('admin.email-template-edit', compact('templateName', 'content', 'defaultContent'));
    }

    /**
     * Update email template
     */
    public function update(AdminEmailTemplateUpdateRequest $request, $templateName)
    {
        return AdminEmailTemplateOps::update($request, $templateName);
    }

    /**
     * Preview email template
     */
    public function preview($templateName)
    {
        // This would render the template with sample data
        // Implementation depends on template structure
        return view("emails.{$templateName}", [
            // Sample data for preview
        ]);
    }

    /**
     * Restore default template
     */
    public function restoreDefault($templateName)
    {
        $defaultContent = $this->getDefaultTemplate($templateName);
        
        if (!$defaultContent) {
            return back()->with('error', 'Default template not available.');
        }

        $templatePath = resource_path("views/emails/{$templateName}.blade.php");
        File::put($templatePath, $defaultContent);

        return back()->with('success', 'Template restored to default.');
    }

    /**
     * Get default template content (placeholder - store defaults separately)
     */
    private function getDefaultTemplate($templateName)
    {
        // In production, store default templates in a separate location
        // For now, return null (admin can manually restore)
        return null;
    }
}

