
CREATE TRIGGER `after_task_collaborator_delete`
AFTER DELETE ON `task_collaborators`
FOR EACH ROW
BEGIN
    DECLARE task_updated_at DATETIME;
    DECLARE task_updated_by INT;

    -- Fetch `updated_at` and `updatedBy` from `tasks` for the deleted task
    SELECT `updated_at`, `updatedBy` INTO task_updated_at, task_updated_by
    FROM `tasks`
    WHERE `id` = OLD.`taskId`;

    INSERT INTO `task_activity_logs` (
        `taskId`,
        `action`,
        `property`,
        `oldId`,
        `created_at`,
        `updated_at`,
        `createdBy`,
        `updatedBy`
    )
    VALUES (
        OLD.`taskId`,
        '1',                             -- Indicates removal of a collaborator
        IF(OLD.`flag` = '0', '0', '1'),  -- 0 for assignee, 1 for reviewer based on property
        OLD.`collaborator`,
        task_updated_at,
        task_updated_at,
        task_updated_by,
        task_updated_by
    );
END;

