<?php 
$pageTitle = "About Us - Florazziu";
include 'includes/header.php'; 
?>

<main class="about-us-page">
    <div class="about-us-card">
        <h1 class="about-us-title">About us</h1>
        
        <div class="about-us-content">
            <p>Selamat datang di Aplikasi rekomendasi bunga (Florazziu), aplikasi rekomendasi bunga yang membantu Anda menemukan bunga terbaik untuk setiap momen spesial. Kami hadir untuk mempermudah Anda dalam memilih bunga yang tepat berdasarkan jenis, warna, makna, serta kesan yang ingin disampaikan.</p>
            
            <p>Dengan teknologi cerdas dan database lengkap, Florazziu memberikan rekomendasi bunga yang sesuai untuk berbagai acara, seperti ulang tahun, pernikahan, ucapan terima kasih, hingga ungkapan duka cita. Kami percaya bahwa setiap bunga memiliki cerita dan makna tersendiri, dan kami ingin membantu Anda menyampaikan perasaan dengan cara yang indah dan bermakna.</p>
            
            <p>Temukan bunga yang sempurna dengan mudah dan cepat bersama kami.</p>
        </div>
        
        <div class="about-us-footer">
            <a href="index.php" class="back-btn">Back</a>
        </div>
    </div>
</main>
<style>
    /* About Us Page Styles */
.about-us-page {
    background-color: #FDFCE8;
    min-height: 100vh;
    padding: 50px 0;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative; /* Tambahkan ini */
}

.about-us-card {
    background-color: #E2CEB1;
    width: 1300px;
    height: 700px;
    border-radius: 20px;
    padding: 60px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    position: relative; /* Tambahkan ini */
}

.about-us-title {
    color: #584C43;
    font-size: 48px;
    font-weight: 700;
    margin-bottom: 40px;
    text-align: center;
}

.about-us-content {
    color: #333;
    font-size: 18px;
    line-height: 1.8;
    flex-grow: 1;
}

.about-us-content p {
    margin-bottom: 25px;
    text-align: justify;
}

.about-us-footer {
    position: absolute; /* Ubah ke absolute */
    right: 60px; /* Jarak dari kanan */
    bottom: 40px; /* Jarak dari bawah */
}

.back-btn {
    background-color: #584C43;
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
}

.back-btn:hover {
    background-color: #3a332d;
    transform: translateY(-2px);
}

/* Styling untuk halaman About Us yang responsif */

.about-us-page {
    background-color: #FDFCE8;
    min-height: 100vh;
    padding: 50px 20px; /* Tambahkan padding horizontal untuk ruang di tepi layar */
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    /* Tambahkan overflow-x: hidden; di body atau main-content jika ada horizontal scroll */
}

.about-us-card {
    background-color: #E2CEB1;
    width: 90%; /* Gunakan persentase untuk lebar, bukan fixed pixel */
    max-width: 1300px; /* Batasi lebar maksimal untuk desktop */
    /* height: 700px; - Hapus fixed height, biarkan konten menentukan tinggi */
    min-height: 400px; /* Beri tinggi minimum agar tidak terlalu kecil */
    border-radius: 20px;
    padding: 60px; /* Padding besar untuk desktop */
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    position: relative;
    box-sizing: border-box; /* Pastikan padding termasuk dalam lebar */
}

.about-us-title {
    color: #584C43;
    font-size: 48px; /* Ukuran font besar untuk desktop */
    font-weight: 700;
    margin-bottom: 40px;
    text-align: center;
    word-break: break-word; /* Memastikan kata yang panjang tidak keluar dari batas */
}

.about-us-content {
    color: #333;
    font-size: 18px; /* Ukuran font standar untuk desktop */
    line-height: 1.8;
    flex-grow: 1;
    overflow-y: auto; /* Tambahkan scroll jika konten terlalu panjang untuk card */
    padding-right: 15px; /* Sedikit padding untuk scrollbar */
    scrollbar-width: thin; /* Firefox */
    scrollbar-color: #584C43 #f1f1f1; /* Firefox */
}

/* Custom scrollbar untuk Webkit (Chrome, Safari) */
.about-us-content::-webkit-scrollbar {
    width: 8px;
}

.about-us-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.about-us-content::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.about-us-content::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.about-us-content p {
    margin-bottom: 25px;
    text-align: justify;
}

.about-us-footer {
    position: absolute;
    right: 60px;
    bottom: 40px;
    display: flex; /* Untuk menjaga tombol tetap di baris yang sama */
    justify-content: flex-end; /* Dorong tombol ke kanan */
    width: calc(100% - 120px); /* Sesuaikan lebar footer dengan padding card */
    max-width: 1300px; /* Batasi lebar maksimal sesuai card */
}

.back-btn {
    background-color: #584C43;
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
    white-space: nowrap; /* Mencegah tombol pecah baris */
}

.back-btn:hover {
    background-color: #3a332d;
    transform: translateY(-2px);
}

/* --- Media Queries --- */
@media (max-width: 992px) {
    .about-us-card {
        padding: 40px; /* Kurangi padding untuk tablet */
        width: 95%; /* Lebih lebar di tablet */
        min-height: 350px;
    }

    .about-us-title {
        font-size: 38px; /* Kurangi ukuran font judul */
        margin-bottom: 30px;
    }

    .about-us-content {
        font-size: 16px; /* Kurangi ukuran font konten */
        line-height: 1.7;
    }
    
    .about-us-content p {
        margin-bottom: 20px;
    }

    .about-us-footer {
        right: 40px; /* Sesuaikan posisi footer */
        bottom: 30px;
        width: calc(100% - 80px); /* Sesuaikan lebar footer dengan padding card */
    }

    .back-btn {
        padding: 10px 20px; /* Kurangi padding tombol */
        font-size: 15px;
    }
}

@media (max-width: 768px) {
    .about-us-page {
        padding: 30px 15px; /* Padding lebih kecil di mobile */
    }

    .about-us-card {
        padding: 25px; /* Padding lebih kecil lagi */
        width: 100%; /* Penuhi lebar layar di mobile */
        min-height: auto; /* Biarkan tinggi menyesuaikan sepenuhnya */
        border-radius: 15px; /* Sedikit kurva yang lebih kecil */
    }

    .about-us-title {
        font-size: 30px; /* Ukuran font judul untuk mobile */
        margin-bottom: 25px;
    }

    .about-us-content {
        font-size: 15px; /* Ukuran font konten untuk mobile */
        line-height: 1.6;
        padding-right: 10px; /* Padding untuk scrollbar di mobile */
    }

    .about-us-content p {
        margin-bottom: 15px;
    }

    .about-us-footer {
        position: static; /* Ubah ke static agar mengikuti alur dokumen */
        margin-top: 25px; /* Beri jarak dari konten di atasnya */
        right: auto; /* Hapus posisi absolut */
        bottom: auto; /* Hapus posisi absolut */
        width: 100%; /* Penuhi lebar container */
        justify-content: center; /* Pusatkan tombol di mobile */
    }

    .back-btn {
        width: 100%; /* Tombol memenuhi lebar penuh di mobile */
        text-align: center; /* Teks tombol di tengah */
        padding: 10px 15px;
        font-size: 15px;
    }
}

@media (max-width: 480px) {
    .about-us-title {
        font-size: 24px; /* Ukuran font judul untuk layar sangat kecil */
        margin-bottom: 20px;
    }

    .about-us-content {
        font-size: 14px; /* Ukuran font konten untuk layar sangat kecil */
    }

    .about-us-card {
        padding: 20px; /* Padding lebih kecil lagi */
    }
</style>

<?php include 'includes/footer.php'; ?>