<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Footer Sederhana</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    .modern-footer {
      background-color: #FDFCE8;
      color: #333;
      font-family: 'Poppins', sans-serif;
      padding: 20px 0;
      border-top: 1px solid #e9ecef;
      margin-top: 10px;
    }

    .footer-bottom {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 10px;
      max-width: 1200px;
      margin: 0 auto;
      padding-left: 20px;
      padding-right: 20px;
    }

    .social-media {
      display: flex;
      gap: 15px;
    }

    .social-icon {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 36px;
      height: 36px;
      background-color: #fff;
      color: #6c757d;
      border-radius: 50%;
      transition: all 0.3s ease;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
      text-decoration: none;
    }

    .social-icon:hover {
      background-color: #a67c52;
      color: white;
      transform: translateY(-3px);
    }

    .copyright {
      font-size: 13px;
      color: #6c757d;
      text-align: center;
    }

    @media (max-width: 768px) {
      .footer-bottom {
        flex-direction: column-reverse;
        gap: 15px;
        text-align: center;
      }
    }
  </style>
</head>
<body>
  <div class="page-container">
    <!-- Konten halaman di sini, misalnya halaman lain yang include footer.php -->

    <footer class="modern-footer">
      <div class="footer-bottom">
        <p class="copyright">© 2025 FLORAZZIU. All rights reserved.</p>
        <div class="social-media">
          <a href="https://www.facebook.com/share/1Yg6LahS4i/" class="social-icon"><i class="fab fa-facebook-f"></i></a>
          <a href="https://www.tiktok.com/@zakkirahimun?is_from_webapp=1&sender_device=pc" class="social-icon"><i class="fab fa-tiktok"></i></a>
          <a href="https://www.instagram.com/zakki_bilqis?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" class="social-icon"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
    </footer>
  </div>
</body>

</html>
