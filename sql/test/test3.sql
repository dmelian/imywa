create table ajaxtest(
	id integer auto_increment,
	name varchar(20),
	description varchar(80),
	primary key (id)
);

create table ajaxtest2(
	id integer auto_increment,
	id1 integer,
	name varchar(20),
	description varchar(80),
	primary key (id)
);

insert into ajaxtest(name, description) values('ball', 'a rounded thing the children uses to play with');
insert into ajaxtest(name, description) values('doll', 'a little wood woman to simulate living');
insert into ajaxtest(name, description) values('saturn', 'a planet');
insert into ajaxtest(name, description) values('joystick', 'a stick for controlling the game');
insert into ajaxtest(name, description) values('pedal', 'to go on it');
insert into ajaxtest(name, description) values('bicicle', 'vehicle with two ruedas');
insert into ajaxtest(name, description) values('aeroplane', 'a vehicle that files');
insert into ajaxtest(name, description) values('ship', 'a vehicle that swim');
insert into ajaxtest(name, description) values('matrix', 'a organizated set of things');
insert into ajaxtest(name, description) values('battle', 'a disorganizated confront of intereses');
insert into ajaxtest(name, description) values('bat', 'like a mouse that flies');
insert into ajaxtest(name, description) values('chipiron', 'a little squid');
insert into ajaxtest(name, description) values('estacada', 'everybody go away');
insert into ajaxtest(name, description) values('squid', 'like a octopusy');
insert into ajaxtest(name, description) values('shark', 'a very big fish who eats human bodies');
insert into ajaxtest(name, description) values('lion', 'the king of the jungle.');
insert into ajaxtest(name, description) values('elephant', 'the biggest animal in the jungle');
insert into ajaxtest(name, description) values('chair', 'a thing where your ass rests');
insert into ajaxtest(name, description) values('table', 'a thing on what the people eats');
insert into ajaxtest(name, description) values('pencil', 'a tool for writing');
insert into ajaxtest(name, description) values('eraser', 'a tool for deleting your writing');
insert into ajaxtest(name, description) values('film', 'a movie');
insert into ajaxtest(name, description) values('shoe', 'a thing you put on your feet');
insert into ajaxtest(name, description) values('blue', 'this beautiful colour');
insert into ajaxtest(name, description) values('guitar', 'an amazing musical instrument');
insert into ajaxtest(name, description) values('chord', 'a set of notes');
insert into ajaxtest(name, description) values('jazz', 'a style of doing music');


insert into ajaxtest2(id1, name, description) 
	select id,name,description from ajaxtest;
