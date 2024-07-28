create table users
(
    id       int auto_increment,
    email    varchar(255) not null,
    password varchar(255) not null,
    constraint users_pk
        primary key (id),
    constraint users_email
        unique (email)
)
    collate = utf8mb4_bin;
