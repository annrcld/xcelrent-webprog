-- SQL command to add transmission field to cars table
ALTER TABLE cars ADD COLUMN transmission VARCHAR(20) AFTER fuel_type;

-- Update existing records to have a default value if needed
UPDATE cars SET transmission = 'Automatic' WHERE transmission IS NULL;