<?php

namespace App\Services\Admin;

class AdminEmailTemplateOps
{
    public static function update($request, $templateName)
    {
        $request->validated();
        $path = resource_path('views/emails/' . $templateName . '.blade.php');
        if (!file_exists($path)) {
            return back()->with('error', 'Template not found.');
        }

        file_put_contents($path, $request->content);
        return back()->with('success', 'Template updated successfully.');
    }
}

