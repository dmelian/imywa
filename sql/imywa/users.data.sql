insert into dbServers(dbServer) values ('localhost');

insert into languages(language,name) values ('es','español'),('en','english');

insert into themes(theme) values
	('amedita'),('blitzer'),('cupertino'),('custom-theme'),('dark-hive')
	,('eggplant'),('excite-bike'),('flick'),('hot-sneaks'),('humanity')
	,('le-frog'),('overcast'),('pepper-grinder'),('redmond'),('smoothness')
	,('south-street'),('start'),('sunny'),('temas'),('ui-darkness')
	,('ui-lightness'),('vader');

insert into users(usr,theme,language) values
	('root','amedita','es')
	, ('dmelian','blitzer','es');

source users.data.app.test_1.sql;
source users.data.app.contaluz-gestor.sql;
source users.data.app.contaluz-pruebas.sql;
source users.data.app.contaluz-pama.sql;
source users.data.app.transfer.sql;


update users set defApp = 'test_1';
