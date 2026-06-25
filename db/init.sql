-- Runs ONCE automatically when the local MySQL container is first created
-- (mounted into /docker-entrypoint-initdb.d by docker-compose.yml).
-- On Render this file is NOT used — schema.php creates the table instead.

CREATE TABLE IF NOT EXISTS messages (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(80)  NOT NULL,
  message    VARCHAR(500) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO messages (name, message) VALUES
  ('Ada',   'Welcome to the guestbook!'),
  ('Linus', 'Hello from the database.');
