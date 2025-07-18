<?php

namespace App\Console\Commands;

use App\Enums\SubRegionLevel;
use App\Models\SubRegion;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use WuriN7i\IdRefs\Models\Region;

class InitBukuWarga extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bukuwarga:init';

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
        $this->checkUser();

        if ($village = $this->searchVillage()) {
            $subRegion = new SubRegion();
            $subRegion->name = $village->name;
            $subRegion->region()->associate($village);
            $subRegion->level = SubRegionLevel::VILLAGE;
            $subRegion->save();
        }
    }

    protected function checkUser() : void
    {
        if (! User::count()) {
            $this->error('There no user registered! Please make at least 1 user registered.');

            $this->info('Run command `make:filament-user` to create a user.');
            die();
        }
    }

    public function searchVillage(): Region
    {
        $searchCount = 0;
        $message = 'Cari desa...';

        while ($searchCount === 0 || $searchCount > 15) {
            $name = $this->ask($message);
            $searchCount = Region::where('name', 'like', "%{$name}%")
                ->villageOnly()
                ->count();

            $message = $searchCount
                ? "Pencarian kurang spesifik, ditemukan {$searchCount} mengandung '{$name}'"
                : "Tidak ditemukan nama desa mengandung '{$name}'";
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

        $selectedVillage = $this->choice(
            'Pilih desa:',
            $searchResult->map(
                fn (Region $r) => "{$r->id}: {$r->name}, {$r->parent->name}"
            )->toArray()
        );

        preg_match('/^(\d{10}):/', $selectedVillage, $matches);
        return $searchResult->firstWhere('id', $matches[1]);
    }
}
