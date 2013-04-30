-- A lookup for more than one field

create table master(
	master int not null auto_increment,
	fk1 int,
	fk2 int,
	data varchar(12),
	primary key (master),
	foreign key (fk1,fk2) references doblekey (k1, k2) on delete cascade on update cascade
) Engine InnoDB;

create table doblekey(
	k1 int not null,
	k2 int not null,
	data varchar(12),
	primary key (k1,k2)
) Engine InnoDB;
	

insert into doblekey(k1,k2,data) values(1,1,'Uno');
insert into doblekey(k1,k2,data) values(1,2,'Dos');
insert into doblekey(k1,k2,data) values(1,3,'Tres');
insert into doblekey(k1,k2,data) values(2,1,'Uno-2');
insert into doblekey(k1,k2,data) values(2,2,'Dos-2');


