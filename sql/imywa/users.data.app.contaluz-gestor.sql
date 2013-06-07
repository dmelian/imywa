select 'CONTALUZ-GESTOR' as file;

insert into sources(source) values ('contaluz');

insert into dbs (db) values ('cluz');

insert into dbPartitions(db, partitionLevel, partitionId)
	values ('cluz', 0, ''), ('cluz', 1, 'cliente');

insert into apps(app,dbServer,source, theme) 
	values ('contaluz-gestor','localhost','contaluz','amedita');

insert into appDbs(app, db, dbName, main) 
	values ('contaluz-gestor', 'cluz', 'cluz', true);

insert into appRoles(app,role,startClass,defPermissionType) 
	values ('contaluz-gestor','gestor','contaluz_inicio','allow');

insert into userRoles(usr,app,role)
	select users.usr, 'contaluz-gestor', 'gestor' from users;	

insert into rolePermissions(app, role, objective, actions, permission) values
	('contaluz-gestor', 'gestor', 'contaluz_inicio', null, 'allow');

