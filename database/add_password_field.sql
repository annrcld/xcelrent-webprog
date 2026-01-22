ALTER TABLE users ADD COLUMN password VARCHAR(255) AFTER phone; 
  
-- After adding the password field, you'll need to update the signup API to include the password in the INSERT statement  
-- And update the login API to verify the password using password_verify(\)  
  
-- Example of how the updated signup would work:  
-- INSERT INTO users (first_name, last_name, email, phone, password) VALUES (?, ?, ?, ?, ?);  
  
-- Example of how the updated login would work:  
-- SELECT id, first_name, last_name, email, phone, password FROM users WHERE email = ?  
-- Then use password_verify(\) to check the password 
