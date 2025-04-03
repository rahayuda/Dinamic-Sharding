-- Buat Database db_a
CREATE DATABASE IF NOT EXISTS `db_a` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_a`;

-- Buat Tabel articles
CREATE TABLE `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `views` int(11) DEFAULT 0,
  `modified` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Masukkan Data ke dalam tabel articles
INSERT INTO `articles` (`title`, `content`, `views`, `modified`) VALUES
('Optimasi Basis Data dengan Sharding', 'Sharding adalah teknik untuk membagi basis data besar menjadi bagian lebih kecil...', 0, '2025-04-03 08:52:23'),
('Penerapan AI dalam Manajemen Informasi', 'Artificial Intelligence semakin banyak diterapkan dalam manajemen informasi untuk meningkatkan efisiensi...', 0, '2025-04-03 09:46:25'),
('Keamanan Data pada Sistem Terdistribusi', 'Keamanan menjadi aspek krusial dalam sistem basis data terdistribusi...', 0, '2025-04-03 09:54:31'),
('Pengaruh Machine Learning pada Bisnis', 'Machine Learning telah membawa perubahan signifikan dalam dunia bisnis...', 0, '2025-04-03 09:50:25'),
('Reinforcement Learning untuk Database', 'Reinforcement Learning digunakan untuk mengoptimalkan pengelolaan sumber daya basis data...', 0, '2025-04-03 09:46:25'),
('Mengenal OpenAI Gym untuk Simulasi AI', 'OpenAI Gym adalah framework yang digunakan untuk mengembangkan dan menguji algoritma AI...', 0, '2025-04-03 09:48:25'),
('Firebase vs MySQL dalam Aplikasi Web', 'Firebase dan MySQL memiliki keunggulan masing-masing dalam pengelolaan basis data...', 0, '2025-04-03 09:48:25'),
('Pemanfaatan Kafka dalam Data Terdistribusi', 'Kafka sering digunakan dalam sistem big data untuk pemrosesan data secara real-time...', 0, '2025-04-03 09:45:25'),
('Analisis K-Means untuk Clustering', 'K-Means adalah algoritma clustering yang banyak digunakan dalam pengelompokan dokumen...', 0, '2025-04-03 09:47:25'),
('Transformers dalam NLP', 'Model Transformer telah mengubah cara mesin memahami dan menghasilkan teks...', 1, '2025-04-03 09:45:26'),
('Manajemen Data dengan AI', 'Pemanfaatan AI dalam manajemen data dapat meningkatkan efisiensi dan akurasi...', 0, '2025-04-03 09:48:25'),
('Arsitektur Microservices pada Web', 'Microservices memungkinkan pengembangan sistem yang fleksibel dan mudah dikembangkan...', 3, '2025-04-03 09:47:20'),
('Blockchain untuk Keamanan Data', 'Blockchain dapat digunakan untuk meningkatkan keamanan dan transparansi data...', 1, '2025-04-03 09:45:18'),
('Automasi Data Science dengan Python', 'Python menyediakan berbagai pustaka untuk automasi proses data science...', 2, '2025-04-03 09:46:48'),
('Big Data dan Tantangannya', 'Big Data menghadirkan berbagai tantangan dalam penyimpanan dan pengolahan data...', 1, '2025-04-03 09:43:32'),
('Cloud Computing dalam Transformasi Digital', 'Cloud Computing memungkinkan perusahaan lebih efisien dalam mengelola sumber daya IT...', 0, '2025-04-03 09:47:25');

-- Buat Tabel q_table
CREATE TABLE `q_table` (
  `state` int(11) NOT NULL,
  `action0` float DEFAULT 0,
  `action1` float DEFAULT 0,
  `action2` float DEFAULT 0,
  PRIMARY KEY (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Buat Database db_b
CREATE DATABASE IF NOT EXISTS `db_b` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_b`;

-- Buat Tabel articles di db_b
CREATE TABLE `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `views` int(11) DEFAULT 0,
  `modified` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;
