-- EasonSaid 数据库结构

CREATE TABLE `eason_said` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Contents` varchar(100) DEFAULT NULL,
  `Source` varchar(100) DEFAULT NULL,
  `Contributors` varchar(100) DEFAULT NULL,
  `Time` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8mb4;

-- 视图：自动计算每句歌词的分享人数和每首歌的分享次数
CREATE VIEW `eason_said_with_counts` AS
SELECT
  `es`.`id` AS `id`,
  `es`.`Contents` AS `Contents`,
  `es`.`Source` AS `Source`,
  `es`.`Contributors` AS `Contributors`,
  `es`.`Time` AS `Time`,
  ((CHAR_LENGTH(`es`.`Contributors`) - CHAR_LENGTH(REPLACE(`es`.`Contributors`, '、', ''))) + 1) AS `LyricShareCount`,
  (SELECT COUNT(0) FROM `eason_said` `es2` WHERE (`es2`.`Source` = `es`.`Source`)) AS `SongShareCount`
FROM `eason_said` `es`;
