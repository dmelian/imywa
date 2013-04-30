select 'JACKPOT' as file;

insert into sources(source) values ('jackpot');

insert into dbs (db) values ('jackpot');

insert into apps(app,dbServer,source, theme) values
	('jackpot','localhost','jackpot','amedita');

insert into appDbs(app, db, dbName,main) values
	('jackpot', 'jackpot', 'jackpot', true);

insert into appRoles(app,role,startClass,defPermissionType) values
	('jackpot','user','jackpot_inicio','allow');

insert into userRoles(usr,app,role)
	values ('dmelian', 'jackpot', 'user');

insert into rolePermissions(app, role, objective, actions, permission) values
	('jackpot', 'user', 'jackpot_inicio', null, 'allow');

