<?php
require_once 'config.php';
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$category = $_GET['category'] ?? 'angka';

$materi_data = [
    'angka' => [
        'title' => 'Pola Angka',
        'icon' => '🔢',
        'color' => '#d25a4b',
        'content' => '
            <h2>POLA ANGKA</h2>
            <p><strong>Materi:</strong> Pola angka biasanya mengikuti aturan tertentu seperti penjumlahan, pengurangan, perkalian, pembagian, atau pengulangan yang teratur. Dengan memahami prinsip dasar seperti bilangan berurutan, barisan aritmetika, barisan geometri, dan pola campuran, kita dapat menentukan suku berikutnya atau mencari nilai tertentu dalam pola tersebut.</p>
            
            <h3>1. Bilangan Berurutan</h3>
            <p>Bilangan berurutan memiliki selisih 1 antar setiap bilangan. Jika bilangan pertama nilainya x, maka dua bilangan berikutnya adalah x + 1 dan x + 2.</p>
            <div class="example-box">
                <strong>Contoh:</strong> 5, 6, 7, 8, 9, ...
            </div>
            
            <h3>2. Barisan Aritmatika</h3>
            <p>Barisan aritmetika memiliki selisih tetap antar suku, disebut beda (b). Jika suku pertama a, maka suku ke-n diperoleh dengan menambahkan (n - 1) kali beda pada suku pertama.</p>
            <div class="formula-box">
                <strong>Rumus suku ke-n:</strong> u<sub>n</sub> = a + (n - 1)b
            </div>
            <div class="example-box">
                <strong>Contoh:</strong> 3, 7, 11, 15, 19, ... (beda = 4)<br>
                Suku ke-5: u<sub>5</sub> = 3 + (5-1)×4 = 3 + 16 = 19
            </div>
            
            <h3>3. Barisan Geometri</h3>
            <p>Barisan geometri memiliki rasio tetap, yaitu bilangan yang digunakan untuk mengalikan suku sebelumnya. Jika suku pertama a dan r adalah rasio, maka suku ke-n diperoleh dengan mengalikan a dengan r pangkat (n - 1).</p>
            <div class="formula-box">
                <strong>Rumus suku ke-n:</strong> u<sub>n</sub> = a × r<sup>(n-1)</sup>
            </div>
            <div class="example-box">
                <strong>Contoh:</strong> 2, 6, 18, 54, ... (rasio = 3)<br>
                Suku ke-4: u<sub>4</sub> = 2 × 3<sup>3</sup> = 2 × 27 = 54
            </div>
            
            <h3>4. Pola Campuran</h3>
            <p>Pola campuran terbentuk dari gabungan dua atau lebih barisan yang berjalan berselang-seling, misalnya pola posisi ganjil dan pola posisi genap. Untuk menentukan suatu suku, cari dulu apakah suku tersebut bagian dari pola ganjil atau pola genap. Tidak ada satu rumus umum, melainkan menggunakan rumus aritmetika atau geometri sesuai pola masing-masing.</p>
            <div class="example-box">
                <strong>Contoh:</strong> 1, 10, 3, 20, 5, 30, 7, 40, ...<br>
                • Posisi ganjil (1, 3, 5, 7): bilangan ganjil berurutan<br>
                • Posisi genap (10, 20, 30, 40): kelipatan 10
            </div>
        '
    ],
    'huruf' => [
        'title' => 'Pola Huruf',
        'icon' => '🔤',
        'color' => '#88b751',
        'content' => '
            <h2>POLA HURUF</h2>
            <p><strong>Materi:</strong> Pola huruf mengikuti aturan tertentu, baik berupa pengulangan, pergeseran posisi, maupun penjumlahan berdasarkan nilai huruf. Berikut materi dasar yang harus dipahami:</p>
            
            <h3>1. Pengulangan Kata/Frasa</h3>
            <p>Ketika sebuah kata atau frasa diulang terus-menerus, posisi huruf yang dicari ditentukan oleh sisa pembagian posisi terhadap panjang kata/frasa tersebut.</p>
            <div class="formula-box">
                <strong>Rumus posisi efektif:</strong><br>
                (n - 1) mod (panjang kata)
            </div>
            <div class="example-box">
                <strong>Contoh:</strong> Kata "POLA" diulang: POLAPOLAPOLA...<br>
                Huruf ke-7 = ?<br>
                Panjang kata = 4<br>
                Posisi efektif = (7-1) mod 4 = 6 mod 4 = 2<br>
                Huruf posisi ke-2 (dimulai dari 0) = <strong>L</strong>
            </div>
            
            <h3>2. Pengambilan Huruf Berdasarkan Kelipatan</h3>
            <p>Jika huruf diambil berdasarkan kelipatan tertentu, maka posisi pengambilan adalah hasil dari kelipatan tersebut. Setelah itu, gunakan modulo untuk menentukan huruf yang sesuai.</p>
            <div class="formula-box">
                <strong>Rumus posisi efektif pola:</strong><br>
                (posisi - 1) mod (panjang kata)
            </div>
            <div class="example-box">
                <strong>Contoh:</strong> Dari kata "MATEMATIKA", ambil setiap huruf ke-3<br>
                Huruf ke-3, ke-6, ke-9: T, A, A
            </div>
            
            <h3>3. Pola Kata Bertambah Panjang</h3>
            <p>Jika panjang kata bertambah (misalnya kata 1 = 3 huruf, kata 2 = 4 huruf, dst), maka tentukan posisi huruf dengan menghitung total panjang sampai melewati posisi yang dicari, lalu cari sisa posisinya di kata tersebut.</p>
            <div class="formula-box">
                <strong>Penjumlahan panjang kata ke-n:</strong><br>
                3 + 4 + ... + (2 + n)
            </div>
            <div class="example-box">
                <strong>Contoh:</strong><br>
                Kata 1: ABC (3 huruf)<br>
                Kata 2: DEFG (4 huruf)<br>
                Kata 3: HIJKL (5 huruf)<br>
                Total sampai kata 2 = 3 + 4 = 7 huruf<br>
                Total sampai kata 3 = 3 + 4 + 5 = 12 huruf
            </div>
            
            <h3>4. Huruf Bersusun</h3>
            <p>Untuk huruf bersusun, lakukan operasi per kolom lalu ubah kembali ke huruf sesuai nilai yang didapatkan. Operasi bilangan yang dapat dilakukan yaitu penjumlahan, pengurangan, perkalian. Jika hasil dari operasi melebihi nilai 26 dimana itu huruf terakhir pada alphabet maka dilakukan pengulangan dari huruf A.</p>
            <div class="example-box">
                <strong>Contoh:</strong> A=1, B=2, C=3, ... Z=26<br>
                A + B = 1 + 2 = 3 = C<br>
                Y + D = 25 + 4 = 29 = 29 - 26 = 3 = C (melewati Z, mulai dari A lagi)
            </div>
            <div class="note-box">
                <strong>💡 Catatan:</strong> Untuk nilai > 26, gunakan rumus: <code>(nilai - 1) mod 26 + 1</code>
            </div>
        '
    ],
    'gambar' => [
        'title' => 'Pola Gambar',
        'icon' => '🎨',
        'color' => '#f3ae4d',
        'content' => '
            <h2>POLA GAMBAR</h2>
            <p><strong>Materi:</strong> Pola gambar melibatkan pengenalan bentuk, warna, ukuran, rotasi, dan posisi yang berulang atau berubah secara teratur.</p>
            
            <h3>1. Pola Bentuk</h3>
            <p>Perhatikan urutan bentuk yang berulang seperti segitiga, persegi, lingkaran, dan seterusnya.</p>
            <div class="example-box">
                <strong>Contoh:</strong> △ ▢ ○ △ ▢ ○ △ ▢ ?<br>
                Jawaban: <strong>○</strong> (lingkaran)
            </div>
            
            <h3>2. Pola Warna</h3>
            <p>Warna berulang atau berganti secara teratur dalam urutan tertentu.</p>
            <div class="example-box">
                <strong>Contoh:</strong> 🔴 🔵 🟡 🔴 🔵 🟡 🔴 🔵 ?<br>
                Jawaban: <strong>🟡</strong> (kuning)
            </div>
            
            <h3>3. Pola Ukuran</h3>
            <p>Objek berubah ukuran secara bertahap atau berulang: kecil → sedang → besar.</p>
            <div class="example-box">
                <strong>Contoh:</strong> ● ◐ ○ ● ◐ ○ ● ◐ ?<br>
                Jawaban: <strong>○</strong> (besar)
            </div>
            
            <h3>4. Pola Rotasi</h3>
            <p>Objek berputar dengan sudut tertentu: 0°, 90°, 180°, 270°, atau kembali ke posisi awal.</p>
            <div class="example-box">
                <strong>Contoh:</strong> Segitiga berputar 90° searah jarum jam setiap langkah<br>
                △ → ▷ → ▽ → ◁ → △
            </div>
            
            <h3>5. Pola Kombinasi</h3>
            <p>Gabungan dari beberapa pola sekaligus, misalnya bentuk berubah DAN warna berubah.</p>
            <div class="example-box">
                <strong>Contoh:</strong> Lingkaran merah, persegi biru, segitiga kuning, lingkaran merah, persegi biru, ?<br>
                Jawaban: <strong>Segitiga kuning</strong>
            </div>
        '
    ],
    'kalender' => [
        'title' => 'Pola Kalender',
        'icon' => '📅',
        'color' => '#f17cc9',
        'content' => '
            <h2>POLA KALENDER</h2>
            <p><strong>Materi:</strong> Kalender memiliki pola teratur yang dapat dihitung menggunakan operasi aritmatika sederhana. Setiap 7 hari, nama hari akan berulang sehingga untuk mencari hari tertentu pada masa depan atau masa lalu, kita cukup menggunakan sisa pembagian (modulo 7). Pola ini juga berlaku ketika menghitung jumlah hari tertentu dalam satu bulan atau menentukan hari pada tanggal tertentu dalam tahun kabisat maupun non-kabisat.</p>
            
            <h3>1. Hari dalam Seminggu</h3>
            <p>Ada 7 hari yang berulang terus-menerus: Senin, Selasa, Rabu, Kamis, Jumat, Sabtu, Minggu.</p>
            <div class="formula-box">
                <strong>Rumus mencari hari:</strong><br>
                Hari ke-n = (hari awal + (n - 1)) mod 7
            </div>
            <div class="example-box">
                <strong>Contoh:</strong> Jika tanggal 1 Januari jatuh pada hari Senin, hari apakah tanggal 15 Januari?<br>
                Senin = 0, Selasa = 1, ... Minggu = 6<br>
                Hari ke-15 = (0 + 14) mod 7 = 14 mod 7 = 0 = <strong>Senin</strong>
            </div>
            
            <h3>2. Jumlah Hari dalam Bulan</h3>
            <p>Setiap bulan memiliki jumlah hari yang berbeda:</p>
            <ul>
                <li><strong>31 hari:</strong> Januari, Maret, Mei, Juli, Agustus, Oktober, Desember</li>
                <li><strong>30 hari:</strong> April, Juni, September, November</li>
                <li><strong>28/29 hari:</strong> Februari (28 hari biasa, 29 hari di tahun kabisat)</li>
            </ul>
            <div class="note-box">
                <strong>💡 Tip Mengingat:</strong> "30 hari ada di bulan yang tidak berakhir dengan -BER kecuali September dan November"
            </div>
            
            <h3>3. Pola Tanggal Berulang</h3>
            <p>Tanggal dengan hari yang sama berulang setiap 7 hari.</p>
            <div class="example-box">
                <strong>Contoh:</strong> Jika tanggal 1 Januari = Senin, maka:<br>
                • Tanggal 8 Januari = Senin<br>
                • Tanggal 15 Januari = Senin<br>
                • Tanggal 22 Januari = Senin<br>
                • Tanggal 29 Januari = Senin
            </div>
            
            <h3>4. Tahun Kabisat</h3>
            <p>Tahun kabisat terjadi setiap 4 tahun sekali. Pada tahun kabisat, bulan Februari memiliki 29 hari (bukan 28 hari).</p>
            <div class="formula-box">
                <strong>Aturan tahun kabisat:</strong><br>
                • Jika tahun habis dibagi 4 → Kabisat<br>
                • Kecuali tahun habis dibagi 100 → Bukan kabisat<br>
                • Kecuali tahun habis dibagi 400 → Kabisat
            </div>
            <div class="example-box">
                <strong>Contoh:</strong><br>
                • 2024 → Kabisat (habis dibagi 4)<br>
                • 2100 → Bukan kabisat (habis dibagi 100 tapi tidak 400)<br>
                • 2000 → Kabisat (habis dibagi 400)
            </div>
            
            <h3>5. Menghitung Selisih Hari</h3>
            <p>Untuk menghitung hari pada tanggal tertentu, hitung total hari dari tanggal awal, lalu gunakan modulo 7.</p>
            <div class="example-box">
                <strong>Contoh:</strong> Jika 1 Januari 2024 = Senin, hari apa tanggal 1 Maret 2024?<br>
                Januari = 31 hari<br>
                Februari (2024 kabisat) = 29 hari<br>
                Total = 31 + 29 = 60 hari dari 1 Januari<br>
                60 mod 7 = 4 → 4 hari setelah Senin = <strong>Jumat</strong>
            </div>
        '
    ]
];

