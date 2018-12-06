SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `veiculos` (
  `id` int(11) NOT NULL,
  `veiculo` varchar(60) CHARACTER SET utf8 NOT NULL,
  `marca` varchar(60) CHARACTER SET utf8 NOT NULL,
  `ano` int(11) NOT NULL,
  `descricao` text CHARACTER SET utf8 NOT NULL,
  `vendido` tinyint(1) NOT NULL DEFAULT '0',
  `imagem` longblob NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `veiculos`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `veiculos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;
