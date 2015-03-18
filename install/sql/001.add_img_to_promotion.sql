USE bhw_pc;

ALTER TABLE kz_promotion ADD COLUMN img varchar(255)  null default null;  
ALTER TABLE kz_promotion ADD COLUMN goods_id int(11) unsigned  null default null;