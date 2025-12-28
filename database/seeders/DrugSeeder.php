<?php

namespace Database\Seeders;

use App\Models\Drug;
use App\Models\DrugPrice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DrugSeeder extends Seeder
{
   /**
    * Run the database seeds.
    */
   public function run(): void
   {
      $drugs = [
         // Analgesik (Pain Relievers)
         [
            'name' => 'Paracetamol 500mg',
            'category' => 'Analgesik',
            'description' => 'Paracetamol adalah obat pereda nyeri dan penurun demam yang paling umum digunakan. Efektif untuk meredakan sakit kepala ringan hingga sedang, nyeri otot, nyeri punggung, sakit gigi, dan demam.',
            'dosage_info' => "Dewasa & Anak >12 tahun:\n• 500mg - 1000mg setiap 4-6 jam\n• Maksimum 4000mg per hari\n\nAnak 6-12 tahun:\n• 250mg - 500mg setiap 4-6 jam\n• Maksimum 2000mg per hari\n\nAnak <6 tahun:\n• Konsultasikan dengan dokter",
            'side_effects' => "Efek samping jarang terjadi jika digunakan sesuai dosis:\n• Mual\n• Ruam kulit\n• Reaksi alergi (jarang)\n\nDosis berlebih dapat menyebabkan kerusakan hati.",
            'warnings' => "• Jangan melebihi dosis yang dianjurkan\n• Hindari alkohol saat menggunakan obat ini\n• Hati-hati pada pasien dengan gangguan hati\n• Konsultasikan dengan dokter jika demam berlangsung >3 hari",
            'pregnancy_safe' => true,
            'prices' => [
               ['pharmacy_name' => 'Apotek K-24', 'price_min' => 2000, 'price_max' => 2500],
               ['pharmacy_name' => 'Apotek Kimia Farma', 'price_min' => 1800, 'price_max' => 2200],
               ['pharmacy_name' => 'Century Healthcare', 'price_min' => 2200, 'price_max' => 2800],
            ],
         ],
         [
            'name' => 'Ibuprofen 400mg',
            'category' => 'Analgesik',
            'description' => 'Ibuprofen adalah obat antiinflamasi nonsteroid (NSAID) yang digunakan untuk meredakan nyeri, mengurangi peradangan, dan menurunkan demam. Efektif untuk nyeri otot, sakit kepala, nyeri haid, dan arthritis.',
            'dosage_info' => "Dewasa:\n• 200mg - 400mg setiap 4-6 jam\n• Maksimum 1200mg per hari (tanpa resep)\n• Maksimum 3200mg per hari (dengan resep)\n\nAnak >6 bulan:\n• 5-10mg/kg setiap 6-8 jam\n• Konsultasikan dengan dokter",
            'side_effects' => "• Gangguan pencernaan, mual, muntah\n• Sakit perut, heartburn\n• Pusing, sakit kepala\n• Ruam kulit\n• Risiko pendarahan lambung jika penggunaan jangka panjang",
            'warnings' => "• Tidak disarankan untuk ibu hamil terutama trimester ketiga\n• Hindari jika memiliki riwayat tukak lambung\n• Gunakan dengan makanan untuk mengurangi iritasi lambung\n• Hati-hati pada pasien dengan gangguan ginjal atau jantung",
            'pregnancy_safe' => false,
            'prices' => [
               ['pharmacy_name' => 'Apotek K-24', 'price_min' => 3500, 'price_max' => 4500],
               ['pharmacy_name' => 'Apotek Kimia Farma', 'price_min' => 3200, 'price_max' => 4000],
            ],
         ],

         // Antibiotik
         [
            'name' => 'Amoxicillin 500mg',
            'category' => 'Antibiotik',
            'description' => 'Amoxicillin adalah antibiotik golongan penicillin yang digunakan untuk mengobati berbagai infeksi bakteri seperti infeksi saluran pernapasan, infeksi telinga, infeksi saluran kemih, dan infeksi kulit.',
            'dosage_info' => "Dewasa & Anak >40kg:\n• 250mg - 500mg setiap 8 jam\n• Atau 500mg - 875mg setiap 12 jam\n• Durasi: 7-14 hari tergantung infeksi\n\nAnak <40kg:\n• 20-45mg/kg/hari dibagi dalam 2-3 dosis\n• Konsultasikan dengan dokter",
            'side_effects' => "• Diare, mual, muntah\n• Ruam kulit\n• Sakit perut\n• Reaksi alergi (segera hentikan jika terjadi)\n• Kandidiasis (infeksi jamur) pada penggunaan lama",
            'warnings' => "• Hanya gunakan dengan resep dokter\n• Habiskan seluruh antibiotik meski gejala membaik\n• Jangan digunakan jika alergi penicillin\n• Informasikan dokter tentang riwayat alergi obat",
            'pregnancy_safe' => true,
            'prices' => [
               ['pharmacy_name' => 'Apotek K-24', 'price_min' => 5000, 'price_max' => 7000],
               ['pharmacy_name' => 'Apotek Kimia Farma', 'price_min' => 4500, 'price_max' => 6500],
               ['pharmacy_name' => 'Guardian', 'price_min' => 5500, 'price_max' => 7500],
            ],
         ],
         [
            'name' => 'Azithromycin 500mg',
            'category' => 'Antibiotik',
            'description' => 'Azithromycin adalah antibiotik golongan macrolide yang efektif untuk infeksi bakteri saluran pernapasan, kulit, telinga, dan infeksi menular seksual. Memiliki keunggulan dosis sekali sehari.',
            'dosage_info' => "Dewasa:\n• 500mg sekali sehari pada hari pertama\n• Dilanjutkan 250mg sekali sehari selama 4 hari\n• Atau 500mg sekali sehari selama 3 hari\n\nAnak:\n• 10mg/kg pada hari pertama\n• 5mg/kg per hari selama 4 hari berikutnya",
            'side_effects' => "• Mual, muntah, diare\n• Sakit perut\n• Sakit kepala\n• Gangguan pendengaran (jarang)\n• Aritmia jantung (sangat jarang)",
            'warnings' => "• Hanya gunakan dengan resep dokter\n• Hati-hati pada pasien dengan gangguan hati\n• Dapat memperpanjang interval QT\n• Informasikan dokter jika menggunakan obat jantung",
            'pregnancy_safe' => true,
            'prices' => [
               ['pharmacy_name' => 'Apotek K-24', 'price_min' => 15000, 'price_max' => 20000],
               ['pharmacy_name' => 'Apotek Kimia Farma', 'price_min' => 14000, 'price_max' => 18000],
            ],
         ],

         // Antihistamin
         [
            'name' => 'Cetirizine 10mg',
            'category' => 'Antihistamin',
            'description' => 'Cetirizine adalah antihistamin generasi kedua yang digunakan untuk meredakan gejala alergi seperti bersin, gatal, mata berair, dan ruam kulit. Lebih tidak mengantuk dibanding antihistamin generasi pertama.',
            'dosage_info' => "Dewasa & Anak >12 tahun:\n• 10mg sekali sehari\n• Dapat dibagi 5mg dua kali sehari\n\nAnak 6-12 tahun:\n• 5mg - 10mg sekali sehari\n\nAnak 2-6 tahun:\n• 2.5mg - 5mg sekali sehari\n• Atau 2.5mg dua kali sehari",
            'side_effects' => "• Mengantuk (lebih ringan dari antihistamin lama)\n• Mulut kering\n• Pusing\n• Sakit kepala\n• Kelelahan",
            'warnings' => "• Dapat menyebabkan kantuk, hindari mengemudi jika terpengaruh\n• Hati-hati pada pasien dengan gangguan ginjal\n• Konsultasikan dengan dokter sebelum digunakan ibu hamil/menyusui",
            'pregnancy_safe' => false,
            'prices' => [
               ['pharmacy_name' => 'Apotek K-24', 'price_min' => 1500, 'price_max' => 2500],
               ['pharmacy_name' => 'Apotek Kimia Farma', 'price_min' => 1200, 'price_max' => 2000],
               ['pharmacy_name' => 'Century Healthcare', 'price_min' => 1800, 'price_max' => 2800],
            ],
         ],
         [
            'name' => 'Loratadine 10mg',
            'category' => 'Antihistamin',
            'description' => 'Loratadine adalah antihistamin non-sedatif yang efektif untuk alergi ringan hingga sedang termasuk rhinitis alergi, urtikaria (biduran), dan reaksi alergi kulit lainnya.',
            'dosage_info' => "Dewasa & Anak >12 tahun:\n• 10mg sekali sehari\n\nAnak 2-12 tahun:\n• Berat >30kg: 10mg sekali sehari\n• Berat ≤30kg: 5mg sekali sehari",
            'side_effects' => "• Sakit kepala\n• Mulut kering\n• Mengantuk (jarang)\n• Gangguan pencernaan",
            'warnings' => "• Kategori B untuk kehamilan - konsultasikan dengan dokter\n• Masuk ke ASI dalam jumlah kecil\n• Penyesuaian dosis pada gangguan hati berat",
            'pregnancy_safe' => true,
            'prices' => [
               ['pharmacy_name' => 'Apotek K-24', 'price_min' => 2000, 'price_max' => 3000],
               ['pharmacy_name' => 'Guardian', 'price_min' => 2500, 'price_max' => 3500],
            ],
         ],

         // Antasida
         [
            'name' => 'Antasida Doen',
            'category' => 'Antasida',
            'description' => 'Kombinasi aluminium hidroksida dan magnesium hidroksida untuk menetralkan asam lambung. Efektif meredakan maag, heartburn, dan gangguan pencernaan akibat asam.',
            'dosage_info' => "Dewasa:\n• 1-2 tablet dikunyah setelah makan\n• Dapat diulang setiap 4 jam bila perlu\n• Maksimum 8 tablet per hari\n\nAnak:\n• Konsultasikan dengan dokter",
            'side_effects' => "• Konstipasi (dari aluminium)\n• Diare (dari magnesium)\n• Mual\n• Penggunaan jangka panjang: gangguan keseimbangan elektrolit",
            'warnings' => "• Tidak untuk penggunaan jangka panjang tanpa pengawasan dokter\n• Jangan digunakan bersamaan dengan obat lain (beri jarak 2 jam)\n• Hati-hati pada pasien dengan gangguan ginjal",
            'pregnancy_safe' => true,
            'prices' => [
               ['pharmacy_name' => 'Apotek K-24', 'price_min' => 3000, 'price_max' => 4000],
               ['pharmacy_name' => 'Apotek Kimia Farma', 'price_min' => 2500, 'price_max' => 3500],
            ],
         ],
         [
            'name' => 'Omeprazole 20mg',
            'category' => 'Antasida',
            'description' => 'Omeprazole adalah proton pump inhibitor (PPI) yang mengurangi produksi asam lambung. Digunakan untuk GERD, tukak lambung, tukak duodenum, dan sindrom Zollinger-Ellison.',
            'dosage_info' => "GERD:\n• 20mg sekali sehari selama 4-8 minggu\n\nTukak duodenum:\n• 20mg sekali sehari selama 2-4 minggu\n\nTukak lambung:\n• 20mg sekali sehari selama 4-8 minggu\n\nDiminum sebelum makan (idealnya pagi hari)",
            'side_effects' => "• Sakit kepala\n• Diare, konstipasi\n• Mual, muntah\n• Perut kembung\n• Penggunaan jangka panjang: risiko defisiensi B12 dan magnesium",
            'warnings' => "• Konsultasikan dengan dokter untuk penggunaan >14 hari\n• Hati-hati pada kehamilan (kategori C)\n• Dapat menutupi gejala kanker lambung\n• Interaksi dengan clopidogrel",
            'pregnancy_safe' => false,
            'prices' => [
               ['pharmacy_name' => 'Apotek K-24', 'price_min' => 2000, 'price_max' => 3500],
               ['pharmacy_name' => 'Apotek Kimia Farma', 'price_min' => 1800, 'price_max' => 3000],
               ['pharmacy_name' => 'Century Healthcare', 'price_min' => 2500, 'price_max' => 4000],
            ],
         ],

         // Vitamin
         [
            'name' => 'Vitamin C 500mg',
            'category' => 'Vitamin',
            'description' => 'Vitamin C (asam askorbat) adalah suplemen untuk meningkatkan daya tahan tubuh, sebagai antioksidan, dan membantu penyerapan zat besi. Penting untuk kesehatan kulit, tulang, dan sistem kekebalan.',
            'dosage_info' => "Dewasa:\n• Kebutuhan harian: 75-90mg\n• Suplemen: 500mg - 1000mg per hari\n\nAnak:\n• 1-3 tahun: 15mg/hari\n• 4-8 tahun: 25mg/hari\n• 9-13 tahun: 45mg/hari\n• 14-18 tahun: 65-75mg/hari",
            'side_effects' => "Dosis tinggi (>2000mg/hari):\n• Gangguan pencernaan\n• Diare\n• Mual\n• Batu ginjal (pada yang rentan)\n• Heartburn",
            'warnings' => "• Dosis sangat tinggi dapat menyebabkan batu ginjal\n• Hati-hati pada pasien dengan riwayat batu ginjal\n• Dapat mempengaruhi hasil tes gula darah",
            'pregnancy_safe' => true,
            'prices' => [
               ['pharmacy_name' => 'Apotek K-24', 'price_min' => 8000, 'price_max' => 15000],
               ['pharmacy_name' => 'Apotek Kimia Farma', 'price_min' => 7000, 'price_max' => 12000],
               ['pharmacy_name' => 'Guardian', 'price_min' => 10000, 'price_max' => 20000],
            ],
         ],
         [
            'name' => 'Vitamin D3 1000 IU',
            'category' => 'Vitamin',
            'description' => 'Vitamin D3 (cholecalciferol) penting untuk kesehatan tulang, penyerapan kalsium, dan fungsi sistem imun. Membantu mencegah osteoporosis dan mendukung kesehatan otot.',
            'dosage_info' => "Dewasa:\n• Pemeliharaan: 600-800 IU per hari\n• Defisiensi: 1000-4000 IU per hari\n\nLansia:\n• 800-2000 IU per hari\n\nIdealnya dikonsumsi bersama makanan berlemak untuk penyerapan optimal",
            'side_effects' => "Dosis sangat tinggi (toksisitas):\n• Mual, muntah\n• Kelemahan otot\n• Haus berlebihan\n• Sering buang air kecil\n• Hypercalcemia",
            'warnings' => "• Jangan melebihi 4000 IU per hari tanpa pengawasan dokter\n• Periksa kadar vitamin D darah secara berkala\n• Interaksi dengan beberapa obat (steroid, cholestyramine)",
            'pregnancy_safe' => true,
            'prices' => [
               ['pharmacy_name' => 'Apotek K-24', 'price_min' => 50000, 'price_max' => 80000],
               ['pharmacy_name' => 'Century Healthcare', 'price_min' => 55000, 'price_max' => 90000],
            ],
         ],

         // Obat Batuk
         [
            'name' => 'OBH Combi',
            'category' => 'Obat Batuk',
            'description' => 'OBH Combi adalah obat batuk hitam kombinasi yang mengandung ekspektoran dan antitusif untuk meredakan batuk berdahak dan batuk kering. Membantu mengencerkan dahak dan melegakan tenggorokan.',
            'dosage_info' => "Dewasa:\n• 3 sendok takar (15ml) 3 kali sehari\n\nAnak 6-12 tahun:\n• 2 sendok takar (10ml) 3 kali sehari\n\nAnak 2-6 tahun:\n• 1 sendok takar (5ml) 3 kali sehari",
            'side_effects' => "• Mengantuk\n• Pusing\n• Mual\n• Konstipasi\n• Mulut kering",
            'warnings' => "• Dapat menyebabkan kantuk\n• Tidak direkomendasikan untuk anak <2 tahun\n• Hindari alkohol\n• Konsultasikan dengan dokter jika batuk >7 hari",
            'pregnancy_safe' => false,
            'prices' => [
               ['pharmacy_name' => 'Apotek K-24', 'price_min' => 15000, 'price_max' => 22000],
               ['pharmacy_name' => 'Apotek Kimia Farma', 'price_min' => 14000, 'price_max' => 20000],
            ],
         ],
         [
            'name' => 'Ambroxol 30mg',
            'category' => 'Obat Batuk',
            'description' => 'Ambroxol adalah mukolitik yang membantu mengencerkan dahak dan memudahkan pengeluarannya. Efektif untuk batuk berdahak akibat infeksi saluran pernapasan.',
            'dosage_info' => "Dewasa & Anak >12 tahun:\n• 30mg 2-3 kali sehari\n• Dapat dikurangi menjadi 30mg 2 kali sehari setelah perbaikan\n\nAnak 6-12 tahun:\n• 15mg 2-3 kali sehari\n\nAnak 2-6 tahun:\n• 7.5mg 3 kali sehari",
            'side_effects' => "• Mual, muntah\n• Diare\n• Gangguan pencernaan\n• Reaksi alergi (jarang)\n• Gangguan pengecapan",
            'warnings' => "• Minum banyak air untuk membantu mengencerkan dahak\n• Hati-hati pada pasien dengan tukak lambung\n• Konsultasikan dengan dokter untuk ibu hamil trimester pertama",
            'pregnancy_safe' => true,
            'prices' => [
               ['pharmacy_name' => 'Apotek K-24', 'price_min' => 3000, 'price_max' => 5000],
               ['pharmacy_name' => 'Guardian', 'price_min' => 3500, 'price_max' => 5500],
            ],
         ],

         // Obat Flu
         [
            'name' => 'Panadol Flu & Batuk',
            'category' => 'Obat Flu',
            'description' => 'Kombinasi paracetamol, phenylephrine, dan dextromethorphan untuk meredakan gejala flu seperti demam, sakit kepala, hidung tersumbat, dan batuk kering.',
            'dosage_info' => "Dewasa & Anak >12 tahun:\n• 2 kaplet setiap 6 jam\n• Maksimum 8 kaplet per hari\n\nAnak 6-12 tahun:\n• 1 kaplet setiap 6 jam\n• Maksimum 4 kaplet per hari",
            'side_effects' => "• Mengantuk\n• Mulut kering\n• Jantung berdebar\n• Pusing\n• Insomnia",
            'warnings' => "• Dapat menyebabkan kantuk\n• Hindari alkohol\n• Tidak untuk anak <6 tahun\n• Hati-hati pada pasien hipertensi dan jantung\n• Tidak untuk ibu hamil/menyusui tanpa konsultasi dokter",
            'pregnancy_safe' => false,
            'prices' => [
               ['pharmacy_name' => 'Apotek K-24', 'price_min' => 12000, 'price_max' => 18000],
               ['pharmacy_name' => 'Apotek Kimia Farma', 'price_min' => 11000, 'price_max' => 16000],
               ['pharmacy_name' => 'Guardian', 'price_min' => 13000, 'price_max' => 20000],
            ],
         ],
         [
            'name' => 'Decolgen',
            'category' => 'Obat Flu',
            'description' => 'Decolgen adalah obat flu yang mengandung paracetamol, phenylpropanolamine, dan chlorpheniramine untuk meredakan demam, sakit kepala, hidung tersumbat, dan bersin-bersin.',
            'dosage_info' => "Dewasa & Anak >12 tahun:\n• 1 kaplet 3 kali sehari setelah makan\n\nAnak 6-12 tahun:\n• ½ kaplet 3 kali sehari setelah makan",
            'side_effects' => "• Mengantuk\n• Mulut kering\n• Pusing\n• Konstipasi\n• Gelisah",
            'warnings' => "• Dapat menyebabkan kantuk, hindari mengemudi\n• Tidak untuk anak <6 tahun\n• Hati-hati pada pasien hipertensi, diabetes, glaukoma\n• Tidak untuk ibu hamil/menyusui",
            'pregnancy_safe' => false,
            'prices' => [
               ['pharmacy_name' => 'Apotek K-24', 'price_min' => 5000, 'price_max' => 8000],
               ['pharmacy_name' => 'Apotek Kimia Farma', 'price_min' => 4500, 'price_max' => 7000],
            ],
         ],

         // Antiseptik
         [
            'name' => 'Betadine Solution 30ml',
            'category' => 'Antiseptik',
            'description' => 'Larutan povidone-iodine 10% untuk membersihkan dan mencegah infeksi pada luka kecil, luka bakar ringan, dan abrasi kulit. Membunuh bakteri, jamur, dan virus.',
            'dosage_info' => "Penggunaan topikal:\n• Bersihkan area luka dengan air\n• Oleskan betadine secukupnya\n• Dapat digunakan 2-3 kali sehari\n• Tutup dengan perban jika diperlukan",
            'side_effects' => "• Iritasi kulit lokal\n• Reaksi alergi (gatal, kemerahan)\n• Pewarnaan kulit sementara\n• Rasa perih pada luka terbuka",
            'warnings' => "• Hanya untuk penggunaan luar\n• Hindari kontak dengan mata\n• Tidak untuk luka dalam atau serius\n• Hentikan jika terjadi iritasi berlebihan\n• Hati-hati pada pasien dengan gangguan tiroid",
            'pregnancy_safe' => false,
            'prices' => [
               ['pharmacy_name' => 'Apotek K-24', 'price_min' => 18000, 'price_max' => 25000],
               ['pharmacy_name' => 'Apotek Kimia Farma', 'price_min' => 17000, 'price_max' => 23000],
               ['pharmacy_name' => 'Guardian', 'price_min' => 20000, 'price_max' => 28000],
            ],
         ],
         [
            'name' => 'Hansaplast Antiseptic Spray',
            'category' => 'Antiseptik',
            'description' => 'Spray antiseptik untuk membersihkan dan melindungi luka kecil dari infeksi. Praktis digunakan tanpa perlu menyentuh luka langsung.',
            'dosage_info' => "Penggunaan:\n• Bersihkan luka dari kotoran\n• Semprotkan dari jarak 10-15cm\n• Biarkan kering\n• Dapat diulang 2-3 kali sehari\n• Tutup dengan plester atau perban",
            'side_effects' => "• Iritasi kulit ringan\n• Rasa perih sementara\n• Reaksi alergi (jarang)",
            'warnings' => "• Hanya untuk penggunaan luar\n• Hindari kontak dengan mata dan membran mukosa\n• Jauhkan dari jangkauan anak-anak\n• Simpan di tempat sejuk dan kering",
            'pregnancy_safe' => true,
            'prices' => [
               ['pharmacy_name' => 'Apotek K-24', 'price_min' => 25000, 'price_max' => 35000],
               ['pharmacy_name' => 'Guardian', 'price_min' => 28000, 'price_max' => 38000],
            ],
         ],
      ];

      foreach ($drugs as $drugData) {
         $prices = $drugData['prices'];
         unset($drugData['prices']);

         $drug = Drug::create($drugData);

         foreach ($prices as $price) {
            $drug->prices()->create($price);
         }
      }
   }
}
