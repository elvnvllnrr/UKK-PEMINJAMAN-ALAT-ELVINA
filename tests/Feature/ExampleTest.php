<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Kelas pengujian respons dasar aplikasi. */
class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    /** Memastikan aplikasi mengembalikan respons berhasil pada halaman utama. */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $response = $this->get('/');

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $response->assertStatus(200);
    }
}
