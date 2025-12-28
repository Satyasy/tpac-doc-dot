<?php

namespace Database\Seeders;

use App\Models\HealthArticle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HealthArticleSeeder extends Seeder
{
   /**
    * Run the database seeds.
    */
   public function run(): void
   {
      $articles = [
         [
            'title' => 'Biosecurity Jadi Pertahanan Baru, Menkes: Ancaman Kesehatan Lebih Berbahaya dari Perang Militer',
            'category' => 'Berita & Update Kesehatan',
            'content' => 'Kementerian Kesehatan menegaskan pentingnya biosecurity sebagai pertahanan baru dalam menghadapi ancaman kesehatan global. Menurut Menteri Kesehatan, ancaman dari penyakit menular dan patogen berbahaya bisa lebih destruktif dibandingkan perang militer konvensional. Hal ini terbukti dari dampak pandemi COVID-19 yang melumpuhkan ekonomi dan kehidupan sosial di seluruh dunia. Pemerintah berencana meningkatkan investasi dalam sistem surveilans penyakit, laboratorium biosafety, dan pelatihan tenaga kesehatan untuk mengantisipasi ancaman biologis di masa depan.',
            'source' => 'Kemenkes RI',
            'verified' => true,
            'published_at' => now()->subDays(1),
         ],
         [
            'title' => 'Ini Capaian dan Temuan dalam Program Cek Kesehatan Gratis 2025',
            'category' => 'Berita & Update Kesehatan',
            'content' => 'Program Cek Kesehatan Gratis 2025 telah berhasil menjangkau lebih dari 10 juta masyarakat Indonesia. Dari hasil pemeriksaan, ditemukan bahwa prevalensi hipertensi masih tinggi di kalangan dewasa muda. Program ini juga berhasil mendeteksi dini berbagai penyakit tidak menular seperti diabetes dan gangguan jantung. Kementerian Kesehatan akan melanjutkan program ini dengan penambahan titik layanan di daerah terpencil.',
            'source' => 'Kemenkes RI',
            'verified' => true,
            'published_at' => now()->subDays(2),
         ],
         [
            'title' => '53 Juta Anak Sekolah Bakal di Skrining Kesehatan Mulai Juli 2025',
            'category' => 'Berita & Update Kesehatan',
            'content' => 'Pemerintah akan melaksanakan program skrining kesehatan massal untuk 53 juta anak sekolah mulai Juli 2025. Program ini mencakup pemeriksaan kesehatan mata, gigi, tinggi badan, berat badan, dan deteksi dini masalah kesehatan mental. Tujuannya adalah memastikan anak-anak Indonesia tumbuh sehat dan optimal. Kerjasama antara Kemenkes dan Kemendikbud akan memastikan program ini berjalan lancar di seluruh sekolah di Indonesia.',
            'source' => 'Kemenkes RI',
            'verified' => true,
            'published_at' => now()->subDays(3),
         ],
         [
            'title' => 'Tampak Mirip, Ketahui Beda Gejala Virus Corona dengan Flu Biasa',
            'category' => 'Edukasi Kesehatan',
            'content' => 'Gejala COVID-19 dan flu biasa memang tampak mirip, namun ada beberapa perbedaan penting yang perlu diketahui. COVID-19 seringkali disertai dengan hilangnya indra penciuman dan perasa, yang jarang terjadi pada flu biasa. Selain itu, gejala COVID-19 cenderung berkembang lebih lambat dibandingkan flu. Jika Anda mengalami gejala pernapasan, segera konsultasikan dengan tenaga kesehatan untuk mendapatkan diagnosis yang tepat dan penanganan yang sesuai.',
            'source' => 'WHO Indonesia',
            'verified' => true,
            'published_at' => now()->subDays(5),
         ],
         [
            'title' => 'Panduan Pola Makan Sehat untuk Usia Remaja',
            'category' => 'Tips & Gaya Hidup Sehat',
            'content' => 'Masa remaja adalah periode penting untuk pertumbuhan dan perkembangan. Pola makan yang sehat sangat diperlukan untuk mendukung proses ini. Remaja membutuhkan asupan protein yang cukup untuk membangun otot, kalsium untuk tulang yang kuat, dan zat besi untuk mencegah anemia. Hindari makanan olahan dan minuman manis berlebihan. Konsumsilah buah, sayur, biji-bijian utuh, dan protein tanpa lemak setiap hari.',
            'source' => 'Kemenkes RI',
            'verified' => true,
            'published_at' => now()->subDays(7),
         ],
         [
            'title' => '5 Cara Efektif Menjaga Kesehatan Mental di Era Digital',
            'category' => 'Tips & Gaya Hidup Sehat',
            'content' => 'Era digital membawa banyak kemudahan, namun juga tantangan bagi kesehatan mental. Berikut 5 cara efektif untuk menjaga kesehatan mental: 1) Batasi waktu layar dan media sosial, 2) Luangkan waktu untuk aktivitas offline seperti olahraga dan hobi, 3) Jaga kualitas tidur dengan menghindari gadget sebelum tidur, 4) Praktikkan mindfulness dan meditasi, 5) Jaga hubungan sosial yang bermakna dengan keluarga dan teman.',
            'source' => 'Kemenkes RI',
            'verified' => true,
            'published_at' => now()->subDays(10),
         ],
         [
            'title' => 'Mengenal Gejala Awal Penyakit Jantung yang Sering Diabaikan',
            'category' => 'Pencegahan & Perawatan',
            'content' => 'Penyakit jantung seringkali datang tanpa gejala yang jelas. Namun, ada beberapa tanda awal yang sering diabaikan: nyeri dada ringan, sesak napas saat beraktivitas, mudah lelah, pembengkakan pada kaki, dan detak jantung tidak teratur. Jika Anda mengalami gejala-gejala ini, segera periksakan diri ke dokter. Deteksi dini sangat penting untuk penanganan yang efektif.',
            'source' => 'PERKI',
            'verified' => true,
            'published_at' => now()->subDays(12),
         ],
         [
            'title' => 'Pentingnya Vaksinasi untuk Mencegah Penyakit Berbahaya',
            'category' => 'Pencegahan & Perawatan',
            'content' => 'Vaksinasi adalah salah satu pencapaian terbesar dalam bidang kesehatan masyarakat. Vaksin bekerja dengan merangsang sistem kekebalan tubuh untuk melawan penyakit tanpa menyebabkan penyakit tersebut. Program imunisasi telah berhasil mengendalikan berbagai penyakit berbahaya seperti polio, campak, dan difteri. Pastikan Anda dan keluarga mendapatkan vaksinasi lengkap sesuai jadwal yang direkomendasikan.',
            'source' => 'IDAI',
            'verified' => true,
            'published_at' => now()->subDays(15),
         ],
      ];

      foreach ($articles as $article) {
         HealthArticle::create([
            'title' => $article['title'],
            'slug' => Str::slug($article['title']),
            'content' => $article['content'],
            'category' => $article['category'],
            'source' => $article['source'],
            'verified' => $article['verified'],
            'published_at' => $article['published_at'],
         ]);
      }
   }
}
