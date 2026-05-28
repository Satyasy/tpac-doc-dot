import { pageState } from './inertia';
import { getInitialHealthDashboardProps } from './mock-data';

interface MockResponse {
    data: any;
    status: number;
    statusText: string;
    headers: any;
    config: any;
}

const mockResponse = (data: any, status = 200): Promise<MockResponse> => {
    return new Promise((resolve) => {
        setTimeout(() => {
            resolve({
                data,
                status,
                statusText: 'OK',
                headers: {},
                config: {}
            });
        }, 600);
    });
};

const handleGet = (url: string) => {
    console.log(`[Mock Axios GET]: ${url}`);
    
    // 1. Consultation AI Chat messages
    if (url.includes('/consultation/session/') && url.endsWith('/messages')) {
        const sessionId = url.split('/')[3];
        const storedMessages = localStorage.getItem(`docdot_chat_${sessionId}`);
        
        if (storedMessages) {
            return mockResponse({ messages: JSON.parse(storedMessages) });
        } else {
            const initialMessages = [
                {
                    id: 1001,
                    sender: 'ai',
                    message: `Halo! Saya **DocDot AI**, asisten kesehatan pintar Anda. 🩺\n\nSilakan ceritakan keluhan kesehatan, gejala yang sedang dialami, atau tanyakan informasi medis apa pun yang Anda perlukan. \n\n*Contoh: "Saya merasa pusing sejak tadi pagi" atau "Berikan tips untuk maag."*`,
                    created_at: new Date().toISOString()
                }
            ];
            localStorage.setItem(`docdot_chat_${sessionId}`, JSON.stringify(initialMessages));
            return mockResponse({ messages: initialMessages });
        }
    }
    
    // 2. Doctor and Patient endpoints
    if (url === '/doctor-patient/doctors') {
        return mockResponse([
            { id: 1, name: 'dr. Andi Pratama, Sp.PD', specialization: 'Spesialis Penyakit Dalam', rating: 4.9, active: true },
            { id: 2, name: 'dr. Rina Sari, Sp.A', specialization: 'Spesialis Anak', rating: 4.8, active: true },
            { id: 3, name: 'dr. Budi Santoso, Sp.KJ', specialization: 'Spesialis Kejiwaan (Psikiater)', rating: 4.9, active: true },
            { id: 4, name: 'dr. Dewi Lestari, Sp.OG', specialization: 'Spesialis Kebidanan & Kandungan', rating: 4.7, active: true }
        ]);
    }
    
    if (url === '/doctor-patient/my-doctors') {
        const stored = localStorage.getItem('docdot_my_doctors') || '[]';
        return mockResponse(JSON.parse(stored));
    }
    
    if (url === '/doctor-patient/my-patients') {
        return mockResponse([
            { id: 201, name: 'Ahmad Faisal', gender: 'Laki-laki', age: 34, last_alert: 'Tekanan Darah Tinggi', date: '2026-05-28' },
            { id: 202, name: 'Siti Rahma', gender: 'Perempuan', age: 28, last_alert: 'Stres Tingkat Tinggi', date: '2026-05-27' }
        ]);
    }
    
    if (url === '/doctor-patient/pending-requests') {
        return mockResponse([
            { id: 301, patient: { name: 'Rudi Hermawan', email: 'rudi@gmail.com' }, created_at: new Date().toISOString() }
        ]);
    }
    
    if (url.includes('/doctor-patient/patient/')) {
        const id = url.split('/').pop();
        return mockResponse({
            id: Number(id),
            name: id === '201' ? 'Ahmad Faisal' : 'Siti Rahma',
            email: id === '201' ? 'ahmad.faisal@gmail.com' : 'siti.rahma@gmail.com',
            physical_logs: [
                { id: 501, weight_kg: 78.5, blood_pressure: '145/95', activity_minutes: 15, logged_at: '2026-05-28' },
                { id: 502, weight_kg: 79.0, blood_pressure: '142/90', activity_minutes: 20, logged_at: '2026-05-26' }
            ],
            mental_logs: [
                { id: 601, mood: 3, stress_level: 4, sleep_hours: 5.5, note: 'Tidur kurang nyenyak, pusing.', logged_at: '2026-05-28' }
            ]
        });
    }

    return mockResponse({});
};

