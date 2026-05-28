import { pageState } from './inertia';

export const getMockArticles = () => [
    {
        id: 1,
        title: 'Langkah Tepat Mencegah Sakit Lambung (Maag) Kambuh Kembali',
        slug: 'mencegah-sakit-lambung',
        category: 'Tips & Gaya Hidup Sehat',
        content: `
            <p class="mb-4 text-justify">Sakit lambung atau gastritis adalah peradangan pada lapisan pelindung lambung. Kondisi ini sangat sering kambuh, terutama saat penderita mengalami stres emosional, kelelahan, atau pola makan yang tidak teratur.</p>
            <p class="mb-4 text-justify">Berikut beberapa langkah praktis yang dapat dilakukan untuk mencegah maag kambuh kembali:</p>
            <h3 class="text-lg font-semibold mt-6 mb-2 text-[#1b1b18]">1. Pola Makan Teratur dan Porsi Kecil</h3>
            <p class="mb-4 text-justify">Hindari makan dalam porsi besar sekaligus karena dapat meregangkan lambung dan merangsang produksi asam lambung berlebih. Cobalah pola makan <strong>small but frequent</strong> (porsi kecil namun sering, misalnya 5-6 kali sehari).</p>
            <h3 class="text-lg font-semibold mt-6 mb-2 text-[#1b1b18]">2. Hindari Makanan Pemicu</h3>
            <p class="mb-4 text-justify">Beberapa makanan terbukti memicu produksi asam lambung atau mengiritasi dinding lambung. Batasi atau hindari makanan yang terlalu pedas, sangat asam (seperti jeruk masam dan cuka), bersantan kental, berminyak tinggi (gorengan), cokelat, kafein (kopi dan teh), serta minuman berkarbonasi.</p>
            <h3 class="text-lg font-semibold mt-6 mb-2 text-[#1b1b18]">3. Jangan Langsung Berbaring Setelah Makan</h3>
            <p class="mb-4 text-justify">Gaya gravitasi membantu menjaga asam lambung tetap di tempatnya. Jika Anda langsung berbaring setelah makan, asam lambung akan lebih mudah naik ke esofagus (kerongkongan) yang memicu sensasi terbakar di dada (heartburn). Berikan jeda minimal 2 sampai 3 jam setelah makan sebelum tidur.</p>
            <h3 class="text-lg font-semibold mt-6 mb-2 text-[#1b1b18]">4. Kelola Stres dengan Baik</h3>
            <p class="mb-4 text-justify">Hubungan antara otak dan saluran pencernaan sangat kuat (disebut <em>brain-gut axis</em>). Ketika Anda stres, tubuh akan melepaskan hormon yang memicu peningkatan sekresi asam lambung. Lakukan latihan pernapasan, yoga, atau meditasi ringan setiap hari.</p>
        `,
        source: 'Kementerian Kesehatan Republik Indonesia (Kemkes)',
        published_at: '2026-05-20T08:00:00Z'
    },
    {
        id: 2,
        title: 'Pentingnya Menjaga Kualitas Tidur Demi Kesehatan Mental Kita',
        slug: 'kualitas-tidur-kesehatan-mental',
        category: 'Edukasi Kesehatan',
        content: `
            <p class="mb-4 text-justify">Tidur bukan sekadar mematikan kesadaran dan mengistirahatkan otot tubuh. Bagi otak, tidur merupakan fase krusial untuk melakukan "pembersihan" sisa metabolisme, mengonsolidasikan memori, serta menyaring stres dan kecemasan emosional yang dialami sepanjang hari.</p>
            <p class="mb-4 text-justify">Kurang tidur kronis terbukti meningkatkan hormon kortisol (hormon stres) dan menurunkan kemampuan regulasi emosi, sehingga meningkatkan risiko gangguan kecemasan hingga depresi berat.</p>
            <h3 class="text-lg font-semibold mt-6 mb-2 text-[#1b1b18]">Berapa Lama Waktu Tidur yang Cukup?</h3>
            <p class="mb-4 text-justify">Untuk orang dewasa usia 18-64 tahun, organisasi kesehatan dunia merekomendasikan tidur malam berkualitas berkisar antara <strong>7 hingga 9 jam</strong> setiap hari.</p>
            <h3 class="text-lg font-semibold mt-6 mb-2 text-[#1b1b18]">Tips Praktis Meningkatkan Kualitas Tidur (Sleep Hygiene):</h3>
            <ul class="list-disc pl-6 mb-4 space-y-2">
                <li><strong>Jadwal Konsisten:</strong> Tidurlah dan bangun pada jam yang sama setiap hari, termasuk di hari libur. Ini membantu mengatur jam biologis tubuh (ritme sirkadian).</li>
                <li><strong>Bebas Gadget Sebelum Tidur:</strong> Matikan HP, tablet, dan TV minimal 1 jam sebelum memejamkan mata. Cahaya biru (blue light) layar menghambat produksi melatonin, hormon alami yang memicu rasa kantuk.</li>
                <li><strong>Kondisi Kamar yang Mendukung:</strong> Atur suhu kamar agar terasa sejuk, pastikan kasur dan bantal nyaman, serta matikan lampu kamar secara total agar tidur lebih lelap.</li>
            </ul>
        `,
        source: 'WHO (World Health Organization)',
        published_at: '2026-05-25T14:30:00Z'
    },
    {
        id: 3,
        title: 'Mengenal Bahaya Obesitas dan Cara Menghitung BMI Secara Mandiri',
        slug: 'mengenal-bahaya-obesitas-bmi',
        category: 'Pencegahan & Perawatan',
        content: `
            <p class="mb-4 text-justify">Obesitas didefinisikan sebagai penumpukan lemak tubuh yang berlebih akibat ketidakseimbangan kronis antara asupan kalori makanan dengan energi yang dikeluarkan melalui aktivitas fisik.</p>
            <p class="mb-4 text-justify">Kondisi ini merupakan salah satu faktor risiko utama pemicu berbagai penyakit tidak menular (PTM) yang berbahaya, seperti penyakit jantung koroner, stroke, diabetes melitus tipe 2, osteoartritis, hingga hipertensi.</p>
            <h3 class="text-lg font-semibold mt-6 mb-2 text-[#1b1b18]">Cara Menghitung Body Mass Index (BMI) / Indeks Massa Tubuh (IMT)</h3>
            <p class="mb-4 text-justify">Anda dapat mengukur status berat badan Anda secara mandiri di rumah menggunakan rumus BMI standar:</p>
            <div class="bg-gray-100 p-4 rounded-lg my-4 text-center font-mono text-sm border border-gray-200">
                BMI = Berat Badan (kg) / [Tinggi Badan (m) x Tinggi Badan (m)]
            </div>
            <p class="mb-4 text-justify">Contoh: Jika berat badan Anda 68 kg dengan tinggi badan 170 cm (1.70 m), cara menghitungnya:</p>
            <div class="bg-gray-100 p-4 rounded-lg my-4 text-center font-mono text-sm border border-gray-200">
                BMI = 68 / (1.70 * 1.70) = 68 / 2.89 = 23.53 (Normal)
            </div>
            <h3 class="text-lg font-semibold mt-6 mb-2 text-[#1b1b18]">Kategori BMI Klasifikasi Asia-Pasifik (Kemenkes RI):</h3>
            <ul class="list-disc pl-6 mb-4 space-y-1">
                <li><strong>Kurang dari 18.5:</strong> Berat badan kurang (Underweight)</li>
                <li><strong>18.5 – 25.0:</strong> Normal (Ideal)</li>
                <li><strong>25.1 – 27.0:</strong> Kelebihan berat badan (Overweight)</li>
                <li><strong>Lebih dari 27.0:</strong> Obesitas (Obese)</li>
            </ul>
        `,
        source: 'Direktorat P2PTM Kemenkes RI',
        published_at: '2026-05-27T09:15:00Z'
    }
];
export const getMockDrugs = () => [
    {
        id: 1,
        name: 'Paracetamol 500mg',
        category: 'Analgesik',
        description: 'Paracetamol (Acetaminophen) digunakan secara luas untuk meredakan nyeri ringan hingga sedang (seperti sakit kepala, sakit gigi, nyeri otot, dan nyeri pasca operasi) serta efektif sebagai penurun demam.',
        usage: 'Dewasa: 1-2 tablet (500mg - 1000mg), dikonsumsi 3-4 kali sehari jika diperlukan (maksimal 4 gram atau 8 tablet sehari). Anak-anak 6-12 tahun: 1/2 - 1 tablet, 3-4 kali sehari. Sebaiknya diminum setelah makan.',
        dosage_info: 'Dewasa: 500 mg - 1000 mg per kali konsumsi, maksimal 4000 mg per hari.\nAnak-anak 6-12 tahun: 250 mg - 500 mg per kali konsumsi, maksimal 2000 mg per hari.',
        side_effects: 'Sangat jarang menimbulkan efek samping jika dikonsumsi sesuai dosis yang dianjurkan. Pada dosis berlebih (overdosis), obat ini dapat menyebabkan kerusakan serius pada organ hati.',
        warnings: 'Jangan dikonsumsi melebihi dosis yang dianjurkan. Hati-hati penggunaan pada penderita gangguan fungsi hati dan ginjal. Hindari konsumsi alkohol selama menggunakan obat ini.',
        pregnancy_safe: true,
        type: 'Obat Bebas',
        prices: [
            { id: 1, pharmacy_name: 'Apotek Kimia Farma', price_min: 5000, price_max: 7500 },
            { id: 2, pharmacy_name: 'Apotek K-24', price_min: 4800, price_max: 7200 },
            { id: 3, pharmacy_name: 'Apotek Century', price_min: 5200, price_max: 8000 }
        ]
    },
    {
        id: 2,
        name: 'Amoxicillin 500mg',
        category: 'Antibiotik',
        description: 'Amoxicillin adalah obat antibiotik golongan penisilin yang berfungsi membunuh bakteri penyebab berbagai macam infeksi, seperti infeksi saluran pernapasan (pneumonia, bronkitis), infeksi telinga, kulit, hidung, tenggorokan, dan saluran kemih.',
        usage: 'Harus dikonsumsi sesuai dengan petunjuk dan resep dokter. Penggunaannya wajib dihabiskan meskipun gejala sakit sudah hilang untuk mencegah terjadinya resistensi bakteri.',
        dosage_info: 'Harus dengan resep dokter.\nDewasa: 250 mg - 500 mg setiap 8 jam.\nAnak-anak: 20-40 mg/kg berat badan per hari, dibagi menjadi 3 kali dosis pemberian.',
        side_effects: 'Efek samping yang umum meliputi mual, diare, sakit perut, ruam kulit ringan, atau sariawan di mulut.',
        warnings: 'Hati-hati pada pasien yang memiliki riwayat alergi terhadap antibiotik golongan Penicillin atau Sefalosporin. Wajib dihabiskan sesuai resep dokter.',
        pregnancy_safe: true,
        type: 'Obat Keras',
        prices: [
            { id: 4, pharmacy_name: 'Apotek Kimia Farma', price_min: 12000, price_max: 18000 },
            { id: 5, pharmacy_name: 'Apotek K-24', price_min: 11500, price_max: 17000 },
            { id: 6, pharmacy_name: 'Apotek Century', price_min: 13000, price_max: 19500 }
        ]
    },
    {
        id: 3,
        name: 'Antasida DOEN',
        category: 'Antasida',
        description: 'Antasida DOEN merupakan kombinasi Aluminium Hidroksida dan Magnesium Hidroksida yang bekerja sinergis menetralkan asam lambung yang berlebih. Sangat efektif mengurangi gejala sakit maag, nyeri ulu hati, mual, perut kembung, dan rasa perih di lambung.',
        usage: 'Dewasa: 1-2 tablet kunyah, dikonsumsi 3-4 kali sehari. Anak-anak 6-12 tahun: 1/2 - 1 tablet, 3-4 kali sehari. Diminum 1-2 jam sebelum makan atau saat menjelang tidur malam. Wajib dikunyah terlebih dahulu sebelum ditelan.',
        dosage_info: 'Dewasa: 1-2 tablet kunyah, 3-4 kali sehari.\nAnak 6-12 tahun: 1/2-1 tablet, 3-4 kali sehari.\nDiminum saat perut kosong (1-2 jam sebelum makan) atau menjelang tidur.',
        side_effects: 'Efek samping jarang terjadi, dapat berupa sembelit (konstipasi) akibat kandungan Aluminium, atau diare ringan akibat kandungan Magnesium.',
        warnings: 'Hindari penggunaan jangka panjang (lebih dari 2 minggu) tanpa instruksi dokter. Hati-hati pada penderita gangguan ginjal dan pasien yang sedang menjalani diet rendah fosfor.',
        pregnancy_safe: false,
        type: 'Obat Bebas',
        prices: [
            { id: 7, pharmacy_name: 'Apotek Kimia Farma', price_min: 3500, price_max: 5000 },
            { id: 8, pharmacy_name: 'Apotek K-24', price_min: 3200, price_max: 4800 },
            { id: 9, pharmacy_name: 'Apotek Century', price_min: 3800, price_max: 5500 }
        ]
    }
];
export const getInitialHealthDashboardProps = () => {
    // Check local storage
    const storedPhysical = localStorage.getItem('docdot_physical_logs');
    const storedMental = localStorage.getItem('docdot_mental_logs');
    const storedProfile = localStorage.getItem('docdot_user_profile');
    
    const physicalLogs = storedPhysical ? JSON.parse(storedPhysical) : [
        { id: 101, weight_kg: 68.5, blood_pressure: '120/80', activity_minutes: 30, logged_at: '2026-05-26' },
        { id: 102, weight_kg: 68.3, blood_pressure: '118/79', activity_minutes: 45, logged_at: '2026-05-27' },
        { id: 103, weight_kg: 68.0, blood_pressure: '121/80', activity_minutes: 20, logged_at: '2026-05-28' }
    ];
    
    const mentalLogs = storedMental ? JSON.parse(storedMental) : [
        { id: 201, mood: 4, stress_level: 2, sleep_hours: 8, note: 'Tidur nyenyak, bangun terasa segar!', logged_at: '2026-05-26' },
        { id: 202, mood: 3, stress_level: 3, sleep_hours: 6.5, note: 'Banyak tugas kantor hari ini, tubuh agak capek.', logged_at: '2026-05-27' },
        { id: 203, mood: 5, stress_level: 1, sleep_hours: 8.5, note: 'Luar biasa! Tidur sangat lelap dan sempat olahraga pagi.', logged_at: '2026-05-28' }
    ];

    const profile = storedProfile ? JSON.parse(storedProfile) : {
        height: 170,
        weight: 68.0,
        bmi: 23.5,
        bmi_category: 'Normal'
    };

    // Calculate dynamic stats
    const avg_mood = mentalLogs.length > 0
        ? mentalLogs.reduce((acc: number, log: any) => acc + Number(log.mood), 0) / mentalLogs.length
        : 3;
    const avg_stress = mentalLogs.length > 0
        ? mentalLogs.reduce((acc: number, log: any) => acc + Number(log.stress_level), 0) / mentalLogs.length
        : 3;
    const avg_sleep = mentalLogs.length > 0
        ? mentalLogs.reduce((acc: number, log: any) => acc + Number(log.sleep_hours || 0), 0) / mentalLogs.length
        : 7;
    const total_activity_minutes = physicalLogs.reduce((acc: number, log: any) => acc + Number(log.activity_minutes || 0), 0);

    return {
        profile,
        physicalLogs: physicalLogs.sort((a: any, b: any) => b.logged_at.localeCompare(a.logged_at)),
        mentalLogs: mentalLogs.sort((a: any, b: any) => b.logged_at.localeCompare(a.logged_at)),
        insights: [
            { id: 1, type: 'Kebugaran', summary: `Keren! Total aktivitas fisik Anda minggu ini sudah mencapai ${total_activity_minutes} menit. Target minimal mingguan 75 menit telah terlampaui.`, risk_level: 'Low', created_at: '2026-05-28' },
            { id: 2, type: 'Indeks Massa Tubuh', summary: `BMI Anda berada di angka ${profile.bmi} (${profile.bmi_category}). Jaga terus pola makan sehat seimbang Anda.`, risk_level: 'Low', created_at: '2026-05-28' },
            { id: 3, type: 'Stres & Tidur', summary: `Rata-rata tidur Anda sangat ideal (${avg_sleep.toFixed(1)} jam). Tingkat stres rata-rata terkendali di skala ${avg_stress.toFixed(1)}/5.`, risk_level: 'Low', created_at: '2026-05-28' }
        ],
        stats: {
            avg_mood,
            avg_stress,
            avg_sleep,
            total_activity_minutes,
            logs_this_week: physicalLogs.length + mentalLogs.length
        }
    };
};
