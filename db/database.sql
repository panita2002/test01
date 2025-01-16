CREATE DATABASE editor_db;

USE editor_db;

CREATE TABLE editor_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    title VARCHAR(255),
    design VARCHAR(255),
    content TEXT,
    created_at DATE,
    created_time TIME,
    updated_at DATE,
    update_time TIME
);

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
