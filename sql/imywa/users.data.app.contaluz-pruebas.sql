select 'CONTALUZ-PRUEBAS' as file;

insert into sources(source) values ('contaluz');

insert into dbs (db) values ('cluz_cliente');

insert into apps(app,dbServer,source, theme) 
	values ('contaluz-pruebas','localhost','contaluz','amedita');

insert into appDbs(app, db, dbName, main) 
	values ('contaluz-pruebas', 'cluz_cliente', 'cluz_pruebas', true)
	,('contaluz-pruebas', 'cluz', 'cluz', false);

insert into appRoles(app,role,startClass,defPermissionType) 
	values ('contaluz-pruebas','gestor','contaluz_mapa','allow')
	, ('contaluz-pruebas','consultor','contaluz_mapa','allow');

insert into userRoles(usr,app,role)
	select users.usr, 'contaluz-pruebas', 'consultor' from users;	

insert into rolePermissions(app, role, objective, actions, permission) values
	('contaluz-pruebas', 'consultor', 'contaluz_mapa', null, 'allow');

