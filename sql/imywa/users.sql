create table themes(
	theme varchar(20),
	primary key(theme)
) engine InnoDB, default character set utf8;


create table dbServers(
	dbServer varchar(20) not null,
	primary key(dbServer)
) engine InnoDB, default character set utf8;

create table sources(
	source varchar(20) not null,
	primary key(source)
) engine InnoDB, default character set utf8;


create table apps(
	app varchar(20) not null,
	dbServer varchar(20) null,
	theme varchar(20) null,
	source varchar(20) null,
	primary key (app),
	foreign key (theme) references themes(theme) on delete set null on update set null,
	foreign key (dbServer) references dbServers(dbServer) on delete set null on update set null,
	foreign key (source) references sources(source) on delete set null on update set null
) engine InnoDB, default character set utf8;


create table dbs(
	db varchar(40) not null,
	primary key (db)
) engine InnoDB, default character set utf8;


create table dbPartitions(
	db varchar(40) not null,
	partitionLevel integer not null,
	partitionId varchar(20) null,
	primary key (db, partitionLevel),
	foreign key (db) references dbs(db) on delete cascade on update cascade
) engine InnoDB, default character set utf8;

create table appDbs(
	app varchar(20) not null,
	db varchar(40) not null,
	dbName varchar(40) not null,
	main boolean not null,
	primary key (app, db),
	foreign key (app) references apps(app) on delete cascade on update cascade,
	foreign key (db) references dbs(db) on delete cascade on update cascade
) engine InnoDB, default character set utf8;


create table appRoles(
	app varchar(20) not null,
	role varchar(20) not null,
	theme varchar(20),
	startClass varchar(40) not null,
	defPermissionType enum('deny','allow'),
	primary key (app, role),
	foreign key (app) references apps(app) on delete cascade on update cascade,
	foreign key (theme) references themes(theme) on delete set null on update set null
) engine InnoDB, default character set utf8;

create table rolePermissions(
	app varchar(20) not null,
	role varchar(20) not null,
	objective varchar(60) not null,
	actions varchar(80),
	permission enum('deny','allow','except','only') not null,
	primary key(app, role, objective),
	foreign key (app, role) references appRoles(app, role) on delete cascade on update cascade
) engine InnoDB, default character set utf8;

create table languages(
	language varchar(20),
	name varchar(40),
	primary key(language)
) engine InnoDB, default character set utf8;


create table users(
	usr varchar(20) not null,
	theme varchar(20),
	language varchar(10),
	defApp varchar(20),
	primary key(usr),
	foreign key (language) references languages(language) on delete set null on update set null,
	foreign key (theme) references themes(theme) on delete set null on update set null,
	foreign key (defApp) references apps(app) on delete set null on update set null
) engine InnoDB, default character set utf8;


create table userRoles(
	usr varchar(20) not null,
	app varchar(20) not null,
	role varchar(20) not null,
	primary key (usr,app),
	foreign key (usr) references users(usr) on delete cascade on update cascade,
	foreign key (app, role) references appRoles(app, role) on delete cascade on update cascade
) engine InnoDB, default character set utf8;


create table sessions(
	sessionId varchar(20) not null,
	usr varchar(20) not null,
	createTime datetime not null,
	lastActiveTime time not null,
	active boolean not null,
	primary key(sessionId),
	index (usr, sessionId),
	foreign key (usr) references users(usr) on delete cascade on update cascade
) engine InnoDB, default character set utf8;

