/* sync platform trigger */
DROP TRIGGER fb_ds_datastore;
CREATE TRIGGER fb_ds_datastore AFTER INSERT ON fnbl_device_datastore
  FOR EACH ROW BEGIN
    DELETE FROM fnbl_device_datastore WHERE id = NEW.id;
  END;