create table rooms
(
    id   int auto_increment,
    name varchar(50) not null,
    constraint rooms_pk
        primary key (id),
    constraint rooms_name
        unique (name)
)
    collate = utf8mb4_bin;
