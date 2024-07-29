create table reservations
(
    id       int auto_increment,
    user_id  int  not null,
    room_id  int  not null,
    workday  date not null,
    slot     int  not null,
    duration int  not null,
    constraint reservations_pk
        primary key (id),
    constraint reservations_users_id_fk
        foreign key (user_id) references users (id),
    constraint reservations_rooms_id_fk
        foreign key (room_id) references rooms (id)
)
    collate = utf8mb4_bin;
