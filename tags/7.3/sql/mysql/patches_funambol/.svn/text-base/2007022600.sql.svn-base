DROP TRIGGER IF EXISTS fb_ds;
DELIMITER |
CREATE TRIGGER fb_ds AFTER UPDATE ON fnbl_last_sync
  FOR EACH ROW BEGIN
		DELETE FROM fnbl_device_datastore;
		DELETE FROM fnbl_ds_cttype_rx;
		DELETE FROM fnbl_ds_cttype_tx;
		DELETE FROM fnbl_ds_mem;
  END;
|
DELIMITER ;