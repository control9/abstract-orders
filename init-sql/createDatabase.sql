CREATE TABLE IF NOT EXISTS USERS (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  login VARCHAR(30) NOT NULL,
  pass VARCHAR(36) NOT NULL,
  salt VARCHAR(4) NOT NULL,
  money INT(11) UNSIGNED NOT NULL,
  worker BOOLEAN NOT NULL,
  active BOOLEAN NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE (login)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS SESSIONS (
  user_id INT(11) UNSIGNED NOT NULL,
  session_id VARCHAR(36) NOT NULL,
  started TIMESTAMP NOT NULL,
  active BOOLEAN NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS CARDS (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  creator INT(11) UNSIGNED NOT NULL,
  executor INT(11) UNSIGNED,
  cost INT(11) UNSIGNED NOT NULL,
  created TIMESTAMP NOT NULL,
  completed BOOLEAN not null,
  PRIMARY KEY  (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;