CREATE TABLE IF NOT EXISTS `stats` (
`CommonName` text NOT NULL,
`RealAddress` text NOT NULL,
`BytesReceived` text NOT NULL,
`BytesSent` text NOT NULL,
`Since` text NOT NULL,
`VirtualAddress` text NOT NULL,
`LastRef` text NOT NULL,
`updated` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

* * * * * cd [cron location]; php cron.php