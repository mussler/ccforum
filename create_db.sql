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
content varchar(1024) not null,
subject varchar(256) not null,
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


#user insert
	insert into user (alias, pwd, type, lastonline) values ('ToyahTheWriter', sha1('test'), 'A', CURRENT_TIMESTAMP);
	insert into user (alias, pwd, type, lastonline) values ('Perfect7', sha1('test'), 'A', CURRENT_TIMESTAMP);
	insert into user (alias, pwd, type, lastonline) values ('GentleRain', sha1('test'), 'O', CURRENT_TIMESTAMP);
	insert into user (alias, pwd, type, lastonline) values ('LA', sha1('test'), 'O', CURRENT_TIMESTAMP);
	insert into user (alias, pwd, type, lastonline) values ('kad613', sha1('test'), 'O', CURRENT_TIMESTAMP);
#post input;

INSERT INTO post (author, clocked, content, subject, udel) VALUES(1, '2014-08-06 01:11:44', 'What is your favorite part of working and earning money online? My favorite part is that I can set my work schedule around my life and not the other way around. I also love being able to stop what I am doing and go play with my kids or start dinner.', 'What is your favorite part of working online?', 0);
INSERT INTO post (author, clocked, content, subject, udel) VALUES(2, '2014-08-06 01:14:46', 'My favorite part would be not having to leave the house. The work scheduling benefits are true, but also can be a downside, as it is sometimes difficult to not let the work get shoved aside for &#34;emergencies&#34; my children have .', 'RE: What is your favorite part of working online?', 0);
INSERT INTO post (author, clocked, content, subject, udel) VALUES(3, '2014-08-06 01:18:21', 'Aside from the flexibility that ToyahTheWriter and Perfect7 already mentioned, I would add that I love the simple fact that I can work wherever I like.  Much of the time I do operate from my home office, but when the weather is fine, it is a joy.', 'RE: What is your favorite part of working online?', 0);
INSERT INTO post (author, clocked, content, subject, udel) VALUES(3, '2014-08-06 01:21:17', 'Money that is. Are you happy with what you have? Would $100,000 make you happy? How about one million or one billion?\r\n \r\nIn the news you hear about people complaining about how others have so much while others have so little.', 'How Much Would Make You Happy?', 0);
INSERT INTO post (author, clocked, content, subject, udel) VALUES(4, '2014-08-06 01:22:09', 'We could use a little bit more money every month. With two teenagers it&#39;s hard to keep money in the bank. We live paycheck to paycheck, which can be stressful at times. $100,000 would be a wonderful thing, even $10,000 would be nice.', 'RE: How Much Would Make You Happy?', 0);
INSERT INTO post (author, clocked, content, subject, udel) VALUES(5, '2014-08-06 01:22:40', 'My husband is self-employed, in a service business. We do not get a regular paycheck, but the money trickles in as jobs are done. Having enough money to cover a month&#39;s worth of bills, food, and gas in the bank at any given time would be ideal.', 'RE: RE: How Much Would Make You Happy?', 0);
#thred input;
INSERT INTO `thread` (`authororg`, `clockedorg`, `author`, `clocked`) VALUES(1, '2014-08-06 01:11:44', 2, '2014-08-06 01:14:37');
INSERT INTO `thread` (`authororg`, `clockedorg`, `author`, `clocked`) VALUES(1, '2014-08-06 01:11:44', 2, '2014-08-06 01:14:46');
INSERT INTO `thread` (`authororg`, `clockedorg`, `author`, `clocked`) VALUES(1, '2014-08-06 01:11:44', 3, '2014-08-06 01:16:48');
INSERT INTO `thread` (`authororg`, `clockedorg`, `author`, `clocked`) VALUES(1, '2014-08-06 01:11:44', 3, '2014-08-06 01:16:56');
INSERT INTO `thread` (`authororg`, `clockedorg`, `author`, `clocked`) VALUES(1, '2014-08-06 01:11:44', 3, '2014-08-06 01:18:21');
INSERT INTO `thread` (`authororg`, `clockedorg`, `author`, `clocked`) VALUES(3, '2014-08-06 01:21:17', 4, '2014-08-06 01:22:09');
INSERT INTO `thread` (`authororg`, `clockedorg`, `author`, `clocked`) VALUES(4, '2014-08-06 01:22:09', 5, '2014-08-06 01:22:40');