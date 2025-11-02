<?php

namespace Tests\Unit;

use App\Enums\AreaType;
use App\Enums\SubRegionLevel;
use App\Models\SubRegion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubRegionTest extends TestCase
{
    use RefreshDatabase;

    public function test_sub_region_creation_sets_correct_type()
    {
        $subRegion = SubRegion::factory()->create([
            'name' => 'Test Village',
        ]);

        $this->assertInstanceOf(SubRegion::class, $subRegion);
        $this->assertEquals(AreaType::SubRegion, $subRegion->type);
        $this->assertEquals('Test Village', $subRegion->name);
    }

    public function test_sub_region_without_parent_defaults_to_village_level()
    {
        $subRegion = SubRegion::factory()->create([
            'name' => 'Root Village',
            'parent_id' => null,
        ]);

        $this->assertEquals(SubRegionLevel::VILLAGE, $subRegion->level);
    }

    public function test_sub_region_level_increases_from_parent()
    {
        // Buat parent village (level 0)
        $village = SubRegion::factory()->create([
            'name' => 'Parent Village',
            'level' => SubRegionLevel::VILLAGE,
            'parent_id' => null,
        ]);

        // Buat child RW yang akan memiliki level parent + 1
        $rw = SubRegion::factory()->create([
            'name' => 'Child RW',
            'parent_id' => $village->id,
        ]);

        $this->assertEquals(SubRegionLevel::RW, $rw->level);
        $this->assertEquals($village->level->value + 1, $rw->level->value);

        // Buat child RT yang akan memiliki level RW + 1
        $rt = SubRegion::factory()->create([
            'name' => 'Child RT',
            'parent_id' => $rw->id,
        ]);

        $this->assertEquals(SubRegionLevel::RT, $rt->level);
        $this->assertEquals($rw->level->value + 1, $rt->level->value);
    }

    public function test_sub_region_hierarchy_maintains_correct_levels()
    {
        // Buat hierarchy lengkap: Village -> RW -> RT
        $village = SubRegion::factory()->create([
            'name' => 'Test Kalurahan',
            'parent_id' => null,
        ]);

        $rw = SubRegion::factory()->create([
            'name' => 'Test Padukuhan',
            'parent_id' => $village->id,
        ]);

        $rt = SubRegion::factory()->create([
            'name' => 'Test RT',
            'parent_id' => $rw->id,
        ]);

        // Refresh models untuk mendapatkan data terbaru
        $village->refresh();
        $rw->refresh();
        $rt->refresh();

        // Verifikasi level hierarchy
        $this->assertEquals(SubRegionLevel::VILLAGE, $village->level);
        $this->assertEquals(SubRegionLevel::RW, $rw->level);
        $this->assertEquals(SubRegionLevel::RT, $rt->level);

        // Verifikasi bahwa setiap level bertambah 1 dari parent
        $this->assertEquals(0, $village->level->value);
        $this->assertEquals(1, $rw->level->value);
        $this->assertEquals(2, $rt->level->value);

        // Verifikasi parent relationships
        $this->assertNull($village->parent_id);
        $this->assertEquals($village->id, $rw->parent_id);
        $this->assertEquals($rw->id, $rt->parent_id);
    }

    public function test_sub_region_parent_relationship()
    {
        $village = SubRegion::factory()->create([
            'name' => 'Parent Village',
        ]);

        $rw = SubRegion::factory()->create([
            'name' => 'Child RW',
            'parent_id' => $village->id,
        ]);

        // Test parent relationship
        $this->assertInstanceOf(SubRegion::class, $rw->parent);
        $this->assertEquals($village->id, $rw->parent->id);
        $this->assertEquals('Parent Village', $rw->parent->name);
    }

    public function test_sub_region_children_relationship()
    {
        $village = SubRegion::factory()->create([
            'name' => 'Parent Village',
        ]);

        $rw1 = SubRegion::factory()->create([
            'name' => 'RW 1',
            'parent_id' => $village->id,
        ]);

        $rw2 = SubRegion::factory()->create([
            'name' => 'RW 2',
            'parent_id' => $village->id,
        ]);

        // Test children relationship
        $children = $village->children;
        $this->assertCount(2, $children);
        $this->assertTrue($children->contains('id', $rw1->id));
        $this->assertTrue($children->contains('id', $rw2->id));
    }

    public function test_sub_region_scope_methods()
    {
        // Clear existing data untuk memastikan test yang bersih
        SubRegion::query()->forceDelete();

        // Buat hierarchy yang benar untuk mendapatkan level yang berbeda
        // Village (level 0) - parent null, akan jadi VILLAGE otomatis
        $village = SubRegion::factory()->create([
            'name' => 'Test Village',
            'parent_id' => null
        ]);

        // RW (level 1) - parent village, akan jadi RW otomatis
        $rw = SubRegion::factory()->create([
            'name' => 'Test RW',
            'parent_id' => $village->id
        ]);

        // RT (level 2) - parent RW, akan jadi RT otomatis
        $rt = SubRegion::factory()->create([
            'name' => 'Test RT',
            'parent_id' => $rw->id
        ]);

        // Refresh untuk mendapatkan nilai terbaru
        $village->refresh();
        $rw->refresh();
        $rt->refresh();

        // Test scope methods
        $this->assertCount(1, SubRegion::villageOnly()->get());
        $this->assertCount(1, SubRegion::rwOnly()->get());
        $this->assertCount(1, SubRegion::rtOnly()->get());

        // Verifikasi bahwa scope mengembalikan record yang benar
        $this->assertEquals($village->id, SubRegion::villageOnly()->first()->id);
        $this->assertEquals($rw->id, SubRegion::rwOnly()->first()->id);
        $this->assertEquals($rt->id, SubRegion::rtOnly()->first()->id);
    }

    public function test_parent_name_attribute_accessor()
    {
        $village = SubRegion::factory()->create([
            'name' => 'Parent Village',
        ]);

        $rw = SubRegion::factory()->create([
            'name' => 'Child RW',
            'parent_id' => $village->id,
        ]);

        // Test accessor dengan loaded relationship
        $rw->load('parent');
        $this->assertEquals('Parent Village', $rw->parent_name);

        // Test accessor tanpa loaded relationship
        $rwFresh = SubRegion::find($rw->id);
        $this->assertEquals('Parent Village', $rwFresh->parent_name);
    }
}
