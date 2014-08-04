drop table if exists has;
drop table if exists thread;
drop table if exists post;
drop table if exists image;
drop table if exists user;
create table user 
(
uid int not null auto_increment,
alias varchar(16) not null,
pwd blob not null,
lastonline datetime not null,
type enum('A', 'O'),
primary key(uid),
unique(alias)
);
create table post
(
author int not null,
clocked datetime not null,
content varchar(256),
subject varchar(128),
primary key(author, clocked),
foreign key(author) references user(uid)
);
create table thread
	(
		authororg int not null,
		clockedorg datetime not null,
		author int not null,
		clocked datetime not null,
		primary key(author, clocked),
		unique(authororg, clockedorg),
		foreign key(authororg, clockedorg) references post(author, clocked)
		);
create table image
	(
		id int primary key auto_increment,
		imageitself blob not null,
		owner varchar(16) not null unique,
		copyrightrestricted boolean default false,
		caption varchar(64),
		alttext varchar(16),
		avatar blob,
		mimetype varchar(32) not null,
		foreign key(owner) references user(alias)
		);
	create table has(
		id int not null unique,
		author int not null,
		clocked datetime not null,
		primary key(author, clocked),
		foreign key(author, clocked) references post(author, clocked),
		foreign key(id) references image(id)
		);
#user insert
	insert into user (alias, pwd, type, lastonline) values ('Patrick', sha1('password'), 'A', CURRENT_TIMESTAMP);
	insert into user (alias, pwd, type, lastonline) values ('Niels', sha1('password'), 'A', CURRENT_TIMESTAMP);
	insert into user (alias, pwd, type, lastonline) values ('Ginta', sha1('password'), 'O', CURRENT_TIMESTAMP);
	insert into user (alias, pwd, type, lastonline) values ('Patricia', sha1('password'), 'O', CURRENT_TIMESTAMP);
	insert into user (alias, pwd, type, lastonline) values ('Jozeph', sha1('password'), 'O', CURRENT_TIMESTAMP);
	insert into user (alias, pwd, type, lastonline) values ('test', sha1('test'), 'A', CURRENT_TIMESTAMP);
	insert into user (alias, pwd, type, lastonline) values ('test2', sha1('test2'), 'O', CURRENT_TIMESTAMP);
#post insert
	insert into post values (1, CURRENT_TIMESTAMP, 'CONTENT FOR POST #1', 'POST #1');
	insert into post values (2, CURRENT_TIMESTAMP, 'CONTENT FOR POST #2', 'POST #2');
	insert into post values (3, CURRENT_TIMESTAMP, 'CONTENT FOR POST #3', 'POST #3');
	insert into post values (4, CURRENT_TIMESTAMP, 'CONTENT FOR POST #4', 'POST #4');
	insert into post values (5, CURRENT_TIMESTAMP, 'CONTENT FOR POST #5', 'POST #5');