delimiter $$

drop procedure if exists db_insert$$
create procedure db_insert(
	in idb varchar(20),
	in icaption varchar(32),
	in idescrip text,
	in iengine varchar(20)
)
begin
	set idb = lower(idb);

	insert into db(db, caption, descrip, engine)
		values (idb, icaption, idescrip, iengine);

	select 0 as error, 'database $_db inserted.' as message, idb as db;
end
$$


drop procedure if exists db_modify$$
create procedure db_modify(
	in xdb varchar(20),
	in idb varchar(20),
	in icaption varchar(32),
	in idescrip text,
	in iengine varchar(20)
)
begin
	declare mxdb varchar(20);
	declare mydb varchar(20);

	select db into mxdb from db where db = xdb;
	select db into mydb from db where db = idb;
	
	set idb = lower(idb);
	set xdb = lower(xdb);

	if mxdb is null then
		select 1 as error, 'database $_db not found.' as message, xdb as db;

	elseif not mydb is null and mxdb <> mydb then
		select 1 as error, 'database $_db already exists.' as message, idb as db;

	else	

		update db
			set db = idb, caption = icaption, descrip = idescrip, engine = iengine
			where db = xdb;

	select 0 as error, 'database $_db modified.' as message, xdb as db;
end
$$


drop procedure if exists db_delete$$
create procedure db_delete(
	in idb varchar(20)
)
begin
	declare mydb varchar(20);
	select db into mydb from db where db = idb;
	
	set idb = lower(idb);

	if mydb is null then 
		select 1 as error, 'database $_db not found.' as message, idb as db;

	else 
		delete from db where db = idb;
		select 0 as error, 'database $_db deleted.' as message, idb as db;

	end if;	
end
$$



delimiter ;
