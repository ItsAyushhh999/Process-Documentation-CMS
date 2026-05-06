
CREATE TRIGGER `after_task_types_insert`
AFTER INSERT ON `task_taskTypes_pivot`
FOR EACH ROW
BEGIN
    DECLARE tasktype_count INT;
    -- Get the count of task types for the taskId
    SELECT SUM(tp.count) INTO tasktype_count
    FROM (SELECT COUNT(DISTINCT `created_at`) as count
        FROM `task_taskTypes_pivot`
        WHERE `task_id` = NEW.`task_id`
        GROUP BY created_at)
    as tp;

    IF tasktype_count > 1 THEN
        INSERT INTO `task_activity_logs` (
            `taskId`,
            `action`,
            `property`,
            `newId`,
            `created_at`,
            `updated_at`,
            `createdBy`,
            `updatedBy`
        ) VALUES (
            NEW.`task_id`,
            '0',   -- Indicates insertion of a task_types
            '4',   -- 4 for tasktypes based on property
            NEW.`taskTypes_id`,
            NEW.`updated_at`,
            NEW.`updated_at`,
            NEW.`updatedBy`,
            NEW.`updatedBy`
            );
        END IF;
    END;
