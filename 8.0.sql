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
type enum('O', 'A') not null,
primary key(uid),
unique(alias)
);
create table post
(
author int  not null,
clocked datetime  not null,
content varchar(256) not null,
subject varchar(128) not null,
udel boolean default false,
foreign key(author) references user(uid) on delete cascade,
primary key(author, clocked)
);
create table thread
	(	

		authororg int null,
		clockedorg datetime null,
		author int not null,
		clocked datetime not null,
		primary key(author, clocked)
		);
create table image
	(
		id int primary key auto_increment,
		imageitself longblob not null,
		owner int not null unique,
		copyrightrestricted boolean default false,
		caption varchar(64),
		alttext varchar(16),
		avatar blob,
		mimetype varchar(32) not null,
		foreign key(owner) references user(uid) on delete cascade
		);
	create table has(
		id int not null unique,
		author int not null,
		clocked datetime not null,
		primary key(author, clocked),
		foreign key(author, clocked) references post(author, clocked),
		foreign key(id) references image(id)
		);
#triggers
drop trigger if exists delete_thread;
delimiter $$
create trigger delete_thread
after delete on thread
for each row
begin
delete from post where post.author = old.author and post.clocked = old.clocked;
end
$$
delimiter ;
drop trigger if exists delete_post;
delimiter $$
create trigger delete_post
after delete on post
for each row
begin
delete from thread where thread.authororg = old.author and thread.clockedorg = old.clocked;
end
$$
delimiter ;

#user insert
	insert into user (alias, pwd, type, lastonline) values ('test', sha1('test'), 'A', CURRENT_TIMESTAMP);
	insert into user (alias, pwd, type, lastonline) values ('test2', sha1('test'), 'A', CURRENT_TIMESTAMP);
	insert into user (alias, pwd, type, lastonline) values ('test3', sha1('test'), 'O', CURRENT_TIMESTAMP);
	insert into user (alias, pwd, type, lastonline) values ('test4', sha1('test'), 'O', CURRENT_TIMESTAMP);
	insert into user (alias, pwd, type, lastonline) values ('test5', sha1('test'), 'O', CURRENT_TIMESTAMP);

#post insert
	insert into post(author, clocked, content, subject) values (1, '2014-08-05 20:18:51', 'x1', 'x1');
	insert into post(author, clocked, content, subject) values (1, '2014-08-05 20:18:52', 'x2', 'x2');
	insert into post(author, clocked, content, subject) values (1, '2014-08-05 20:18:53', 'x3', 'x3');
	insert into post(author, clocked, content, subject) values (2, '2014-08-05 20:18:54', 'x4', 'x4');
	insert into post(author, clocked, content, subject) values (2, '2014-08-05 20:18:55', 'x5', 'x5');
	insert into post(author, clocked, content, subject) values (3, '2014-08-05 20:18:56', 'x6', 'x6');

#thread insert
	insert into thread values(1, '2014-08-05 20:18:51', 1, '2014-08-05 20:18:52');
	insert into thread values(1, '2014-08-05 20:18:51', 1, '2014-08-05 20:18:53');
	insert into thread values(1, '2014-08-05 20:18:53', 2, '2014-08-05 20:18:54');
	insert into thread values(1, '2014-08-05 20:18:53', 2, '2014-08-05 20:18:55');
	insert into thread values(1, '2014-08-05 20:18:51', 3, '2014-08-05 20:18:56');