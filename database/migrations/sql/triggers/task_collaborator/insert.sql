CREATE TRIGGER `after_task_collaborator_insert`
AFTER INSERT ON `task_collaborators`
FOR EACH ROW
BEGIN
    DECLARE previous_collaborator_count INT;

    -- Get the count of collaborators for the taskId
    SELECT SUM(tc.count) INTO previous_collaborator_count
    FROM (
        SELECT COUNT(DISTINCT `created_at`) AS count
        FROM `task_collaborators`
        WHERE `taskId` = NEW.`taskId`
        GROUP BY `created_at`
    ) AS tc;

    IF previous_collaborator_count > 1 THEN
        INSERT INTO `task_activity_logs` (
            `taskId`,
            `action`,
            `property`,
            `newId`,
            `created_at`,
            `updated_at`,
            `createdBy`,
            `updatedBy`
        )
        VALUES (
            NEW.`taskId`,
            '0',                             -- Indicates insertion of a collaborator
            IF(NEW.`flag` = '0', '0', '1'),  -- 0 for assignee, 1 for reviewer based on property
            NEW.`collaborator`,
            NEW.`updated_at`,
            NEW.`updated_at`,
            NEW.`updatedBy`,
            NEW.`updatedBy`
        );
    END IF;
END;

