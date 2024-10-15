alter table `ktvs_dvds_groups` add column `external_id` varchar(100) NOT NULL after `status_id`;

update ktvs_options set value='5.0.1' where value='5.0.0' and variable='SYSTEM_VERSION';