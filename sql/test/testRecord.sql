create table prueba_record(
	N_Factura integer unsigned,
	N_Linea  integer unsigned,
	id_Cliente varchar(20),
	telefono varchar(9),
	cantidad integer,
	fecha  date,
	primary key (N_Factura, N_Linea)
);

insert into prueba_record(N_Factura,N_Linea, id_Cliente,telefono,cantidad,fecha)
    values(736749, 224,3765,'663456783',273,'2000-03-12');
insert into prueba_record(N_Factura,N_Linea, id_Cliente,telefono,cantidad,fecha)
    values(736749, 225,3765,'663456783',246,'2001-09-12');
insert into prueba_record(N_Factura,N_Linea, id_Cliente,telefono,cantidad,fecha)
    values(736749, 226,3765,'663456783',6354,'2000-08-12');
    
insert into prueba_record(N_Factura,N_Linea, id_Cliente,telefono,cantidad,fecha)
    values(735749, 224,2665,'928456783',3,'2001-03-12');
insert into prueba_record(N_Factura,N_Linea, id_Cliente,telefono,cantidad,fecha)
    values(735749, 234,2665,'928456783',983,'2010-03-12');
insert into prueba_record(N_Factura,N_Linea, id_Cliente,telefono,cantidad,fecha)
    values(735749, 2124,2665,'928456783',46778,'2000-06-12');

insert into prueba_record(N_Factura,N_Linea, id_Cliente,telefono,cantidad,fecha)
    values(936749, 2,6549,'623856783',273,'2003-03-12');
insert into prueba_record(N_Factura,N_Linea, id_Cliente,telefono,cantidad,fecha)
    values(936749, 4,6549,'623856783',652,'2005-03-12');
insert into prueba_record(N_Factura,N_Linea, id_Cliente,telefono,cantidad,fecha)
    values(936749, 1,6549,'623856783',871,'2008-03-12');   
    
insert into prueba_record(N_Factura,N_Linea, id_Cliente,telefono,cantidad,fecha)
    values(1973, 7546,1080,'928254235',663,'2000-01-12');
insert into prueba_record(N_Factura,N_Linea, id_Cliente,telefono,cantidad,fecha)
    values(1973, 212,1080,'928254235',321,'2000-03-15');
insert into prueba_record(N_Factura,N_Linea, id_Cliente,telefono,cantidad,fecha)
    values(1973, 3654,1080,'928254235',858,'2011-10-19');  
    
insert into prueba_record(N_Factura,N_Linea, id_Cliente,telefono,cantidad,fecha)
    values(2185, 9889,72153,'922365845',222,'2000-12-12');
insert into prueba_record(N_Factura,N_Linea, id_Cliente,telefono,cantidad,fecha)
    values(2185, 745,72153,'922365845',454,'2002-03-11');
insert into prueba_record(N_Factura,N_Linea, id_Cliente,telefono,cantidad,fecha)
    values(2185, 21,72153,'922365845',473,'2010-08-03');  
    
create user record@localhost identified by '12345';
grant all on test.prueba_record to record;
