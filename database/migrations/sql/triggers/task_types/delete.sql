
CREATE TRIGGER `after_task_types_delete`
AFTER DELETE ON `task_taskTypes_pivot`
FOR EACH ROW
BEGIN
    DECLARE task_updated_at DATETIME;
    DECLARE task_updated_by INT;
    -- Fetch `updated_at` and `updatedBy` from `tasks` for the deleted task_types
    SELECT `updated_at`, `updatedBy` INTO task_updated_at, task_updated_by
    FROM `tasks`
    WHERE `id` = OLD.`task_id`;

    INSERT INTO `task_activity_logs` (
        `taskId`,
        `action`,
        `property`,
        `oldId`,
        `created_at`,
        `updated_at`,
        `createdBy`,
        `updatedBy`
    ) VALUES (
        OLD.`task_id`,
        '1',  -- Indicates removal of a task_types
        '4',  -- 4 for tasktypes based on property
        OLD.`taskTypes_id`,
        task_updated_at,
        task_updated_at,
        task_updated_by,
        task_updated_by
    );
END;
