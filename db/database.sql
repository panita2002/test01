CREATE DATABASE editor_db;

USE editor_db;

CREATE TABLE `editor_content` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `title` text DEFAULT NULL,
  `main_topic` varchar(255) DEFAULT NULL,
  `sub_topic` varchar(255) DEFAULT NULL,
  `sub_sub_topic` varchar(255) DEFAULT NULL,
  `order_number` int(11) DEFAULT NULL,
  `design` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `created_time` time DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  `update_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE editor_content
ADD COLUMN project_id INT NOT NULL AFTER id,
ADD COLUMN category_id INT NOT NULL AFTER project_id;



CREATE DATABASE category;

CREATE TABLE category (
    category_id INT AUTO_INCREMENT PRIMARY KEY, -- รหัสหมวดหมู่ (Primary Key)
    category_name VARCHAR(255) NOT NULL,       -- ชื่อหมวดหมู่
    description TEXT,                          -- คำอธิบายหมวดหมู่ (ไม่จำเป็นต้องใส่)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- เวลาที่สร้าง
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP -- เวลาที่อัปเดต
);
