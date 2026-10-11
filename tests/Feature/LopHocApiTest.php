<?php

namespace Tests\Feature;

use App\Models\LopHoc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LopHocApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_lop_hoc_index_endpoint(): void
    {
        LopHoc::create([
            'ten_lop' => 'Lớp API Test',
            'ma_lop' => 'API01',
            'giao_vien' => 'Thầy API',
            'si_so' => 20,
            'trang_thai' => true,
        ]);

        $response = $this->getJson('/api/lop-hoc');

        $currentHour = (int) now()->format('H');

        if ($currentHour < 8 || $currentHour >= 17) {
            $response->assertStatus(400);
            $response->assertJson([
                'success' => false,
                'message' => 'Bạn chỉ được thực hiện các thao tác trong giờ hành chính',
            ]);
        } else {
            $response->assertStatus(200);
            $response->assertJsonStructure([
                'success',
                'message',
                'data',
            ]);
        }
    }
}
