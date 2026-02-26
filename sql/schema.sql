CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','manager','user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS media (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(190) NOT NULL,
  description TEXT NULL,
  tags VARCHAR(255) NULL,
  file_path VARCHAR(255) NOT NULL,
  mime_type VARCHAR(100) NOT NULL,
  media_type ENUM('photo','video') NOT NULL DEFAULT 'photo',
  status ENUM('published','draft') NOT NULL DEFAULT 'published',
  uploaded_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_media_user FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO users (name, email, password_hash, role)
VALUES ('Administrator', 'admin@example.com', '$2y$10$A6i5qbqrhU8o7xM6mA4oMuT5wSMRKnfRb7m8uGQixTXFh2ue7QdOG', 'admin')
ON DUPLICATE KEY UPDATE email=email;
-- Default password: Admin@123
