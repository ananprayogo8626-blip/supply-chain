<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::whereIn('role', ['super_admin', 'admin'])->first() ?? User::first();
        $adminId = $admin ? $admin->id : 1;

        $sampleArticles = [
            [
                'title'     => 'Analisis Risiko Logistik Terusan Suez & Laut Merah Q3 2026',
                'category'  => 'Logistics',
                'summary'   => 'Laporan komprehensif mengenai tingkat kemacetan rute maritim utama dan kenaikan premi asuransi kargo.',
                'content'   => "Gangguan geopolitik di wilayah Laut Merah terus menaikkan waktu transit pengiriman laut antara Asia dan Eropa hingga 10-14 hari tambahan akibat pengalihan rute melalui Tanjung Harapan.

Beberapa poin analisis utama:
1. Peningkatan biaya freight rate kontainer (FEU) hingga 35%.
2. Penumpukan kontainer kosong di pelabuhan hub utama.
3. Rekomendasi mitigasi: Menggunakan strategi multi-modal air-sea freight dan pembukaan buffer stock di kawasan ASEAN.",
                'thumbnail' => 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=800&q=80',
                'status'    => 'Published',
            ],
            [
                'title'     => 'Proyeksi Inflasi & Volatilitas Mata Uang Negara Mitraterbesar',
                'category'  => 'Economy',
                'summary'   => 'Studi perbandingan dampak depresiasi mata uang lokal terhadap biaya manufaktur & impor barang modal.',
                'content'   => "Dinamika ekonomi global dan tren suku bunga acuan memperlihatkan fluktuasi tajam pada nilai tukar valuta asing di kawasan berkembang.

Implikasi Rantai Pasok:
- Kontrak jangka panjang disarankan menggunakan hedging klausul mata uang.
- Evaluasi supplier tier-1 untuk mengantisipasi penurunan margin akibat inflasi bahan baku.",
                'thumbnail' => 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?auto=format&fit=crop&w=800&q=80',
                'status'    => 'Published',
            ],
            [
                'title'     => 'Dampak Cuaca Ekstrem Terhadap Jalur Pelayaran Pasifik',
                'category'  => 'Weather',
                'summary'   => 'Anomali badai dan gelombang tinggi mempengaruhi jadwal kedatangan kapal kargo di pantai barat Amerika Utara.',
                'content'   => "Pemantauan cuaca real-time menggunakan data Open-Meteo menunjukkan peningkatan frekuensi badai kategori 3+ di samudra Pasifik. Peringatan dini telah dikeluarkan untuk jadwal pelayaran Juli-Agustus.",
                'thumbnail' => 'https://images.unsplash.com/photo-1527066579998-dbbae57f4500?auto=format&fit=crop&w=800&q=80',
                'status'    => 'Published',
            ],
        ];

        foreach ($sampleArticles as $art) {
            Article::updateOrCreate(
                ['title' => $art['title']],
                array_merge($art, [
                    'user_id'      => $adminId,
                    'slug'         => Str::slug($art['title']),
                    'published_at' => now(),
                    'views'        => rand(15, 120),
                ])
            );
        }
    }
}
