-- ============================================================
-- LIGHTHOUSE CMS — MySQL Schema
-- Jalankan di phpMyAdmin atau: mysql -u root -p < db.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS lighthouse_cms
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE lighthouse_cms;

-- ── Users (CMS login) ────────────────────────────────────
CREATE TABLE users (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(100) NOT NULL,
  email      VARCHAR(150) NOT NULL UNIQUE,
  password   VARCHAR(255) NOT NULL,
  role       ENUM('admin','sales') DEFAULT 'sales',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Projects ─────────────────────────────────────────────
CREATE TABLE projects (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  title        VARCHAR(200) NOT NULL,
  client       VARCHAR(150),
  year         SMALLINT,
  cat          VARCHAR(100) DEFAULT 'Web Development',
  short_desc   TEXT,
  long_desc    TEXT,
  challenge    TEXT,
  solution     TEXT,
  duration     VARCHAR(100),
  scope        VARCHAR(200),
  tech         JSON,
  challenges   JSON,
  results      JSON,
  metrics      JSON,
  thumbnail    VARCHAR(500),
  gallery      JSON,
  color        TINYINT DEFAULT 0,
  status       ENUM('published','draft') DEFAULT 'published',
  order_num    INT DEFAULT 99,
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Team ─────────────────────────────────────────────────
CREATE TABLE team (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(150) NOT NULL,
  role       VARCHAR(150),
  bio        TEXT,
  skills     JSON,
  linkedin   VARCHAR(300),
  photo      VARCHAR(500),
  color      TINYINT DEFAULT 0,
  active     TINYINT(1) DEFAULT 1,
  order_num  INT DEFAULT 99,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Services ─────────────────────────────────────────────
CREATE TABLE services (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(150) NOT NULL,
  description TEXT,
  icon       VARCHAR(50) DEFAULT 'web',
  active     TINYINT(1) DEFAULT 1,
  order_num  INT DEFAULT 99,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Clients ──────────────────────────────────────────────
CREATE TABLE clients (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(150) NOT NULL,
  logo       VARCHAR(500),
  active     TINYINT(1) DEFAULT 1,
  order_num  INT DEFAULT 99,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Testimonials ─────────────────────────────────────────
CREATE TABLE testimonials (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(150) NOT NULL,
  role       VARCHAR(150),
  company    VARCHAR(150),
  quote      TEXT,
  rating     TINYINT DEFAULT 5,
  status     ENUM('published','draft') DEFAULT 'published',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Leads ────────────────────────────────────────────────
CREATE TABLE leads (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(150) NOT NULL,
  email       VARCHAR(200),
  phone       VARCHAR(30),
  company     VARCHAR(150),
  service     VARCHAR(150),
  message     TEXT,
  status      ENUM('Baru','Diproses','Selesai','Tidak Relevan') DEFAULT 'Baru',
  notes       TEXT,
  updated_by  VARCHAR(150),
  updated_at  DATETIME,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SEED DATA
-- ============================================================

-- Admin users (password: Rahasia@123)
-- Hash dibuat dengan password_hash('Rahasia@123', PASSWORD_DEFAULT)
INSERT INTO users (name, email, password, role) VALUES
('Arno Suwarno', 'admin@lighthouse.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Rina Sales',   'sales@lighthouse.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sales');
-- CATATAN: Jalankan setup.php setelah import untuk set password yang benar

-- Default Services
INSERT INTO services (name, description, icon, active, order_num) VALUES
('Web Development',        'Website dan aplikasi web modern yang cepat, responsif, dan mudah dikelola. Dibangun dengan teknologi terkini untuk performa optimal.',                                                 'web',      1, 1),
('UI/UX Design',           'Desain antarmuka yang intuitif dan visual yang menarik. Kami merancang pengalaman pengguna yang membuat audiens betah dan konversi meningkat.',                                       'uiux',     1, 2),
('Brand Identity',         'Logo, color palette, tipografi, dan panduan brand yang konsisten. Kami membantu bisnis Anda tampil profesional di setiap touchpoint.',                                                'brand',    1, 3),
('Digital Strategy',       'Strategi digital berbasis data untuk pertumbuhan bisnis jangka panjang. SEO, content marketing, dan analitik dalam satu roadmap terintegrasi.',                                       'strategy', 1, 4),
('CMS & Sistem Internal',  'Dashboard admin dan CMS yang mudah digunakan tanpa coding. Tim Anda dapat memperbarui konten dan mengelola leads secara mandiri.',                                                    'cms',      1, 5),
('Maintenance & Support',  'Dukungan teknis berkelanjutan pasca-launch. Update rutin, monitoring performa, dan respons cepat memastikan sistem selalu optimal.',                                                  'wrench',   1, 6),
('E-Commerce Development', 'Toko online yang siap berjualan — produk, keranjang, checkout, dan manajemen order terintegrasi dalam satu platform yang mudah dikelola.',                                            'cart',     1, 7),
('Social Media & Content', 'Konten visual dan copy yang konsisten dengan brand untuk platform sosial. Dari desain feed hingga content calendar bulanan yang terencana.',                                           'social',   1, 8),
('Video & Motion Design',  'Konten video pendek, animasi explainer, dan motion graphic untuk iklan digital, presentasi, maupun kampanye media sosial yang impactful.',                                            'video',    1, 9);

-- Default Projects
INSERT INTO projects (title, client, year, cat, short_desc, long_desc, challenge, solution, status, color) VALUES
('Platform E-Commerce Fashion Lokal',  'Batik Nusantara Co.',    2025, 'Web Development',       'Membangun platform belanja online end-to-end dengan fitur lengkap untuk brand fashion lokal.', 'Platform e-commerce modern dengan sistem pembayaran terintegrasi, manajemen inventori real-time, dan dashboard analitik penjualan.', 'Kompleksitas integrasi payment gateway lokal dan kebutuhan performa tinggi saat flash sale.', 'Arsitektur microservices dengan Redis caching dan queue system untuk menangani lonjakan traffic.', 'published', 0),
('Rebrand Visual Restoran Premium',    'Le Jardin Restaurant',   2025, 'Brand Identity',        'Rebranding menyeluruh dari identitas visual hingga menu design untuk restoran bintang lima.', 'Proyek rebranding komprehensif meliputi logo baru, palet warna, tipografi, stationery, dan panduan brand lengkap.', 'Menjaga esensi heritage restoran sekaligus tampil modern dan premium.', 'Riset mendalam terhadap kompetitor dan pelanggan, menghasilkan identitas yang elegan namun tetap relevan.', 'published', 1),
('Dashboard Analytics B2B SaaS',       'SupplyChain Pro',        2024, 'UI/UX Design',          'Merancang sistem visualisasi data yang intuitif untuk platform SaaS manajemen rantai pasok.', 'Desain ulang dashboard dengan fokus pada data density dan kemudahan penggunaan untuk tim non-teknis.', 'Menampilkan data kompleks dari 12 KPI berbeda dalam satu layar tanpa membingungkan pengguna.', 'Pendekatan progressive disclosure dengan hierarki visual yang jelas dan kustomisasi widget.', 'published', 2),
('Website Company Profile Hukum',      'Nugraha & Rekan Law Firm',2024,'Web Development',       'Website profesional dengan sistem booking konsultasi terintegrasi untuk firma hukum terkemuka.', 'Website multi-halaman dengan CMS custom, sistem pemesanan jadwal konsultasi, dan blog artikel hukum.', 'Membangun kepercayaan secara digital dalam industri yang sangat konservatif.', 'Desain minimalis dan profesional dengan case study terstruktur dan ulasan klien terverifikasi.', 'published', 3),
('Strategi Konten Startup Fintech',    'PayNow Indonesia',       2024, 'Digital Strategy',      'Menyusun strategi konten 6 bulan dan mengoptimalkan SEO untuk startup fintech series A.', 'Audit SEO menyeluruh, riset kata kunci kompetitif, content calendar, dan framework pengukuran performa.', 'Bersaing dengan pemain besar di ruang fintech dengan anggaran konten terbatas.', 'Fokus pada long-tail keywords niche dan konten edukasi mendalam yang tidak bisa direplikasi cepat.', 'published', 4),
('Aplikasi Internal HR System',        'PT Sinar Nusantara',     2023, 'CMS & Sistem Internal', 'Sistem manajemen karyawan internal dengan fitur absensi, payroll, dan evaluasi performa.', 'Aplikasi web full-stack menggantikan proses HR berbasis Excel dengan alur kerja otomatis dan laporan real-time.', 'Migrasi data historis 5 tahun dan pelatihan 200+ pengguna non-teknis.', 'Antarmuka yang familiar mirip spreadsheet dengan onboarding bertahap dan panduan kontekstual.', 'published', 5);

-- Default Team
INSERT INTO team (name, role, bio, skills, active, color, order_num) VALUES
('Budi Pratama',    'Co-Founder & Creative Director', '10+ tahun pengalaman di branding dan UI/UX. Sebelumnya Creative Lead di agensi internasional Jakarta.', '["UI/UX Design","Branding","Figma"]',      1, 0, 1),
('Sari Dewi',       'Head of Development',            'Full-stack developer spesialis React dan Laravel. Passionate tentang performa dan clean architecture.',   '["React / Next.js","Node.js","Laravel"]',  1, 1, 2),
('Reza Firmansyah', 'Project Manager',                'Bersertifikasi PMP dengan track record delivery 40+ proyek digital tepat waktu dan dalam anggaran.',       '["Project Management","Agile","Analytics"]',1, 2, 3),
('Maya Putri',      'UI/UX Designer',                 'Designer dengan latar belakang psikologi kognitif. Ahli dalam research-driven design dan usability testing.', '["UI/UX","Figma","Research"]',           1, 3, 4);

-- Default Testimonials
INSERT INTO testimonials (name, role, company, quote, rating, status) VALUES
('Andi Wijaya',   'CEO',                'PT Maju Digital',      'Lighthouse tidak hanya membangun website — mereka membangun kepercayaan digital kami. Hasilnya melebihi ekspektasi.', 5, 'published'),
('Dian Rahayu',   'Marketing Director', 'Retail Prima Group',   'Tim yang sangat profesional dan komunikatif. Proses kerjanya transparan dan deliverable selalu tepat waktu.',           5, 'published'),
('Hendri Susanto', 'Founder',           'GrowthHack.id',        'ROI dari website baru kami meningkat 3x dalam 2 bulan. Lighthouse benar-benar memahami kebutuhan bisnis, bukan sekadar desain.', 5, 'published'),
('Lina Kartika',  'Brand Manager',      'Le Jardin Restaurant', 'Proses rebrand kami sangat lancar. Mereka berhasil menangkap esensi brand kami dan mengubahnya jadi identitas visual yang kuat.', 5, 'published');

-- Sample Leads
INSERT INTO leads (name, email, phone, company, service, message, status, created_at) VALUES
('Agus Setiawan',   'agus@teknologi.id',    '081234567890', 'PT Teknologi Maju',    'Web Development',  'Kami butuh platform e-commerce untuk produk UMKM binaan kami.',     'Baru',     '2026-06-08 09:15:00'),
('Fitri Handayani', 'fitri@brandlokal.com', '082134567891', 'Brand Lokal Co.',      'Brand Identity',   'Mau rebranding total untuk usaha clothing kami yang akan ekspansi.', 'Diproses', '2026-06-07 14:30:00'),
('Darmawan Putra',  'darmawan@startup.co',  '083234567892', 'StartupHub Indonesia', 'UI/UX Design',     'Butuh desain ulang aplikasi mobile kami. UX-nya kurang intuitif.',  'Selesai',  '2026-06-05 11:00:00');
