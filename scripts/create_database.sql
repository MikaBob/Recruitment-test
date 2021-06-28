SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

--
-- Table structure for table `user`
--
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
    `id` int(11) NOT NULL,
    `firstName` varchar(255) DEFAULT NULL,
     `lastName` varchar(255) DEFAULT NULL,
     `email` varchar(255) NOT NULL,
     `password` varchar(255) NOT NULL,
     `creationDate` datetime NOT NULL,
    `lastLogin` datetime DEFAULT NULL,
     `dynamicFields` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `user` (`id`, `firstName`, `lastName`, `email`, `password`, `creationDate`, `lastLogin`, `dynamicFields`) VALUES
(1, 'admin', 'admin', 'admin@blexr.com', '$2y$10$ztxJP7yjWVTe0Mv/YJgxVeINRrQJnS.ZVpGjmSo7Jn4NxUjptTQS2', '2021-06-01 13:40:17', '0000-00-00 00:00:00', NULL);


ALTER TABLE `user`
    ADD PRIMARY KEY (`id`);

ALTER TABLE `user`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;
