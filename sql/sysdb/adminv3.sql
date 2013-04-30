-- create database adminv3;

create table host (
	name varchar(16),
	ip varchar(15),
	primary key (name)
);

create table program (
	name varchar(16),
	primary key (name)
);

create table rol(
	program varchar(16),
	rol varchar(16),
	primary key (program, rol)
);

create table action(
	program varchar(16),
	action varchar(16),
	description varchar (32),
	rol varchar (32),
	primary key (program, action)
);

create table user(
	name varchar(16),
	primary key (name)
);

create table installation(
	id varchar(16),
	host varchar(16),
	databasename varchar(16), 
	program varchar(16),
	description varchar(32),
	primary key (id)
);

create table useraccess(
	installation varchar(16),
	user varchar(16),
	mainform varchar(16),
	primary key (user, installation)
);

create table userrol(
	installation varchar(16),
	user varchar(16),
	rol varchar(16),
	primary key (installation, user, rol)
);




