select 'TRANSFERS' as file;

insert into sources(source) values ('transfers');

insert into dbs (db) values ('transfers');

insert into apps(app,dbServer,source, theme) values
	('transfers','localhost','transfers','amedita');

insert into appDbs(app, db, dbName, main) values
	('transfers', 'transfers', 'transfers', true);

insert into appRoles(app,role,startClass,defPermissionType) values
	('transfers','user','transfers_inicio','allow');

insert into userRoles(usr,app,role)
	values ('dmelian', 'transfers', 'user');

insert into rolePermissions(app, role, objective, actions, permission) values
	('transfers', 'user', 'transfers_inicio', null, 'allow');