const handlePost = (url: string, data?: any) => {
    console.log(`[Mock Axios POST]: ${url}`, data);
    
    // 1. Create chat session
    if (url === '/consultation/session') {
        return mockResponse({
            session: {
                id: 'session-' + Date.now(),
                title: 'Konsultasi ' + new Date().toLocaleDateString('id-ID', { hour: '2-digit', minute: '2-digit' }),
                created_at: new Date().toISOString()
            }
        });
    }
    
    // 2. AI Chat bot response logic
    if (url.includes('/consultation/session/') && url.endsWith('/message')) {
        const sessionId = url.split('/')[3];
        const userMsg = data?.message || '';
        
        let aiMsg = '';
        const lowerMsg = userMsg.toLowerCase();
        
        if (lowerMsg.includes('halo') || lowerMsg.includes('hi') || lowerMsg.includes('pagi') || lowerMsg.includes('siang') || lowerMsg.includes('malam') || lowerMsg.includes('assalamu')) {
            aiMsg = 'Halo! Saya **DocDot AI**. Saya siap membantu Anda menganalisis keluhan kesehatan secara cepat dan mandiri. Apa keluhan atau gejala yang sedang Anda rasakan saat ini?';
        } else if (lowerMsg.includes('pusing') || lowerMsg.includes('sakit kepala') || lowerMsg.includes('migrain')) {
            aiMsg = `Sakit kepala atau pusing dapat disebabkan oleh berbagai hal, di antaranya:
            
1. **Sakit Kepala Tegang (Tension Headache):** Paling sering terjadi, biasanya akibat stres, kelelahan mata, postur leher yang buruk, atau kurang tidur. Rasanya seperti kepala diikat kencang.
2. **Migrain:** Sakit kepala berdenyut, biasanya di satu sisi kepala, sering disertai sensitivitas terhadap cahaya/suara, dan kadang mual.
3. **Dehidrasi:** Tubuh kekurangan asupan cairan harian.
4. **Tekanan Darah Rendah atau Tinggi.**

**Rekomendasi Perawatan Awal Mandiri:**
* Istirahat di ruangan yang tenang dan minim cahaya.
* Penuhi konsumsi air putih (minimal 2-3 liter per hari).
* Hindari paparan layar gadget atau komputer terlalu lama.
* Kompres dahi atau tengkuk dengan handuk dingin/hangat untuk rileksasi.
* Jika nyeri sangat mengganggu, Anda dapat mengonsumsi obat bebas seperti *Paracetamol* sesuai dengan dosis anjuran.

*Penting: Jika sakit kepala muncul mendadak dengan intensitas sangat hebat, disertai demam tinggi, kaku pada leher, muntah menyembur, atau kelemahan pada anggota gerak, mohon segera hubungi fasilitas kesehatan terdekat.*`;
        } else if (lowerMsg.includes('demam') || lowerMsg.includes('panas') || lowerMsg.includes('menggigil')) {
            aiMsg = `Demam (suhu tubuh > 37,5°C) umumnya merupakan tanda respons imun alami tubuh yang sedang aktif melawan infeksi (virus, bakteri, atau agen asing).
            
Beberapa penyebab umum demam:
* **Infeksi saluran napas:** Flu, batuk pilek (common cold), radang tenggorokan.
* **Infeksi pencernaan:** Gastroenteritis, diare, keracunan makanan.
* **Kelelahan ekstrem atau dehidrasi.**

**Tips Perawatan Awal:**
1. **Kompres Hangat:** Letakkan kompres hangat di dahi, lipat ketiak, atau lipat paha. Jangan gunakan air es karena dapat memicu tubuh menggigil lebih parah.
2. **Hidrasi Ekstra:** Perbanyak konsumsi air putih hangat, teh herbal, atau sup hangat untuk mencegah dehidrasi.
3. **Pakaian yang Sesuai:** Kenakan pakaian yang tipis, longgar, dan menyerap keringat. Jangan memakai selimut yang terlalu tebal.
4. **Obat Pereda Demam:** Anda bisa mengonsumsi *Paracetamol* atau *Ibuprofen* untuk menurunkan suhu tubuh sesuai petunjuk dosis.

*Segera periksakan diri ke dokter apabila demam berlangsung lebih dari 3 hari berturut-turut, mencapai suhu ekstrem (>39°C), atau disertai sesak napas, ruam kemerahan, kaku leher, atau kejang.*`;
        } else if (lowerMsg.includes('lambung') || lowerMsg.includes('maag') || lowerMsg.includes('perut perih') || lowerMsg.includes('ulu hati') || lowerMsg.includes('gerd')) {
            aiMsg = `Nyeri ulu hati, mual, atau perut kembung sering dikaitkan dengan masalah kelebihan asam lambung (seperti dispepsia, maag/gastritis, atau penyakit asam lambung/GERD).

Beberapa pemicu umum asam lambung naik:
* Terlambat makan atau jeda waktu makan yang terlalu lama.
* Mengonsumsi makanan pedas, terlalu asam, bersantan kental, atau berlemak tinggi.
* Tingkat stres emosional yang tinggi.
* Konsumsi minuman berkafein (kopi/teh), minuman berkarbonasi (soda), atau alkohol.

**Langkah Penanganan Mandiri:**
* Terapkan pola makan **porsi kecil tapi sering** (misal makan 5-6 kali sehari dengan porsi kecil).
* Jangan langsung berbaring setelah makan. Beri jeda minimal 2-3 jam agar makanan turun dengan sempurna.
* Posisikan kepala dan pundak lebih tinggi saat tidur (misal menggunakan 2 bantal) jika mengalami gejala ulu hati terasa terbakar saat tidur.
* Kurangi pemicu stres melalui meditasi, pernapasan dalam, atau hobi santai.
* Obat maag bebas seperti golongan *Antasida* dapat dikonsumsi 1 jam sebelum makan untuk meredakan keluhan secara cepat.

*Jika Anda mengalami nyeri perut yang tidak tertahankan, muntah berwarna hitam atau disertai darah, serta BAB berwarna hitam pekat, segeralah ke instalasi gawat darurat.*`;
        } else if (lowerMsg.includes('gaya hidup') || lowerMsg.includes('sehat') || lowerMsg.includes('tips diet')) {
            aiMsg = `Membangun gaya hidup sehat adalah investasi terbaik jangka panjang untuk tubuh Anda. Berikut 4 pilar gaya hidup sehat yang disarankan oleh pakar kesehatan:

1. **Nutrisi Seimbang (Diet 4 Sehat):** Perbanyak konsumsi serat dari buah dan sayur, pilih protein rendah lemak (dada ayam, ikan, tahu/tempe), batasi konsumsi gula, garam berlebih, dan lemak jenuh (gorengan).
2. **Aktivitas Fisik Rutin:** Lakukan olahraga intensitas sedang seperti jalan cepat, bersepeda, atau berenang minimal 150 menit per minggu (misal 30 menit per hari, 5 hari seminggu).
3. **Kualitas Tidur Optimal:** Orang dewasa membutuhkan 7-9 jam tidur berkualitas setiap malam untuk pemulihan sel dan regulasi hormonal.
4. **Kesehatan Mental & Stres Kontrol:** Kelola stres dengan meditasi, luangkan waktu untuk relaksasi, dan jaga hubungan sosial yang positif.

*Anda bisa mencatat kemajuan aktivitas fisik dan suasana hati Anda di **Health Dashboard** aplikasi DocDot untuk melihat grafik perkembangan kesehatan Anda dari waktu ke waktu.*`;
        } else {
            aiMsg = `Terima kasih atas pesan Anda. Sebagai asisten kesehatan berbasis kecerdasan buatan (AI), berikut beberapa saran umum yang dapat saya sampaikan terkait pesan Anda:

1. **Jaga Kondisi Fisik:** Berikan waktu istirahat yang cukup untuk mendukung kemampuan pemulihan alami tubuh.
2. **Penuhi Cairan:** Minumlah air putih minimal 8 gelas per hari untuk menjaga organ tubuh terhidrasi dengan baik.
3. **Gunakan Health Dashboard:** Kami menyediakan pencatatan log kesehatan (fisik & mental) secara interaktif. Anda bisa melacak grafik berat badan, aktivitas, mood, dan stres Anda secara berkala.
4. **Konsultasikan Lebih Lanjut:** Jika keluhan yang Anda rasakan mengganggu aktivitas sehari-hari, sangat disarankan untuk membuat janji temu dengan dokter profesional.

*Catatan: Jawaban AI ini bertujuan edukatif dan tidak menggantikan diagnosis, pemeriksaan medis, atau pengobatan resmi oleh dokter.*`;
        }

        const user_message = {
            id: Date.now() - 1,
            sender: 'user',
            message: userMsg,
            created_at: new Date().toISOString()
        };
        const ai_message = {
            id: Date.now(),
            sender: 'ai',
            message: aiMsg,
            created_at: new Date().toISOString()
        };

        // Fetch existing messages and append
        const storedMessages = localStorage.getItem(`docdot_chat_${sessionId}`);
        const messages = storedMessages ? JSON.parse(storedMessages) : [];
        messages.push(user_message, ai_message);
        localStorage.setItem(`docdot_chat_${sessionId}`, JSON.stringify(messages));

        return mockResponse({ user_message, ai_message });
    }
    
    // 3. Mental Health Log & Physical Log Storage Persistence
    if (url === '/api/mental-health/log' || url === '/health-dashboard/mental') {
        const stored = localStorage.getItem('docdot_mental_logs') || '[]';
        const logs = JSON.parse(stored);
        
        const newLog = {
            id: Date.now(),
            mood: Number(data.mood || 3),
            stress_level: Number(data.stress_level || 3),
            sleep_hours: data.sleep_hours ? Number(data.sleep_hours) : null,
            note: data.note || null,
            logged_at: data.logged_at || new Date().toISOString().split('T')[0]
        };
        
        logs.push(newLog);
        localStorage.setItem('docdot_mental_logs', JSON.stringify(logs));
        return mockResponse({ success: true, message: 'Log mental berhasil disimpan!' });
    }
    
    if (url === '/health-dashboard/physical') {
        const stored = localStorage.getItem('docdot_physical_logs') || '[]';
        const logs = JSON.parse(stored);
        
        const newLog = {
            id: Date.now(),
            weight_kg: data.weight_kg ? Number(data.weight_kg) : null,
            blood_pressure: data.blood_pressure || null,
            activity_minutes: data.activity_minutes ? Number(data.activity_minutes) : null,
            logged_at: data.logged_at || new Date().toISOString().split('T')[0]
        };
        
        logs.push(newLog);
        localStorage.setItem('docdot_physical_logs', JSON.stringify(logs));
        
        // Also update height/weight in user profile if weight is logged
        if (data.weight_kg) {
            const storedProfile = localStorage.getItem('docdot_user_profile');
            const profile = storedProfile ? JSON.parse(storedProfile) : { height: 170, weight: 68.0, bmi: 23.5, bmi_category: 'Normal' };
            
            profile.weight = Number(data.weight_kg);
            const heightInMeters = profile.height / 100;
            profile.bmi = Number((profile.weight / (heightInMeters * heightInMeters)).toFixed(1));
            
            if (profile.bmi < 18.5) profile.bmi_category = 'Underweight';
            else if (profile.bmi <= 25.0) profile.bmi_category = 'Normal';
            else if (profile.bmi <= 27.0) profile.bmi_category = 'Overweight';
            else profile.bmi_category = 'Obese';
            
            localStorage.setItem('docdot_user_profile', JSON.stringify(profile));
        }

        return mockResponse({ success: true, message: 'Log fisik berhasil disimpan!' });
    }
    
    // 4. Request Doctor
    if (url === '/doctor-patient/request') {
        const stored = localStorage.getItem('docdot_my_doctors') || '[]';
        const myDocs = JSON.parse(stored);
        
        // Find doctor details
        const docId = data.doctor_id || 1;
        const doctors = [
            { id: 1, name: 'dr. Andi Pratama, Sp.PD', specialization: 'Spesialis Penyakit Dalam' },
            { id: 2, name: 'dr. Rina Sari, Sp.A', specialization: 'Spesialis Anak' },
            { id: 3, name: 'dr. Budi Santoso, Sp.KJ', specialization: 'Spesialis Kejiwaan' }
        ];
        const doc = doctors.find(d => d.id === docId) || doctors[0];
        
        myDocs.push({
            id: Date.now(),
            doctor: doc,
            status: 'pending',
            created_at: new Date().toISOString()
        });
        
        localStorage.setItem('docdot_my_doctors', JSON.stringify(myDocs));
        return mockResponse({ success: true, message: 'Permintaan koneksi dikirim ke dokter!' });
    }
    
    if (url.includes('/doctor-patient/accept/')) {
        return mockResponse({ success: true });
    }
    
    if (url.includes('/doctor-patient/reject/')) {
        return mockResponse({ success: true });
    }
    
    if (url.includes('/doctor-patient/patient/') && url.endsWith('/mark-read')) {
        return mockResponse({ success: true });
    }

    return mockResponse({ success: true });
};

const handleDelete = (url: string) => {
    console.log(`[Mock Axios DELETE]: ${url}`);
    
    if (url.includes('/doctor-patient/cancel/') || url.includes('/doctor-patient/disconnect/')) {
        const id = Number(url.split('/').pop());
        const stored = localStorage.getItem('docdot_my_doctors') || '[]';
        const myDocs = JSON.parse(stored);
        const updated = myDocs.filter((d: any) => d.id !== id);
        localStorage.setItem('docdot_my_doctors', JSON.stringify(updated));
        return mockResponse({ success: true, message: 'Koneksi dengan dokter dibatalkan.' });
    }
    
    if (url.includes('/consultation/session/')) {
        const sessionId = url.split('/').pop();
        localStorage.removeItem(`docdot_chat_${sessionId}`);
        return mockResponse({ success: true, message: 'Sesi chat berhasil dihapus.' });
    }

    return mockResponse({ success: true });
};

const mockAxios = {
    get: handleGet,
    post: handlePost,
    put: handlePost,
    delete: handleDelete,
    defaults: {
        headers: {
            common: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }
    }
};

export default mockAxios;
