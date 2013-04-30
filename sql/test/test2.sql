create table transaccion(
	transaccion int not null auto_increment,
	cuenta varchar(16) not null,
	tipo enum('debe','haber') not null,
	importe double not null,
	primary key (transaccion)
)engine InnoDB;

create table liquidacion(
	liquidacion int not null auto_increment,
	transacciondebe int null,
	transaccionhaber int null,
	importe double not null,
	primary key (liquidacion)
)engine InnoDB;

insert into transaccion(transaccion, cuenta, tipo, importe) values (1, '100', 'haber', -1000);
insert into transaccion(transaccion, cuenta, tipo, importe) values (2, '101', 'haber', -1000);
insert into transaccion(transaccion, cuenta, tipo, importe) values (3, '102', 'haber', -1000);
insert into transaccion(transaccion, cuenta, tipo, importe) values (4, '100', 'haber', -1000);
insert into transaccion(transaccion, cuenta, tipo, importe) values (5, '101', 'haber', -1000);
insert into transaccion(transaccion, cuenta, tipo, importe) values (6, '100', 'haber', -1000);
insert into transaccion(transaccion, cuenta, tipo, importe) values (7, '101', 'haber', -1000);
insert into transaccion(transaccion, cuenta, tipo, importe) values (8, '102', 'haber', -1000);
insert into transaccion(transaccion, cuenta, tipo, importe) values (9, '100', 'haber', -1000);

insert into transaccion(transaccion, cuenta, tipo, importe) values (11, '100', 'debe', 1000);
insert into transaccion(transaccion, cuenta, tipo, importe) values (12, '101', 'debe', 1000);
insert into transaccion(transaccion, cuenta, tipo, importe) values (13, '102', 'debe', 1000);
insert into transaccion(transaccion, cuenta, tipo, importe) values (14, '100', 'debe', 1000);
insert into transaccion(transaccion, cuenta, tipo, importe) values (15, '101', 'debe', 1000);
insert into transaccion(transaccion, cuenta, tipo, importe) values (16, '100', 'debe', 1000);
insert into transaccion(transaccion, cuenta, tipo, importe) values (17, '101', 'debe', 1000);
insert into transaccion(transaccion, cuenta, tipo, importe) values (18, '102', 'debe', 1000);
insert into transaccion(transaccion, cuenta, tipo, importe) values (19, '100', 'debe', 1000);

insert into liquidacion(transacciondebe, transaccionhaber, importe) values (11,1,1000);
insert into liquidacion(transacciondebe, transaccionhaber, importe) values (12,2,1000);
insert into liquidacion(transacciondebe, transaccionhaber, importe) values (13,3,1000);
insert into liquidacion(transacciondebe, transaccionhaber, importe) values (14,4,1000);
insert into liquidacion(transacciondebe, transaccionhaber, importe) values (15,5,1000);
insert into liquidacion(transacciondebe, transaccionhaber, importe) values (16,6,1000);
insert into liquidacion(transacciondebe, transaccionhaber, importe) values (17,7,1000);
insert into liquidacion(transacciondebe, transaccionhaber, importe) values (18,8,1000);
insert into liquidacion(transacciondebe, transaccionhaber, importe) values (19,9,1000);


