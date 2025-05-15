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
</style>

<?php include 'includes/footer.php'; ?>