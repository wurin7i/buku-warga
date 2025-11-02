<?php

namespace App\Console\Commands;

use Exception;
use App\Enums\SubRegionLevel;
use App\Models\SubRegion;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use WuriN7i\IdRefs\Models\Region;

class InitBukuWarga extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bukuwarga:init {--auto : Automatically download region data without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize buku warga';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->checkRegionData();

        if ($village = $this->searchVillage()) {
            $subRegion = new SubRegion();
            $subRegion->name = $village->name;
            $subRegion->region()->associate($village);
            $subRegion->level = SubRegionLevel::VILLAGE;
            $subRegion->save();

            $this->info("Sub-region '{$subRegion->name}' has been created successfully!");
        }
    }

    protected function checkRegionData(): void
    {
        $regionCount = Region::count();
        
        if ($regionCount === 0) {
            $this->warn('No region data found in database. Initializing region data...');
            $this->info('This process may take several minutes. Please wait...');
            
            $message = 'Do you want to download and populate region data from github.com/cahyadsn/wilayah?';
            $shouldProceed = $this->option('auto') || $this->confirm($message, true);
            
            if ($shouldProceed) {
                try {
                    // Run with non-interactive flag to avoid prompts
                    $exitCode = Artisan::call('idrefs:update-region', [
                        '--no-interaction' => true,
                    ]);
                    
                    if ($exitCode === 0) {
                        $this->info('Region data has been successfully populated!');
                        
                        // Verify that villages are now available
                        $villageCount = Region::villageOnly()->count();
                        $this->info("Total villages now available: {$villageCount}");
                    } else {
                        throw new Exception('Command failed with exit code: ' . $exitCode);
                    }
                } catch (Exception $e) {
                    $this->error('Failed to populate region data: ' . $e->getMessage());
                    $this->error('Please run `php artisan idrefs:update-region --no-interaction` manually.');
                    die();
                }
            } else {
                $this->error('Region data is required to continue. Aborting initialization.');
                die();
            }
        } else {
            $this->info("Region data already exists ({$regionCount} records found).");
        }
    }

    public function searchVillage(): Region
    {
        // Check if there are any villages available
        $totalVillages = Region::villageOnly()->count();
        if ($totalVillages === 0) {
            $this->error('No village data found in the region database.');
            $this->info('The region data might not be properly populated or ' .
                       'the villageOnly() scope might not be working correctly.');
            die();
        }

        $this->info("Total villages available: {$totalVillages}");
        
        $searchCount = 0;
        $message = 'Search for village...';

        while ($searchCount === 0 || $searchCount > 15) {
            $name = $this->ask($message);
            $searchCount = Region::where('name', 'like', "%{$name}%")
                ->villageOnly()
                ->count();

            if ($searchCount === 0) {
                $message = "No villages found containing '{$name}'. Try another keyword...";
            } elseif ($searchCount > 15) {
                $message = "Search too broad, found {$searchCount} villages containing '{$name}'. " .
                          "Use more specific keywords...";
            }
        }

        return $this->villageOptions($name);
    }

    protected function villageOptions(string $name): Region
    {
        /** @var Collection */
        $searchResult = Region::where('name', 'like', "%{$name}%")
            ->with('parent')
            ->villageOnly()
            ->get();

        if ($searchResult->isEmpty()) {
            $this->error("No villages found matching '{$name}'");
            die();
        }

        $options = $searchResult->map(function (Region $r) {
            $parentName = $r->parent ? $r->parent->name : 'Unknown';
            return "{$r->id}: {$r->name}, {$parentName}";
        })->toArray();

        $selectedVillage = $this->choice('Select village:', $options);

        preg_match('/^(\d{10}):/', $selectedVillage, $matches);
        
        if (!isset($matches[1])) {
            $this->error('Invalid selection format');
            die();
        }

        $village = $searchResult->firstWhere('id', $matches[1]);
        
        if (!$village) {
            $this->error('Selected village not found');
            die();
        }

        return $village;
    }
}
