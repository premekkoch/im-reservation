#fake users - all with password 12345678
insert into users values (1, 'test1@test.cz', '$2y$10$/d4hRX7CSBBi8lf2QOndxeoTz3KDMmRN76dBlJUzjDrzCbU0MJk8O');
insert into users values (2, 'test2@test.cz', '$2y$10$ZHK9og1hwOLOnLJpRWTjZuaLbOkQCqAzUPWyUOpukSqhn9/HaB.e2');
insert into users values (3, 'test3@test.cz', '$2y$10$keuh1uoJjXI68TrVIk1Z6.DTzEB5WCxCdY/W4tjQ/SJwhxCQjY/TK');

#fake reservations
insert into reservations values (1, 1, 1, NOW(), 14, 2);
insert into reservations values (2, 1, 1, NOW(), 22, 4);
insert into reservations values (3, 2, 1, NOW(), 26, 1);
insert into reservations values (4, 3, 1, NOW(), 35, 2);
insert into reservations values (5, 3, 2, NOW(), 16, 8);
insert into reservations values (6, 1, 2, NOW(), 33, 1);
insert into reservations values (7, 1, 3, NOW(), 12, 1);
insert into reservations values (8, 1, 3, NOW(), 37, 1);
insert into reservations values (9, 1, 4, NOW(), 12, 2);
insert into reservations values (10, 1, 4, NOW(), 15, 2);
insert into reservations values (11, 1, 4, NOW(), 18, 2);
