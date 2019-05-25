-- MAJ idMesageMax
UPDATE forum_forum
SET idMessageMax = null;
UPDATE forum_topic
SET idMessageMax = 0;

UPDATE forum_topic t
SET t.idMessageMax = (SELECT MAX(id) FROM forum_message WHERE idTopic = t.id);
