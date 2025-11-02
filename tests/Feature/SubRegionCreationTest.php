<?php

namespace Tests\Feature;

use App\Enums\AreaType;
use App\Enums\SubRegionLevel;
use App\Models\SubRegion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubRegionCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_sub_region_hierarchy()
    {
        // Buat village sebagai root
        $village = SubRegion::create([
            'name' => 'Kalurahan Sleman',
        ]);

        $this->assertDatabaseHas('areas', [
            'id' => $village->id,
            'name' => 'Kalurahan Sleman',
            'type' => AreaType::SubRegion->value,
            'level' => SubRegionLevel::VILLAGE->value,
            'parent_id' => null,
        ]);

        // Buat RW sebagai child dari village
        $rw = SubRegion::create([
            'name' => 'Padukuhan Maguwo',
            'parent_id' => $village->id,
        ]);

        $this->assertDatabaseHas('areas', [
            'id' => $rw->id,
            'name' => 'Padukuhan Maguwo',
            'type' => AreaType::SubRegion->value,
            'level' => SubRegionLevel::RW->value,
            'parent_id' => $village->id,
        ]);

        // Buat RT sebagai child dari RW
        $rt = SubRegion::create([
            'name' => 'RT 001',
            'parent_id' => $rw->id,
        ]);

        $this->assertDatabaseHas('areas', [
            'id' => $rt->id,
            'name' => 'RT 001',
            'type' => AreaType::SubRegion->value,
            'level' => SubRegionLevel::RT->value,
            'parent_id' => $rw->id,
        ]);

        // Verifikasi hierarchy relationship
        $village->refresh();
        $rw->refresh();
        $rt->refresh();

        $this->assertNull($village->parent);
        $this->assertEquals($village->id, $rw->parent->id);
        $this->assertEquals($rw->id, $rt->parent->id);

        // Verifikasi children relationship
        $this->assertTrue($village->children->contains($rw));
        $this->assertTrue($rw->children->contains($rt));
        $this->assertCount(0, $rt->children);
    }

    public function test_level_auto_increment_works_correctly()
    {
        // Test bahwa level bertambah otomatis sesuai parent

        // Level 0: Village tanpa parent
        $village = SubRegion::create(['name' => 'Test Village']);
        $this->assertEquals(SubRegionLevel::VILLAGE, $village->level);

        // Level 1: RW dengan parent village
        $rw = SubRegion::create([
            'name' => 'Test RW',
            'parent_id' => $village->id
        ]);
        $rw->refresh();
        $this->assertEquals(SubRegionLevel::RW, $rw->level);
        $this->assertEquals($village->level->value + 1, $rw->level->value);

        // Level 2: RT dengan parent RW
        $rt = SubRegion::create([
            'name' => 'Test RT',
            'parent_id' => $rw->id
        ]);
        $rt->refresh();
        $this->assertEquals(SubRegionLevel::RT, $rt->level);
        $this->assertEquals($rw->level->value + 1, $rt->level->value);
    }

    public function test_multiple_children_same_level()
    {
        // Test bahwa bisa ada multiple child dengan level yang sama

        $village = SubRegion::create(['name' => 'Main Village']);

        // Buat beberapa RW di bawah village yang sama
        $rw1 = SubRegion::create([
            'name' => 'RW 001',
            'parent_id' => $village->id
        ]);

        $rw2 = SubRegion::create([
            'name' => 'RW 002',
            'parent_id' => $village->id
        ]);

        $rw3 = SubRegion::create([
            'name' => 'RW 003',
            'parent_id' => $village->id
        ]);

        // Refresh untuk mendapatkan data terbaru
        collect([$rw1, $rw2, $rw3])->each->refresh();

        // Semua RW harus memiliki level yang sama (RW = 1)
        $this->assertEquals(SubRegionLevel::RW, $rw1->level);
        $this->assertEquals(SubRegionLevel::RW, $rw2->level);
        $this->assertEquals(SubRegionLevel::RW, $rw3->level);

        // Semua RW harus memiliki parent yang sama
        $this->assertEquals($village->id, $rw1->parent_id);
        $this->assertEquals($village->id, $rw2->parent_id);
        $this->assertEquals($village->id, $rw3->parent_id);

        // Village harus memiliki 3 children
        $village->refresh();
        $this->assertCount(3, $village->children);
        $this->assertTrue($village->children->contains($rw1));
        $this->assertTrue($village->children->contains($rw2));
        $this->assertTrue($village->children->contains($rw3));
    }

    public function test_deep_hierarchy_level_increment()
    {
        // Test untuk memastikan level increment bekerja di hierarchy yang dalam

        // Buat chain: Village -> RW -> RT
        $village = SubRegion::create(['name' => 'Deep Village']);

        $rw = SubRegion::create([
            'name' => 'Deep RW',
            'parent_id' => $village->id
        ]);

        $rt = SubRegion::create([
            'name' => 'Deep RT',
            'parent_id' => $rw->id
        ]);

        // Refresh semua models
        collect([$village, $rw, $rt])->each->refresh();

        // Verifikasi sequence level
        $this->assertEquals(0, $village->level->value);
        $this->assertEquals(1, $rw->level->value);
        $this->assertEquals(2, $rt->level->value);

        // Verifikasi enum mapping
        $this->assertEquals(SubRegionLevel::VILLAGE, $village->level);
        $this->assertEquals(SubRegionLevel::RW, $rw->level);
        $this->assertEquals(SubRegionLevel::RT, $rt->level);

        // Verifikasi label
        $this->assertEquals('Kalurahan', $village->level->label());
        $this->assertEquals('Padukuhan', $rw->level->label());
        $this->assertEquals('Rukun Tetangga', $rt->level->label());
    }
}
