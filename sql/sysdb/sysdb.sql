create table db(
	db varchar(20) not null,
	caption varchar(32) not null,
	descrip text null,
	engine varchar(20) null,
	primary key (db)
) engine InnoDB, default character set utf8;

create table ftype(
	ftype varchar(20) not null,
	descrip text null,	
	defimplement varchar(32) null,
	primary key(ftype)
) engine InnoDB, default character set utf8;
	
create table dbftype(
	db varchar(20) not null,
	ftype varchar(20) not null,
	implement varchar(32) null,
	
	primary key(db, ftype),
	foreign key (db) references db(db) on delete cascade on update cascade,
	foreign key (ftype) references ftype(ftype) on delete cascade on update cascade
) engine InnoDB, default character set utf8;

create table tb(
	db varchar(20) not null,
	tb varchar(20) not null,
	caption varchar(32) not null,
	descrip text null,
	primary key(db, tb),
	foreign key(db) references db(db) on delete cascade on update cascade
) engine InnoDB, default character set utf8;

create table fd(
	db varchar(20) not null,
	tb varchar(20) not null,
	fd varchar(20) not null,
	caption varchar(32) not null,
	ftype varchar(20) null,
	
	primary key (db, tb, fd),
	foreign key(db) references db(db) on delete cascade on update cascade,
	foreign key(tb) references db(tb) on delete cascade on update cascade,
	foreign key (ftype) references ftype(ftype) on delete set null on update cascade

) engine InnoDB, default character set utf8;