$current_materi = $materi_data[$category] ?? $materi_data['angka'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $current_materi['title']; ?> - PatternPlay</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #8dd5ff 0%, #5e6aa1 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .back-btn {
            display: inline-block;
            padding: 12px 25px;
            background: white;
            color: #333;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
            font-weight: 600;
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .materi-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .materi-header {
            text-align: center;
            padding: 30px;
            background: <?php echo $current_materi['color']; ?>;
            color: white;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .materi-header .icon {
            font-size: 60px;
            margin-bottom: 15px;
        }
        
        .materi-header h1 {
            font-size: 36px;
        }
        
        .materi-content h2 {
            color: <?php echo $current_materi['color']; ?>;
            font-size: 28px;
            margin-bottom: 20px;
            border-bottom: 3px solid <?php echo $current_materi['color']; ?>;
            padding-bottom: 10px;
        }
        
        .materi-content h3 {
            color: #333;
            font-size: 22px;
            margin-top: 30px;
            margin-bottom: 15px;
            padding-left: 15px;
            border-left: 4px solid <?php echo $current_materi['color']; ?>;
        }
        
        .materi-content p {
            color: #555;
            line-height: 1.8;
            margin-bottom: 15px;
            font-size: 16px;
            text-align: justify;
        }
        
        .materi-content ul {
            margin-left: 40px;
            margin-bottom: 20px;
            color: #555;
            line-height: 2;
        }
        
        .materi-content ul li {
            margin-bottom: 10px;
        }
        
        .formula-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            font-size: 17px;
            font-family: 'Courier New', monospace;
        }
        
        .formula-box strong {
            color: #1565c0;
            display: block;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .example-box {
            background: #f1f8e9;
            border-left: 4px solid #8bc34a;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .example-box strong {
            color: #558b2f;
            display: block;
            margin-bottom: 10px;
        }
        
        .note-box {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .note-box strong {
            color: #e65100;
            display: block;
            margin-bottom: 10px;
        }
        
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #d32f2f;
        }
        
        @media (max-width: 768px) {
            .materi-container {
                padding: 25px;
            }
            
            .materi-header h1 {
                font-size: 28px;
            }
            
            .materi-header .icon {
                font-size: 50px;
            }
            
            .materi-content h2 {
                font-size: 24px;
            }
            
            .materi-content h3 {
                font-size: 20px;
            }
            
            .materi-content p,
            .formula-box,
            .example-box,
            .note-box {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-btn">← Kembali ke Dashboard</a>
        
        <div class="materi-container">
            <div class="materi-header">
                <div class="icon"><?php echo $current_materi['icon']; ?></div>
                <h1><?php echo $current_materi['title']; ?></h1>
            </div>
            
            <div class="materi-content">
                <?php echo $current_materi['content']; ?>
            </div>
        </div>
    </div>
</body>
</html>