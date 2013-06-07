create table db(
	id integer not null,
	revision integer not null,
	name varchar(20) not null,
	deleted boolean not null,
	primary key(id, revision)
) engine InnoDB, default character set utf8;

create table databasedivisions(
	db integer not null
	
) engine InnoDB, default character set utf8;

create table tb(
	id integer not null,
	revision integer not null,
	db integer not null,	
	name varchar(20) not null,
	deleted boolean not null,
	primary key(id, revision),
	foreign key db(id) references db(id) on delete restrict on update restrict
) engine InnoDB, default character set utf8;

