<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTaskTriggers extends Migration
{
    public function up()
    {

        DB::unprepared("
        DROP TRIGGER IF EXISTS after_task_collaborator_insert;
        CREATE TRIGGER after_task_collaborator_insert
        AFTER INSERT ON task_collaborators
        FOR EACH ROW
        BEGIN
            DECLARE previous_collaborator_count INT;

            -- Get the count of collaborators for the taskId
            SELECT SUM(q.count) INTO  previous_collaborator_count FROM (SELECT COUNT(DISTINCT created_at) as count
                FROM task_collaborators
                WHERE taskId = NEW.taskId
                GROUP BY created_at) as q;

            IF previous_collaborator_count > 1  THEN
                INSERT INTO task_activity_logs (taskId, action, property, newId, created_at, updated_at, createdBy)
                VALUES (NEW.taskId, '0', if(NEW.flag = '0' ,'0','1'), NEW.collaborator, NOW(), NOW(), NEW.updatedBy);
            END IF;
        END
    ");
        DB::unprepared("
            DROP TRIGGER IF EXISTS after_task_collaborator_delete;
            CREATE TRIGGER after_task_collaborator_delete
            AFTER DELETE ON task_collaborators
            FOR EACH ROW
            BEGIN
                INSERT INTO task_activity_logs (taskId, action, property, oldId, created_at, updated_at,createdBy )
                VALUES (OLD.taskId, '1', IF(OLD.flag = '0', '0', '1'), OLD.collaborator, NOW(), NOW(),OLD.updatedBy);

            END
        ");

        DB::unprepared("
            DROP TRIGGER IF EXISTS after_task_types_insert;
            CREATE TRIGGER after_task_types_insert
            AFTER INSERT ON  task_taskTypes_pivot
            FOR EACH ROW
            BEGIN
                DECLARE tasktype_count INT;

                -- Get the count of tasktypes for the taskId
                SELECT SUM(q.count) INTO tasktype_count FROM (SELECT COUNT(DISTINCT created_at) as count
                FROM task_taskTypes_pivot
                WHERE task_id = NEW.task_id
                GROUP BY created_at) as q;

                IF tasktype_count > 1  THEN
                    INSERT INTO task_activity_logs (taskId, action, property, newId, created_at, updated_at, createdBy)
                    VALUES (NEW.task_id, '0', '4', NEW.taskTypes_id, NOW(), NOW(),  NEW.updatedBy);
                END IF;
            END
        ");
        DB::unprepared("
            DROP TRIGGER IF EXISTS after_task_types_delete;
            CREATE TRIGGER after_task_types_delete
            AFTER DELETE ON task_taskTypes_pivot
            FOR EACH ROW
            BEGIN
                INSERT INTO task_activity_logs (taskId, action, property, oldId, created_at, updated_at,createdBy )
                VALUES (OLD.task_id, '1', '4', OLD.taskTypes_id, NOW(), NOW(), OLD.updatedBy);

            END
        ");

        DB::unprepared('
            DROP TRIGGER IF EXISTS log_task_update_after;
            CREATE TRIGGER log_task_update_after
            AFTER UPDATE ON tasks
            FOR EACH ROW
            BEGIN
                IF OLD.deadline <> NEW.deadline THEN
                    INSERT INTO task_activity_logs (createdBy, taskId, action, property, oldValue, newValue, created_at, updated_at)
                    VALUES (NEW.updatedBy, OLD.id, "2", "2", OLD.deadline, NEW.deadline, NOW(),  NOW());
                END IF;

                IF OLD.priority <> NEW.priority THEN
                    INSERT INTO task_activity_logs (createdBy, taskId, action, property, oldId, newId, created_at, updated_at)
                    VALUES (NEW.updatedBy, OLD.id, "2", "3", OLD.priority, NEW.priority,  NOW(),  NOW());
                END IF;
            END;
        ');
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS log_task_update_after');
        DB::unprepared('DROP TRIGGER IF EXISTS after_task_collaborator_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS after_task_collaborator_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS after_task_types_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS after_task_types_delete');
    }
}
