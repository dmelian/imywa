select 'TEST' as file;

insert into sources(source) values ('test');

insert into dbs (db) values ('test');

insert into apps(app,dbServer,source, theme) 
	values ('test_1','localhost','test', null);

insert into appDbs(app, db, dbName, main) 
	values ('test_1', 'test', 'test', true);

insert into appRoles(app,role,startClass,defPermissionType) 
	values ('test_1','user','test_inicio','allow');

insert into userRoles(usr,app,role)
	select users.usr, 'test_1', 'user' from users;	

insert into rolePermissions(app, role, objective, actions, permission) values
	('test_1', 'user', 'test_frmx', null, 'deny')
	,('test_1', 'user', 'test_frmx_listframe_form', null, 'allow')
	,('test_1', 'user', 'test_frmx_cardframe_form', null, 'allow')
	,('test_1', 'user', 'test_frmx_cardframe_form_frame1', null, 'deny')
	;

