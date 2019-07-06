SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `commands` (
  `id` int(11) NOT NULL,
  `command` varchar(64) NOT NULL,
  `arguments` text NOT NULL,
  `description` text NOT NULL,
  `level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `user` varchar(32) NOT NULL,
  `modifier` int(11) NOT NULL,
  `item` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `description` varchar(128) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `item` varchar(64) NOT NULL,
  `value` int(11) NOT NULL,
  `weight` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `modifiers` (
  `id` int(11) NOT NULL,
  `description` varchar(64) NOT NULL,
  `value` int(11) NOT NULL,
  `weight` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `settings` (
  `setting` varchar(32) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `tips` (
  `id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user` varchar(32) NOT NULL,
  `message` text NOT NULL,
  `status` enum('PENDING','APPROVED','REJECTED','') NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `commands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `command` (`command`);

ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`),
  ADD KEY `modifier` (`modifier`),
  ADD KEY `item` (`item`),
  ADD KEY `description` (`description`);

ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `weight` (`weight`);

ALTER TABLE `modifiers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `weight` (`weight`);

ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting`);

ALTER TABLE `tips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`),
  ADD KEY `status` (`status`),
  ADD KEY `time` (`time`);


ALTER TABLE `commands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `modifiers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `tips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
