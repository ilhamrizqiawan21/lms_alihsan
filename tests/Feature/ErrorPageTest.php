<?php

namespace Tests\Feature;

use Tests\TestCase;

class ErrorPageTest extends TestCase
{
    public function test_web_404_uses_not_found_page_instead_of_maintenance_page(): void
    {
        $response = $this->get('/halaman-yang-tidak-ada');

        $response->assertStatus(404);
        $response->assertSee('Halaman tidak ditemukan');
        $response->assertDontSee('sedang dalam perbaikan/pengembangan');
    }
}
