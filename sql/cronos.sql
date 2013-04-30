delimiter $$

drop procedure if exists createCronoHeaders$$
create procedure createCronoHeaders(
	in istartingDate date,
	in iinterval enum('day','week','month','year'),
-- 	in icount integer 
)
begin
	declare mydate date;
	declare myname varchar(20);
	declare icount integer;

	drop temporary table if exists cronoHeaders;	
	create temporary table cronoHeaders(
		name varchar(20),
		fromDate date,
		untilDate date,
		primary key (name)
	);

	-- initial period adjust.
	case iinterval
		when 'day' then
			set istartingDate = concat(year(istartingDate),'-',month(istartingDate),'-01');
			set icount = DAY(LAST_DAY(istartingDate));
		when 'week' then 
			set istartingDate = concat(year(istartingDate),'-01-01');
			set icount = week(concat(year(istartingDate),'-12-31'));
		when 'month' then 
			set istartingDate = concat(year(istartingDate),'-01-01');
			set icount = 12;
		when 'year' then 
			set istartingDate = concat(extract(year from istartingDate),'0101');
	end case;

	-- temporary table created.
	while icount > 0 do
		case iinterval
			when 'day' then 
				set myname = date_format(istartingDate, '%d/%m/%Y');
				set mydate = date_add(istartingDate, interval 1 day);
			when 'week' then 
				set myname = date_format(istartingDate, '%v/%x');
				set mydate = date_add(istartingDate, interval 1 week);
			when 'month' then 
				set myname = date_format(istartingDate, '%m/%Y');
				set mydate = date_add(istartingDate, interval 1 month);
			when 'year' then 
				set myname = date_format(istartingDate, '%Y');
				set mydate = date_add(istartingDate, interval 1 year);
		end case;

		insert into cronoHeaders(name, fromDate, untilDate)
			values (myname, istartingDate, date_sub(mydate, interval 1 day));

		set istartingDate= mydate;
		set icount = icount - 1;
	end while;

end
$$


delimiter ;
