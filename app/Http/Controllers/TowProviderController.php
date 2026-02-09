<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class TowProviderController extends Controller
{
    /**
     * Tow Provider directory page: header, intro, directory by island, video placeholder, signup CTA.
     */
    public function index(Request $request): View
    {
        $islands = $this->getDirectoryByIsland();

        return view('tow-provider.index', [
            'islands' => $islands,
        ]);
    }

    /**
     * Directory data: islands from config with placeholder provider names per island.
     * Replace placeholder arrays with DB query when you have tow provider records.
     */
    protected function getDirectoryByIsland(): array
    {
        $islandNames = config('islands.list', []);
        $directory = [];

        foreach ($islandNames as $island) {
            $directory[$island] = [
                // Placeholder until you have real tow providers; replace with DB lookup by island
                '— Providers will be listed here when available',
            ];
        }

        return $directory;
    }
}
