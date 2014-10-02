insert into USERS 
  (login, pass, salt, money, worker, active) 
VALUES 
  ("SYSTEM", "", "", 10000, 0, 0),
  ("boss01", "1234", "abcd", 1500, 0, 1),
  ("boss02", "1234", "abcd", 1500, 0, 1),
  ("boss03", "1234", "abcd", 1500, 0, 1),
  ("boss04", "1234", "abcd", 1500, 0, 1),
  ("boss05", "1234", "abcd", 1500, 0, 1),
  ("worker01", "1234", "abcd", 1500, 1, 1),
  ("worker02", "1234", "abcd", 1500, 1, 1);