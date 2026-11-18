-- Foro Asperger · esquema MySQL / MariaDB (utf8mb4, InnoDB)

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(30) NOT NULL UNIQUE,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  display_name VARCHAR(40) NOT NULL,
  bio VARCHAR(300) NOT NULL DEFAULT '',
  avatar_type ENUM('icon','photo') NOT NULL DEFAULT 'icon',
  avatar_value VARCHAR(80) NOT NULL DEFAULT 'inf:mint',
  who ENUM('nd','family','pro','curious') NOT NULL DEFAULT 'nd',
  identities VARCHAR(190) NOT NULL DEFAULT '',
  role ENUM('member','mod','admin') NOT NULL DEFAULT 'member',
  pref_calm TINYINT(1) NOT NULL DEFAULT 0,
  pref_noanim TINYINT(1) NOT NULL DEFAULT 0,
  pref_bigtext TINYINT(1) NOT NULL DEFAULT 0,
  pref_hidecounts TINYINT(1) NOT NULL DEFAULT 0,
  banned_at DATETIME NULL,
  last_seen_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS password_resets (
  user_id INT UNSIGNED NOT NULL PRIMARY KEY,
  token_hash CHAR(64) NOT NULL,
  expires_at DATETIME NOT NULL,
  CONSTRAINT fk_pr_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS throttle (
  k VARCHAR(190) NOT NULL PRIMARY KEY,
  hits INT UNSIGNED NOT NULL DEFAULT 0,
  reset_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS forums (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  parent_id INT UNSIGNED NULL,
  grp ENUM('quien','etapa','tema','pro') NOT NULL DEFAULT 'tema',
  slug VARCHAR(60) NOT NULL,
  name VARCHAR(60) NOT NULL,
  description VARCHAR(190) NOT NULL DEFAULT '',
  icon VARCHAR(30) NOT NULL DEFAULT 'chat',
  color VARCHAR(12) NOT NULL DEFAULT 'mint',
  position INT NOT NULL DEFAULT 0,
  pro_only TINYINT(1) NOT NULL DEFAULT 0,
  UNIQUE KEY uq_forum_slug (parent_id, slug),
  KEY idx_forum_parent (parent_id, position),
  CONSTRAINT fk_forum_parent FOREIGN KEY (parent_id) REFERENCES forums(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS threads (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  forum_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  title VARCHAR(140) NOT NULL,
  slug VARCHAR(160) NOT NULL,
  tone VARCHAR(12) NOT NULL DEFAULT 'serio',
  summary TEXT NULL,
  pinned TINYINT(1) NOT NULL DEFAULT 0,
  locked TINYINT(1) NOT NULL DEFAULT 0,
  reply_count INT UNSIGNED NOT NULL DEFAULT 0,
  helpful_count INT UNSIGNED NOT NULL DEFAULT 0,
  last_post_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted_at DATETIME NULL,
  KEY idx_thread_forum (forum_id, deleted_at, pinned, last_post_at),
  KEY idx_thread_user (user_id),
  CONSTRAINT fk_thread_forum FOREIGN KEY (forum_id) REFERENCES forums(id) ON DELETE CASCADE,
  CONSTRAINT fk_thread_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS posts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  thread_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  body TEXT NOT NULL,
  tone VARCHAR(12) NOT NULL DEFAULT 'serio',
  is_first TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  edited_at DATETIME NULL,
  deleted_at DATETIME NULL,
  KEY idx_post_thread (thread_id, created_at),
  KEY idx_post_user (user_id),
  CONSTRAINT fk_post_thread FOREIGN KEY (thread_id) REFERENCES threads(id) ON DELETE CASCADE,
  CONSTRAINT fk_post_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS reactions (
  post_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  kind ENUM('ayuda','igual','animo') NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (post_id, user_id, kind),
  CONSTRAINT fk_react_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  CONSTRAINT fk_react_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bookmarks (
  user_id INT UNSIGNED NOT NULL,
  thread_id INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, thread_id),
  CONSTRAINT fk_bm_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_bm_thread FOREIGN KEY (thread_id) REFERENCES threads(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS reports (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  reporter_id INT UNSIGNED NOT NULL,
  target_type ENUM('post','user','presentation','message') NOT NULL,
  target_id INT UNSIGNED NOT NULL,
  reason VARCHAR(500) NOT NULL DEFAULT '',
  status ENUM('open','closed') NOT NULL DEFAULT 'open',
  resolved_by INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_report_status (status, created_at),
  CONSTRAINT fk_report_user FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS resources (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(120) NOT NULL,
  description VARCHAR(300) NOT NULL DEFAULT '',
  kind ENUM('guia','plantilla','pictogramas') NOT NULL DEFAULT 'guia',
  audience ENUM('todos','adultos','familias','trabajo') NOT NULL DEFAULT 'todos',
  icon VARCHAR(30) NOT NULL DEFAULT 'doc',
  color VARCHAR(12) NOT NULL DEFAULT 'green',
  file_name VARCHAR(120) NOT NULL,
  original_name VARCHAR(190) NOT NULL,
  mime VARCHAR(100) NOT NULL,
  size INT UNSIGNED NOT NULL DEFAULT 0,
  uploader_id INT UNSIGNED NOT NULL,
  status ENUM('published','pending') NOT NULL DEFAULT 'pending',
  featured TINYINT(1) NOT NULL DEFAULT 0,
  downloads INT UNSIGNED NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_res_status (status, featured, created_at),
  CONSTRAINT fk_res_user FOREIGN KEY (uploader_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS presentations (
  user_id INT UNSIGNED NOT NULL PRIMARY KEY,
  first_name VARCHAR(40) NOT NULL,
  age TINYINT UNSIGNED NOT NULL,
  city VARCHAR(60) NOT NULL,
  looking ENUM('amistad','grupo','pareja') NOT NULL DEFAULT 'amistad',
  interests VARCHAR(190) NOT NULL DEFAULT '',
  comm ENUM('texto','voz','video','persona') NOT NULL DEFAULT 'texto',
  body VARCHAR(400) NOT NULL DEFAULT '',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_pres_looking (looking, updated_at),
  CONSTRAINT fk_pres_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS greetings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  from_id INT UNSIGNED NOT NULL,
  to_id INT UNSIGNED NOT NULL,
  message VARCHAR(300) NOT NULL DEFAULT '',
  status ENUM('pending','accepted','declined') NOT NULL DEFAULT 'pending',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_greet (from_id, to_id),
  KEY idx_greet_to (to_id, status),
  CONSTRAINT fk_greet_from FOREIGN KEY (from_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_greet_to FOREIGN KEY (to_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS conversations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_a INT UNSIGNED NOT NULL,
  user_b INT UNSIGNED NOT NULL,
  last_message_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_conv (user_a, user_b),
  KEY idx_conv_b (user_b),
  CONSTRAINT fk_conv_a FOREIGN KEY (user_a) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_conv_b FOREIGN KEY (user_b) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  conversation_id INT UNSIGNED NOT NULL,
  sender_id INT UNSIGNED NOT NULL,
  body VARCHAR(2000) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  read_at DATETIME NULL,
  KEY idx_msg_conv (conversation_id, created_at),
  CONSTRAINT fk_msg_conv FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
  CONSTRAINT fk_msg_sender FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS blocks (
  blocker_id INT UNSIGNED NOT NULL,
  blocked_id INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (blocker_id, blocked_id),
  KEY idx_block_blocked (blocked_id),
  CONSTRAINT fk_block_a FOREIGN KEY (blocker_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_block_b FOREIGN KEY (blocked_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS professionals (
  user_id INT UNSIGNED NOT NULL PRIMARY KEY,
  full_name VARCHAR(80) NOT NULL,
  profession VARCHAR(80) NOT NULL,
  license_no VARCHAR(40) NOT NULL,
  city VARCHAR(60) NOT NULL,
  online TINYINT(1) NOT NULL DEFAULT 0,
  specialties VARCHAR(190) NOT NULL DEFAULT '',
  bio VARCHAR(600) NOT NULL DEFAULT '',
  website VARCHAR(190) NOT NULL DEFAULT '',
  phone VARCHAR(30) NOT NULL DEFAULT '',
  booking_url VARCHAR(190) NOT NULL DEFAULT '',
  plan ENUM('basic','featured','center') NOT NULL DEFAULT 'basic',
  verified_at DATETIME NULL,
  manual_active TINYINT(1) NOT NULL DEFAULT 0,
  stripe_customer_id VARCHAR(60) NULL,
  stripe_subscription_id VARCHAR(60) NULL,
  sub_status VARCHAR(30) NOT NULL DEFAULT '',
  current_period_end DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_pro_city (city),
  KEY idx_pro_customer (stripe_customer_id),
  KEY idx_pro_sub (stripe_subscription_id),
  CONSTRAINT fk_pro_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
