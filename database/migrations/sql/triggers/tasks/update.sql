
CREATE TRIGGER `log_task_update_after`
AFTER UPDATE ON `tasks`
FOR EACH ROW
BEGIN
    -- Check if the `deadline` has changed
    IF OLD.`deadline` <> NEW.`deadline` THEN
        INSERT INTO `task_activity_logs` (
            `createdBy`,
            `updatedBy`,
            `taskId`,
            `action`,
            `property`,
            `oldValue`,
            `newValue`,
            `created_at`,
            `updated_at`
        )
        VALUES (
            NEW.`updatedBy`,
            NEW.`updatedBy`,
            OLD.`id`,
            '2',  -- Indicates update on a deadline
            '2',  -- 2 for deadline based on property
            OLD.`deadline`,
            NEW.`deadline`,
            NEW.`updated_at`,
            NEW.`updated_at`
        );
    END IF;

    -- Check if the `priority` has changed
    IF OLD.`priority` <> NEW.`priority` THEN
        INSERT INTO `task_activity_logs` (
            `createdBy`,
            `updatedBy`,
            `taskId`,
            `action`,
            `property`,
            `oldId`,
            `newId`,
            `created_at`,
            `updated_at`
        )
        VALUES (
            NEW.`updatedBy`,
            NEW.`updatedBy`,
            OLD.`id`,
            '2',  -- Indicates update on a priority
            '3',  -- 3 for priority based on property
            OLD.`priority`,
            NEW.`priority`,
            NEW.`updated_at`,
            NEW.`updated_at`
        );
    END IF;
END;
