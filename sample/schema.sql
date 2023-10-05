-- drop database test;
use test;

create table users(
  id integer not null primary key auto_increment,
  username varchar(255),
  password varchar(255)
);
