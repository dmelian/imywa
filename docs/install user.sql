insert into users(usr, theme, language, defApp) values  ('NAME', 'CSS', 'es', 'APP');

insert into userRoles(usr,app,role)    values ('NAME', 'APP', 'ROLE'); -- ROLE: user, gestor, etc


CREATE USER 'USER'@'%' IDENTIFIED BY  'PASSWORD - TEXT'
GRANT ALL PRIVILEGES ON *.* TO 'USER'@'%' WITH GRANT OPTION;

-- Opción más correcta.

CREATE USER 'USER'@'IP-SERVIDOR-WEB' IDENTIFIED BY  'PASSWORD - TEXT'
GRANT SELECT ON imywa.* TO 'USER'@'IP-SERVIDOR-WEB' WITH GRANT OPTION; --solo lectura
GRANT ALL PRIVILEGES ON BD-APP.* TO 'USER'@'IP-SERVIDOR-WEB' WITH GRANT OPTION;