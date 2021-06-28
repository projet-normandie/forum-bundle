-- select
SELECT * FROM `forum_message` WHERE `message` LIKE '%\\\\''%';

-- update
UPDATE forum_message SET message = replace(message, '\\', '')
WHERE `message` LIKE '%\\\\''%';