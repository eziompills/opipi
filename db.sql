
-- Added roles and staff
CREATE TABLE users (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(180) UNIQUE NOT NULL,
  password_hash CHAR(255) NOT NULL,
  role ENUM('customer','owner','staff','admin') DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE salons (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  owner_id BIGINT NOT NULL,
  name VARCHAR(120),
  city VARCHAR(80),
  lat DECIMAL(9,6),
  lng DECIMAL(9,6),
  address VARCHAR(160),
  description TEXT,
  slug VARCHAR(140) UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE services (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  salon_id BIGINT,
  name VARCHAR(120),
  duration SMALLINT,
  price_cents INT,
  FOREIGN KEY (salon_id) REFERENCES salons(id) ON DELETE CASCADE
);

CREATE TABLE staff (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  salon_id BIGINT,
  user_id BIGINT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (salon_id) REFERENCES salons(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE bookings (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  salon_id BIGINT,
  service_id BIGINT,
  customer_id BIGINT,
  staff_id BIGINT,
  starts_at DATETIME,
  ends_at DATETIME,
  status ENUM('pending','confirmed','done','cancelled') DEFAULT 'confirmed',
  reminder_sent TINYINT DEFAULT 0,
  payment_status ENUM('unpaid','paid') DEFAULT 'unpaid',
  stripe_session VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (salon_id) REFERENCES salons(id) ON DELETE CASCADE,
  FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
  FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE CASCADE
);


-- Ratings / Reviews
CREATE TABLE reviews (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  booking_id BIGINT NOT NULL,
  rating TINYINT CHECK (rating BETWEEN 1 AND 5),
  comment TEXT,
  owner_reply TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);


-- Staff availability
CREATE TABLE staff_availability (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  staff_id BIGINT,
  day_of_week TINYINT, -- 0=Sunday
  start_time TIME,
  end_time TIME,
  FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE CASCADE
);


-- Opening hours per salon
CREATE TABLE salon_hours (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  salon_id BIGINT,
  day_of_week TINYINT, -- 0=Sun
  open_time TIME,
  close_time TIME,
  FOREIGN KEY (salon_id) REFERENCES salons(id) ON DELETE CASCADE
);

-- email verification
ALTER TABLE users ADD verified TINYINT DEFAULT 0, ADD verify_token CHAR(32) NULL;

-- ALTER TABLE bookings ADD COLUMN payment_status ENUM('unpaid','paid') DEFAULT 'unpaid', ADD COLUMN stripe_session VARCHAR(255);
