-- id 0
insert into installation (id, program, databasename, description) 
	values ("syslogin","syslogin","adminv3","Login del sistema");

-- id 1
insert into installation (id, program, databasename, description) 
	values ("sysadmin","sysadmin","adminv3","Administración del sistema");

-- id 2
insert into installation (id, program, databasename, description) 
	values ("corere", "corere","corere2","Control Recargar Recaudación");

-- id 3
insert into installation (id, program, databasename, description) 
	values ("vigilancia", "vigilancia","vigilancia","Control de vigilancia");

-- id 4
insert into installation (id, program, databasename, description) 
	values ("comercial", "comercial","comercial","Control de ventas");

-- id 5
insert into installation (id, program, databasename, description) 
	values ("flota", "flota","flota","Control de gasto de vehículos");

-- id 6
insert into installation (id, program, databasename, description) 
	values ("test", "test","test","Test de aplicaciones");

-- id 7
insert into installation (id, program, databasename, description) 
	values ("alquileres", "alquileres","alquileres","Gestión de alquileres");

-- id 8
insert into installation (id, program, databasename, description) 
	values ("cluz_gestor", "contaluz/gestor","contaluz","Contaluz - Gestor");

-- id 9
insert into installation (id, program, databasename, description) 
	values ("cluz_consultor", "contaluz/consultor","contaluz","Contaluz - Consultor");

-- id 10
insert into installation (id, program, databasename, description) 
	values ("cluz_usuario", "contaluz/usuario","contaluz","Contaluz - Usuario");

-- id 11
insert into installation (id, program, databasename, description) 
	values ("holiday", "holiday/pda","holiday","Holiday World - PDA");

-- id 12
insert into installation (id, program, databasename, description) 
	values ("bingos", "bingos","admision","Estudio entradas bingo");

-- id 13
insert into installation (id, program, databasename, description) 
	values ("transfers", "transfers","transfers","Importación a Navision");

-- id 14
insert into installation (id, program, databasename, description) 
	values ("averia", "averia","averia","Seguimiento averías");


insert into useraccess (installation, user) values("cluz_gestor","dmelian");
insert into useraccess (installation, user) values("cluz_consultor","dmelian");
insert into useraccess (installation, user) values("cluz_usuario","dmelian");
insert into useraccess (installation, user) values("transfers","dmelian");
insert into useraccess (installation, user) values("test", "dmelian");

insert into useraccess (installation, user) values("cluz_gestor","root");
insert into useraccess (installation, user) values("cluz_consultor","root"); 
insert into useraccess (installation, user) values("cluz_usuario","root");

