<?php

namespace App\Console\Commands;

use App\Models\Listing;
use Illuminate\Console\Command;

class GenerateListingSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listings:generate-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate slugs for all existing listings that do not have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating slugs for existing listings...');
        
        $listings = Listing::whereNull('slug')->orWhere('slug', '')->get();
        
        if ($listings->isEmpty()) {
            $this->info('No listings found without slugs.');
            return 0;
        }
        
        $bar = $this->output->createProgressBar($listings->count());
        $bar->start();
        
        $updated = 0;
        foreach ($listings as $listing) {
            $listing->slug = $listing->generateSlug();
            $listing->save();
            $updated++;
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("Successfully generated slugs for {$updated} listings.");
        
        return 0;
    }
}
