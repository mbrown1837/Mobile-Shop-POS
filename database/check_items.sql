-- Check what items exist in database
SELECT id, name, code, item_type FROM items WHERE item_type = 'serialized' ORDER BY id;
