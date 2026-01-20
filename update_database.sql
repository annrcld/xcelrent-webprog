ALTER TABLE users ADD COLUMN password VARCHAR(255) AFTER phone; 
CREATE TABLE IF NOT EXISTS user_sessions (  
    id INT AUTO_INCREMENT PRIMARY KEY,  
    user_id INT NOT NULL,  
    session_token VARCHAR(255) NOT NULL,  
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
    expires_at TIMESTAMP,  
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE  
); 
