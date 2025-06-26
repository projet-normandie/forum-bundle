SELECT COUNT(*) AS nbr_doublon, user_id, topic_id, MIN(id) as min_id
FROM pnf_topic_user
GROUP BY user_id, topic_id
HAVING COUNT(*) > 1

DELETE t1 FROM pnf_topic_user t1
INNER JOIN pnf_topic_user t2
WHERE t1.id > t2.id
AND t1.user_id = t2.user_id
AND t1.topic_id = t2.topic_id;