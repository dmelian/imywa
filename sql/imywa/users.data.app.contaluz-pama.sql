select 'CONTALUZ-PAMA' as file;

insert into sources(source) values ('contaluz');

insert into dbs (db) values ('cluz_cliente');

insert into apps(app,dbServer,source, theme) 
	values ('contaluz-pama','localhost','contaluz','amedita');

insert into appDbs(app, db, dbName, main) 
	values ('contaluz-pama', 'cluz_cliente', 'cluz_pama', true)
	, ('contaluz-pama', 'cluz', 'cluz', false);

insert into appRoles(app,role,startClass,defPermissionType) 
	values ('contaluz-pama','gestor','contaluz_mapa','allow')
	, ('contaluz-pama','consultor','contaluz_mapa','allow');

insert into userRoles(usr,app,role)
	select users.usr, 'contaluz-pama', 'consultor' from users;	

insert into rolePermissions(app, role, objective, actions, permission) values
	('contaluz-pama', 'consultor', 'contaluz_mapa', null, 'allow');

