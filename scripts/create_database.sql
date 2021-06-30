SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

--
-- Table structure `request`
--
DROP TABLE IF EXISTS `request`;
CREATE TABLE `request` (
    `id` int(11) NOT NULL,
    `userId` int(11) NOT NULL,
    `startDate` datetime NOT NULL,
    `endDate` datetime NOT NULL,
    `status` enum('PENDING','REJECTED','APPROUVED') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure `user`
--
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
    `id` int(11) NOT NULL,
    `firstName` varchar(255) DEFAULT NULL,
     `lastName` varchar(255) DEFAULT NULL,
     `email` varchar(255) NOT NULL UNIQUE,
     `password` varchar(255) NOT NULL,
     `creationDate` datetime NOT NULL,
    `lastLogin` datetime DEFAULT NULL,
     `dynamicFields` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `user` (`id`, `firstName`, `lastName`, `email`, `password`, `creationDate`, `lastLogin`, `dynamicFields`)
VALUES (
    1, 'Admin', 'Admin', 'admin@blexr.com', '$2y$10$ztxJP7yjWVTe0Mv/YJgxVeINRrQJnS.ZVpGjmSo7Jn4NxUjptTQS2', '2021-06-01 13:40:17', '0000-00-00 00:00:00', '{"MOL":true,"EAG":true,"GRG":true,"JAG":true}'
);

INSERT INTO `user` (`id`, `firstName`, `lastName`, `email`, `password`, `creationDate`, `lastLogin`, `dynamicFields`)
VALUES (
    2, 'User', 'User', 'user@blexr.com', '$2y$10$F.kUrEnrb9025Karx6ddNe496pDSYNoB2aZlHzgvLJ6.FnCS36L.W', '2021-06-01 13:40:17', '0000-00-00 00:00:00', '{"MOL":true,"EAG":true,"GRG":true,"JAG":true}'
);

--
-- Table indexes for table `request`
--
ALTER TABLE `request`
    ADD PRIMARY KEY (`id`),
    ADD KEY (`userId`);

--
-- Table indexes for table `user`
--
ALTER TABLE `user`
    ADD PRIMARY KEY (`id`);

--
-- Auto increment for table `request`
--
ALTER TABLE `request`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Auto increment for table `user`
--
ALTER TABLE `user`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

COMMIT;

--
-- Table constraints for table `request`
--
ALTER TABLE `request`
    ADD CONSTRAINT FOREIGN KEY (`userId`) REFERENCES `user` (`id`);
    SET FOREIGN_KEY_CHECKS=1;
COMMIT;