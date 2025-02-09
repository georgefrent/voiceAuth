CREATE DATABASE IF NOT EXISTS tmp;
USE tmp;
CREATE TABLE IF NOT EXISTS users(
	id INT NOT NULL AUTO_INCREMENT,
	username varchar(255) NOT NULL,
	password varchar(255) NOT NULL,
	email varchar(255),
	voiceFile TINYINT(1) DEFAULT 0,
	authToken TINYINT(1) DEFAULT 0,
	PRIMARY KEY (id)
);

-- Insert a default admin user for testing
INSERT INTO users (username, password, email, voiceFile, authToken) VALUES
('admin', MD5('admin123'), 'admin@example.com', 0, 0);
