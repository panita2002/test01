CREATE TABLE headings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    `order` INT NOT NULL,
    FOREIGN KEY (parent_id) REFERENCES headings(id) ON DELETE CASCADE
);

CREATE TABLE tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    heading_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    FOREIGN KEY (heading_id) REFERENCES headings(id) ON DELETE CASCADE
);

CREATE TABLE table_rows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_id INT NOT NULL,
    variable VARCHAR(255) NOT NULL,
    value VARCHAR(255) NOT NULL,
    description TEXT,
    FOREIGN KEY (table_id) REFERENCES tables(id) ON DELETE CASCADE
);



-- Insert headings (หัวข้อหลักและย่อย)
INSERT INTO headings (id, parent_id, title, `order`) VALUES
(1, NULL, 'บทที่ 1 บทนำ (Introduction)', 1),
(2, NULL, 'บทที่ 2 Setup environment variable', 2),
(3, 2, '2.1 Environment variable บนเครื่องกลุ่ม custodian service', 1),
(4, 2, '2.2 Firewall Port สำหรับที่เกี่ยวกับ Application', 2),
(5, 2, '2.3 Directory structure และ Permission', 3),
(6, NULL, 'บทที่ 3 Setup User และ Group', 3);

-- Insert tables (ตารางสำหรับหัวข้อ)
INSERT INTO tables (id, heading_id, title) VALUES
(1, 3, 'Client'),
(2, 3, 'Proxy Server'),
(3, 3, 'Application Server');

-- Insert table rows (ข้อมูลในตาราง)
INSERT INTO table_rows (id, table_id, variable, value, description) VALUES
-- Client Table
(1, 1, 'BONANZA_HOME', 'E:\\Bonanza', 'Path to Bonanza home directory'),
-- Proxy Server Table
(2, 2, 'TRAEFIK_HOME', 'D:\\traefik\\traefik-3.1.2', 'Traefik installation path'),
(3, 2, 'LOG_TRAEFIK_DIR', 'E:\\logs\\traefik', 'Traefik log directory'),
-- Application Server Table
(4, 3, 'JAVA_HOME', 'D:\\jdk\\jdk-21.0.4.7-hotspot', 'Path to JDK installation'),
(5, 3, 'CATALINA_HOME', 'D:\\tomcat\\apache-tomcat-10.1.30', 'Path to Tomcat installation'),
(6, 3, 'BONANZA_HOME', 'E:\\Bonanza', 'Path to Bonanza directory'),
(7, 3, 'WEB_CONTENT_DIR', 'E:\\webapps', 'Directory for web application content');
